<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\ActualPayment;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function index()
    {
        $category = Category::where('slug', 'via-sell')->first();
        $query = Product::orderBy('stock', 'asc');

        if (Auth::user()->id != 1) {
            $query->where('branch_id', Auth::user()->branch_id);
        }

        if ($category) {
            $query->where('category_id', '!=', $category->id);
        }

        $products = $query->get();

        return view('pos.purchase.purchase', compact('products'));
    }

    // store function 
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'date' => 'required',
            // 'products' => 'required',
            'payment_method' => 'required',
            'document' => 'file|mimes:jpg,pdf,png,svg,webp,jpeg,gif|max:5120'
        ]);
        if ($validator->passes()) {
            $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
            // dd($oldBalance);
            if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->total_payable ?? 0) {
                $totalQty = 0;
                $totalAmount = 0;
                // Assuming all arrays have the same length
                $arrayLength = count($request->product_name);
                for ($i = 0; $i < $arrayLength; $i++) {
                    $totalQty += $request->quantity[$i];
                    $totalAmount += ($request->unit_price[$i] * $request->quantity[$i]);
                }
                $purchaseDate = Carbon::createFromFormat('d-M-Y', $request->date)->format('Y-m-d');

                // purchase table Crud
                $purchase = new Purchase;
                $purchase->branch_id = Auth::user()->branch_id;
                $purchase->supplier_id = $request->supplier_id;
                $purchase->purchase_date =  $purchaseDate;
                $purchase->purchase_by =  Auth::user()->id;
                $purchase->total_quantity =  $totalQty;
                $purchase->total_amount =  $totalAmount;
                if ($request->invoice) {
                    $purchase->invoice = $request->invoice;
                } else {
                    do {
                        $invoice = rand(123456, 999999); // Generate a random number
                        $existingInvoice = Purchase::where('invoice', $invoice)->first(); // Check if the random invoice exists
                    } while ($existingInvoice); // Keep generating until a unique invoice number is found
                    $purchase->invoice = $invoice;
                }
                $purchase->discount_amount = $request->discount_amount;
                if ($request->carrying_cost > 0) {
                    $purchase->sub_total = $request->sub_total - $request->carrying_cost;
                    $purchase->grand_total = $request->grand_total - $request->carrying_cost;
                    $purchase->paid = $request->total_payable - $request->carrying_cost;
                    $due = ($request->grand_total - $request->carrying_cost) - ($request->total_payable - $request->carrying_cost);
                } else {
                    $purchase->sub_total = $request->sub_total;
                    $purchase->grand_total = $request->grand_total;
                    $purchase->paid = $request->total_payable;
                    $due = $request->grand_total - $request->total_payable;
                }
                if ($due > 0) {
                    $purchase->due = $due;
                } else {
                    $purchase->due = 0;
                }
                $purchase->carrying_cost = $request->carrying_cost;
                $purchase->payment_method = $request->payment_method;
                $purchase->note = $request->note;
                if ($request->document) {
                    $docName = rand() . '.' . $request->document->getClientOriginalExtension();
                    $request->document->move(public_path('uploads/purchase/'), $docName);
                    $purchase->document = $docName;
                }
                $purchase->save();

                // get purchaseId
                $purchaseId = $purchase->id;

                for ($i = 0; $i < $arrayLength; $i++) {
                    $items = new PurchaseItem;
                    $items->purchase_id = $purchaseId;
                    $items->product_id = $request->product_id[$i];
                    $items->unit_price = $request->unit_price[$i];
                    $items->quantity = $request->quantity[$i];
                    $items->total_price = $request->unit_price[$i] * $request->quantity[$i];
                    $items->save();


                    $product = Product::findOrFail($request->product_id[$i]);
                    $product->stock += $request->quantity[$i];
                    $product->cost = $request->unit_price[$i];
                    $product->price = $request->sell_price[$i];
                    $product->save();
                }
                // product Carrying Cost carrying cost
                // actual payment CRUD
                $actualPayment = new ActualPayment;
                $actualPayment->branch_id =  Auth::user()->branch_id;
                $actualPayment->payment_type =  'pay';
                $actualPayment->payment_method =  $request->payment_method;
                $actualPayment->supplier_id = $request->supplier_id;
                $actualPayment->amount = $request->total_payable ?? 0;
                $actualPayment->date =  $purchaseDate;
                $actualPayment->save();

                // Account Transaction Crud //

                // Account Transaction 
                $accountTransaction = new AccountTransaction;
                $accountTransaction->branch_id =  Auth::user()->branch_id;
                $accountTransaction->reference_id = $purchaseId;
                $accountTransaction->account_id =  $request->payment_method;
                $accountTransaction->purpose =  'Purchase';
                $accountTransaction->debit = $request->total_payable ?? 0;
                $accountTransaction->balance = $oldBalance->balance - $request->total_payable ?? 0;
                $accountTransaction->created_at = Carbon::now();
                $accountTransaction->save();


                if ($request->carrying_cost > 0) {
                    $expense = new Expense;
                    $expense->branch_id = Auth::user()->branch_id;
                    $expense->purpose =  'Purchase' .  $purchaseId;
                    $expense->expense_date = now()->toDateString();
                    $expense->amount = $request->carrying_cost;
                    $expense->spender = Auth::user()->name;
                    $expense->bank_account_id = $request->payment_method;
                    $expense->save();
                }


                // get Transaction Model
                $transaction = new Transaction;
                $transaction->branch_id = Auth::user()->branch_id;
                $transaction->date =   $purchaseDate;
                $transaction->processed_by =  Auth::user()->id;
                $transaction->payment_type = 'pay';
                $transaction->particulars = 'Purchase#' . $purchaseId;
                $transaction->supplier_id = $request->supplier_id;
                $transaction->payment_method = $request->payment_method;
                if ($request->carrying_cost > 0) {
                    $transaction->credit = $request->total_payable - $request->carrying_cost;
                    $transaction->debit = $request->sub_total - $request->carrying_cost;
                } else {
                    $transaction->credit = $request->total_payable;
                    $transaction->debit = $request->sub_total;
                }
                $transaction->balance = $request->sub_total - $request->total_payable;
                $transaction->save();

                // Supplier Crud
                $supplier = Supplier::findOrFail($request->supplier_id);
                $payWithoutCarryingCost = $request->total_payable - $request->carrying_cost;
                $totalWithoutCarryingCost = $request->sub_total - $request->carrying_cost;
                if ($request->carrying_cost > 0) {
                    $supplier->total_receivable += $totalWithoutCarryingCost;
                    $supplier->total_payable += $payWithoutCarryingCost;
                    $supplier->wallet_balance += ($totalWithoutCarryingCost - $payWithoutCarryingCost);
                } else {
                    $supplier->total_receivable += $request->sub_total;
                    $supplier->total_payable += $request->total_payable;
                    $supplier->wallet_balance += ($request->sub_total - $request->total_payable);
                }
                $supplier->save();


                return response()->json([
                    'status' => 200,
                    'purchaseId' => $purchase->id,
                    'message' => 'successfully save',
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Not Enough Balance in this Account. Please choose Another Account or Deposit Account Balance.',
                ]);
            }
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    }

    // invoice function 
    public function invoice($id)
    {
        $purchase = Purchase::findOrFail($id);
        $branch = Branch::findOrFail($purchase->branch_id);
        $supplier = Supplier::findOrFail($purchase->supplier_id);
        $products = PurchaseItem::where('purchase_id', $purchase->id)->get();
        if ($purchase->purchase_by) {
            $authName = User::findOrFail($purchase->purchase_by)->name;
        } else {
            $authName = "";
        }

        return view('pos.purchase.invoice', compact('purchase', 'branch', 'supplier', 'products', 'authName'));
    }

    // Money Receipt
    public function moneyReceipt($id)
    {
        $purchase = Purchase::findOrFail($id);
        return view('pos.purchase.receipt', compact('purchase'));
    }

    // view Function 
    public function view()
    {
        if (Auth::user()->id == 1) {
            $purchase = Purchase::latest()->get();
        } else {
            $purchase = Purchase::where('branch_id', Auth::user()->branch_id)->latest()->get();
        }

        // return view('pos.purchase.view');
        return view('pos.purchase.view', compact('purchase'));
    }

    // supplierName function 
    public function supplierName($id)
    {
        $supplier = Supplier::findOrFail($id);
        return response()->json([
            'status' => 200,
            'supplier' => $supplier
        ]);
    }

    // edit function 
    public function edit($id)
    {
        $purchase = Purchase::findOrFail($id);
        $branch = Branch::findOrFail($purchase->branch_id)->first();
        $selectedSupplier = Supplier::findOrFail($purchase->supplier_id)->first();
        $suppliers = Supplier::get();
        if (Auth::user()->id == 1) {
            $products = Product::orderBy('stock', 'asc')->get();
        } else {
            $products = Product::where('branch_id', Auth::user()->branch_id)
                ->orderBy('stock', 'asc')
                ->get();
        }
        return view('pos.purchase.edit', compact('purchase', 'branch', 'selectedSupplier', 'suppliers', 'products'));
    }

    // update function 
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required',
            'date' => 'required',
            'total_payable' => 'required',
            'payment_method' => 'required',
            'document' => 'file|mimes:jpg,pdf,png,svg,webp,jpeg,gif|max:5120'
        ]);

        if ($validator->passes()) {
            $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
            if ($oldBalance != null) {
                if ($oldBalance->balance > 0 && $oldBalance->balance >= $request->total_payable) {
                    $totalQty = 0;
                    $totalAmount = 0;
                    // Assuming all arrays have the same length
                    $arrayLength = count($request->product_name);
                    for ($i = 0; $i < $arrayLength; $i++) {
                        $totalQty += $request->quantity[$i];
                        $totalAmount += ($request->unit_price[$i] * $request->quantity[$i]);
                    }
                    $purchaseDate = Carbon::createFromFormat('d-M-Y', $request->date)->format('Y-m-d');

                    // dd($totalAmount);
                    $purchase = Purchase::findOrFail($id);
                    // dd($purchase);


                    // purchase Item 
                    $existingItems = PurchaseItem::where('purchase_id', $id)->pluck('id')->toArray();
                    $requestItems = array_filter($request->purchaseItemId);
                    $itemsToDelete = array_diff($existingItems, $requestItems);
                    PurchaseItem::whereIn('id', $itemsToDelete)->delete();
                    for ($i = 0; $i < $arrayLength; $i++) {
                        if ($request->purchaseItemId[$i] != null) {
                            // Update existing item.
                            $items = PurchaseItem::findOrFail($request->purchaseItemId[$i]);
                            $items->purchase_id = $id;
                            $items->product_id = $request->product_id[$i];
                            $items->unit_price = $request->unit_price[$i];
                            $items->quantity = $request->quantity[$i];
                            $items->total_price = $request->unit_price[$i] * $request->quantity[$i];
                            $items->save();

                            $product = Product::findOrFail($request->product_id[$i]);
                            $product->stock += $request->quantity[$i];
                            $product->save();
                        } else {
                            // Create new item.
                            $items = new PurchaseItem;
                            $items->purchase_id = $id;
                            $items->product_id = $request->product_id[$i];
                            $items->unit_price = $request->unit_price[$i];
                            $items->quantity = $request->quantity[$i];
                            $items->total_price = $request->unit_price[$i] * $request->quantity[$i];
                            $items->save();

                            $product = Product::findOrFail($request->product_id[$i]);
                            $product->stock += $request->quantity[$i];
                            $product->save();
                        }
                    }


                    // actual payment CRUD
                    $actualPayment = new ActualPayment;
                    $actualPayment->branch_id =  Auth::user()->branch_id;
                    $actualPayment->payment_type =  'pay';
                    $actualPayment->payment_method =  $request->payment_method;
                    $actualPayment->supplier_id = $request->supplier_id;
                    $actualPayment->amount = $request->total_payable;
                    $actualPayment->date =  $purchaseDate;
                    $actualPayment->save();

                    // Account Transaction 
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->branch_id =  Auth::user()->branch_id;
                    $accountTransaction->reference_id = $id;
                    $accountTransaction->account_id =  $request->payment_method;
                    $accountTransaction->purpose =  'Purchase Edit';
                    if ($purchase->paid > $request->total_payable) {
                        $accountTransaction->credit = ($purchase->paid - $request->total_payable) ?? 0;
                        $accountTransaction->balance = $oldBalance->balance + ($purchase->paid - $request->total_payable) ?? 0;
                    } else {
                        $accountTransaction->debit = ($request->total_payable - $purchase->paid) ?? 0;
                        $accountTransaction->balance = $oldBalance->balance - ($request->total_payable - $purchase->paid) ?? 0;
                    }
                    $accountTransaction->created_at = Carbon::now();
                    $accountTransaction->save();

                    if ($request->carrying_cost > 0) {
                        $expense = Expense::where('purpose', 'Purchase' . $id)->first();
                        $expense->branch_id = Auth::user()->branch_id;
                        $expense->expense_date = now()->toDateString();
                        $expense->amount = $request->carrying_cost;
                        $expense->spender = Auth::user()->name;
                        $expense->bank_account_id = $request->payment_method;
                        $expense->save();
                    }

                    // get Transaction Model
                    $transaction = Transaction::where('particulars', 'Purchase#' . $id)->first();
                    $transaction->branch_id = Auth::user()->branch_id;
                    $transaction->date =   $purchaseDate;
                    $transaction->processed_by =  Auth::user()->id;
                    $transaction->payment_type = 'pay';
                    $transaction->particulars = 'Purchase#' . $id;
                    $transaction->supplier_id = $request->supplier_id;
                    $transaction->payment_method = $request->payment_method;
                    $transaction->debit = $request->total_payable;
                    $transaction->credit = $request->sub_total;
                    $transaction->balance = $request->total_payable - $request->sub_total;
                    $transaction->save();

                    $supplier = Supplier::findOrFail($request->supplier_id);
                    // Calculate payable amounts
                    $payWithoutCarryingCost = $request->carrying_cost > 0 ? $request->total_payable - $request->carrying_cost : $request->total_payable;
                    $totalWithoutCarryingCost = $request->carrying_cost > 0 ? $request->sub_total - $request->carrying_cost : $request->sub_total;

                    // Update supplier's total receivable and payable
                    $supplier->total_receivable += $totalWithoutCarryingCost;
                    $supplier->total_payable += $payWithoutCarryingCost;

                    // Adjust wallet balance based on the purchase's due amount
                    $walletAdjustment = $totalWithoutCarryingCost - $payWithoutCarryingCost;
                    if ($purchase->due > 0) {
                        $supplier->wallet_balance = ($supplier->wallet_balance - $purchase->due) + $walletAdjustment;
                    } else {
                        $supplier->wallet_balance += $walletAdjustment;
                    }

                    // Save the supplier's updated data
                    $supplier->save();



                    // purchase table Crud 
                    $purchase->branch_id = Auth::user()->branch_id;
                    $purchase->supplier_id = $request->supplier_id;
                    $purchase->purchase_date =  $purchaseDate;
                    $purchase->total_quantity =  $totalQty;
                    $purchase->total_amount =  $totalAmount;
                    if ($request->invoice) {
                        $purchase->invoice = $request->invoice;
                    } else {
                        do {
                            $invoice = rand(123456, 999999); // Generate a random number
                            $existingInvoice = Purchase::where('invoice', $invoice)->first(); // Check if the random invoice exists
                        } while ($existingInvoice); // Keep generating until a unique invoice number is found
                        $purchase->invoice = $invoice;
                    }
                    if ($request->carrying_cost > 0) {
                        $purchase->sub_total = $request->sub_total - $request->carrying_cost;
                        $purchase->grand_total = $request->sub_total - $request->carrying_cost;
                    } else {
                        $purchase->sub_total = $request->sub_total;
                        $purchase->grand_total = $request->grand_total;
                    }
                    $purchase->paid = $request->total_payable;
                    $due = $request->grand_total - $request->total_payable;
                    if ($due > 0) {
                        $purchase->due = $due;
                    } else {
                        $purchase->due = 0;
                    }
                    $purchase->carrying_cost = $request->carrying_cost;
                    $purchase->payment_method = $request->payment_method;
                    $purchase->note = $request->note;
                    if ($request->hasFile('document')) {
                        $extension = $request->document->getClientOriginalExtension();
                        $docName = rand() . '.' . $extension;
                        $request->document->move(public_path('uploads/purchase/'), $docName);
                        $purchase->document = $docName;
                    }
                    $purchase->save();

                    return response()->json([
                        'status' => 200,
                        'purchaseId' => $purchase->id,
                        'message' => 'successfully save',
                    ]);
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Not Enough Balance in this Account. Please choose Another Account',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Please Add Balance to Account or Deposit Account Balance',
                ]);
            }
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    }


    // destroy function 
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        $oldBalance = AccountTransaction::where('account_id', $purchase->payment_method)->latest('created_at')->first();
        // Account Transaction 
        $accountTransaction = new AccountTransaction;
        $accountTransaction->branch_id =  Auth::user()->branch_id;
        $accountTransaction->reference_id = $id;
        $accountTransaction->account_id =  $purchase->payment_method;
        $accountTransaction->purpose =  'Purchase Delete';
        $accountTransaction->credit =  $purchase->paid ?? 0;
        $accountTransaction->balance = $oldBalance->balance + $purchase->paid ?? 0;
        $accountTransaction->created_at = Carbon::now();
        $accountTransaction->save();

        if ($purchase->carrying_cost) {
            $expense = Expense::where('purpose', 'Purchase' . $id)->first();
            if ($expense) {
                $expense->delete();
            }
        }

        $transaction = Transaction::where('particulars', 'Purchase#' . $id)->first();
        $transaction->delete();

        if ($purchase->document) {
            $previousDocumentPath = public_path('uploads/purchase/') . $purchase->document;
            if (file_exists($previousDocumentPath)) {
                unlink($previousDocumentPath);
            }
        }
        $purchase->delete();
        return back()->with('message', "Purchase successfully Deleted");
    }

    // filter function 
    public function filter(Request $request)
    {
        // dd($request->all());
        $purchaseQuery = Purchase::query();

        // Filter by product_id if provided
        if ($request->product_id != "Select Product") {
            $purchaseQuery->whereHas('purchaseItem', function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            });
        }
        // Filter by supplier_id if provided
        if ($request->supplier_id != "Select Supplier") {
            $purchaseQuery->where('supplier_id', $request->supplier_id);
        }

        // Filter by date range if both start_date and end_date are provided
        if ($request->start_date && $request->end_date) {
            $purchaseQuery->whereBetween('purchase_date', [$request->start_date, $request->end_date]);
        }

        // Execute the query
        $purchase = $purchaseQuery->get();

        return view('pos.purchase.table', compact('purchase'))->render();
    }

    // purchaseItem Function 
    public function purchaseItem($id)
    {
        // Fetch PurchaseItem records with associated Product using eager loading
        $purchaseItems = PurchaseItem::where('purchase_id', $id)
            ->with(['product.unit']) // Eager load both product and its unit
            ->get();

        if ($purchaseItems->isNotEmpty()) {
            // If data exists, return the purchase items with product details
            return response()->json([
                'status' => 200,
                'purchaseItems' => $purchaseItems
            ]);
        } else {
            // If no data found, return an error response
            return response()->json([
                'status' => 500,
                'message' => 'Data Not Found'
            ]);
        }
    }


    // get supplier details 
    public function getSupplierDetails($id)
    {
        $supplier = Supplier::findOrFail($id);
        return response()->json(['data' => $supplier], 200);
    }

    // image to PDF 
    public function imageToPdf($id)
    {
        $purchase = Purchase::findOrFail($id);
        $documentPath = public_path('uploads/purchase/' . $purchase->document);
        // Define the data to pass to the PDF generation
        $data = [
            'imagePath' => $documentPath,  // Pass the moved document path
            'title' => "$purchase->document"
        ];

        $pdf = PDF::loadView('pdf.document', $data);
        // dd($pdf);

        // Return the generated PDF for download or streaming
        return response()->json([
            "status" => 200,
            "data" => $pdf
        ]);
    }
}
