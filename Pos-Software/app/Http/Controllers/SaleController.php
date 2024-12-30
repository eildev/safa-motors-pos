<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\ActualPayment;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\PromotionDetails;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SubCategory;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use App\Models\ViaProduct;
use App\Models\ViaSale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
// use Validator;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function index()
    {
        // $withoutPurchase = Product::whereDoesntHave('purchaseItems')->get();
        return view('pos.sale.sale');
    }
    public function getCustomer()
    {
        $data = Customer::where('branch_id', Auth::user()->branch_id)->latest()->get();
        return response()->json([
            'status' => 200,
            'message' => 'successfully save',
            'allData' => $data
        ]);
    }
    public function addCustomer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
        ]);

        if ($validator->passes()) {
            $customer = new Customer;
            $customer->branch_id = Auth::user()->branch_id;
            $customer->name = $request->name;
            $customer->phone = $request->phone;
            $customer->email = $request->email;
            $customer->address = $request->address;
            $customer->opening_payable = $request->wallet_balance ?? 0;
            $customer->wallet_balance = $request->wallet_balance ?? 0;
            $customer->total_receivable = $request->wallet_balance ?? 0;
            $customer->created_at = Carbon::now();
            $customer->save();

            if ($request->wallet_balance > 0) {
                $transaction = new Transaction;
                $transaction->branch_id = Auth::user()->branch_id;
                $transaction->date = Carbon::now();
                $transaction->processed_by =  Auth::user()->id;
                $transaction->payment_type = 'receive';
                $transaction->particulars = 'Opening Due';
                $transaction->customer_id = $customer->id;
                $transaction->credit = 0;
                $transaction->debit = $request->wallet_balance;
                $transaction->balance = $request->wallet_balance ?? 0;
                $transaction->save();
            }


            return response()->json([
                'status' => 200,
                'message' => 'Successfully Save',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    }

    public function store(Request $request)
    {
        // dd($request->paid);

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|numeric',
            'sale_date' => 'required',
            'payment_method' => 'required',
            'paid' => 'required',
            'products' => 'required',
        ], [
            'customer_id.numeric' => 'Please Add Customer.',
        ]);

        if ($validator->passes()) {
            // product Cost//
            $productCost = 0;
            $productAll = $request->products;
            foreach ($productAll as $product) {
                $items = Product::findOrFail($product['product_id']);
                $total = $items->cost * $product['quantity'];
                $productCost += $total;
            }
            // Sale Table CRUD
            $sale = new Sale;
            $sale->branch_id = Auth::user()->branch_id;
            $sale->customer_id = $request->customer_id;
            $sale->sale_date = $request->sale_date;
            $sale->sale_by = Auth::user()->id;
            $sale->invoice_number = $request->invoice_number;
            $sale->order_type = "general";
            $sale->quantity = $request->quantity;
            $sale->total = $request->total_amount;
            // $sale->discount = $request->discount;
            $sale->change_amount = $request->total;
            $sale->actual_discount = $request->actual_discount;
            $sale->tax = $request->tax;
            $sale->receivable = $request->change_amount;
            // $sale->paid = $request->paid;
            $sale->due = $request->due;
            if ($request->due < 0) {
                $sale->paid = $request->paid + $request->due;
            } else {
                $sale->paid = $request->paid;
            }
            // $sale->returned = $request->due;
            $sale->final_receivable = $request->change_amount;
            $sale->payment_method = $request->payment_method;
            $totalSell = $request->total_amount - $request->actual_discount;
            $sale->profit = $totalSell - $productCost;
            $sale->note = $request->note;
            $sale->created_at = Carbon::now();
            $sale->save();

            $saleId = $sale->id;
            $selectedItems = $request->products;

            $category = Category::where('name', 'Via Sell')->first();
            foreach ($selectedItems as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Determine if this should be a "via sell" or "normal sell"
                $isViaSell = false;

                // Check if product is in the "Via Sell" category or stock is 0 or less
                if ($category && $product->category_id == $category->id || $product->stock <= 0) {
                    $isViaSell = true;
                }
                // Handle the sale item
                if ($isViaSell) {
                    $saleItem = new SaleItem;
                    $saleItem->sale_id = $saleId;
                    $saleItem->product_id = $item['product_id'];
                    $saleItem->rate = $item['unit_price'];
                    $saleItem->qty = $item['quantity'];
                    $saleItem->wa_status = $item['wa_status'];
                    $saleItem->wa_duration = $item['wa_duration'];
                    $saleItem->discount = $item['product_discount'];
                    $saleItem->sub_total = $item['total_price'];
                    $saleItem->total_purchase_cost = $product->cost * $item['quantity'];
                    $saleItem->total_profit = $item['total_price'] - ($product->cost * $item['quantity']);
                    $saleItem->sell_type = 'via sell';
                    $saleItem->save();
                } else if ($product->stock < $item['quantity']) {
                    $extraQuantity = $item['quantity'] - $product->stock;
                    // If the stock is greater than 0, first handle the available stock as a "normal sell"
                    if ($product->stock > 0) {
                        $saleItem = new SaleItem;
                        $saleItem->sale_id = $saleId;
                        $saleItem->product_id = $item['product_id'];
                        $saleItem->rate = $item['unit_price'];
                        $saleItem->qty = $product->stock;
                        $saleItem->wa_status = $item['wa_status'];
                        $saleItem->wa_duration = $item['wa_duration'];
                        $discount = $item['product_discount'] / 2;
                        $saleItem->discount = $discount;
                        $subTotal = ($item['unit_price'] * $product->stock) - $discount;
                        $saleItem->sub_total = $subTotal;
                        $saleItem->total_purchase_cost = $product->cost * $product->stock;
                        $saleItem->total_profit = $subTotal - ($product->cost * $product->stock);
                        $saleItem->sell_type = 'normal sell';
                        $saleItem->save();
                    }

                    // Handle the extra quantity as a "via sell"
                    if ($extraQuantity > 0) {
                        $extraItem = new SaleItem;
                        $extraItem->sale_id = $saleId;
                        $extraItem->product_id = $item['product_id'];
                        $extraItem->rate = $item['unit_price'];
                        $extraItem->qty = $extraQuantity;
                        $extraItem->wa_status = $item['wa_status'];
                        $extraItem->wa_duration = $item['wa_duration'];
                        $discount = $item['product_discount'] / 2;
                        $extraItem->discount = $discount;
                        $subTotal2 = ($item['unit_price'] * $extraQuantity) - $discount;
                        $extraItem->sub_total = $subTotal2;
                        $extraItem->total_purchase_cost = $product->cost * $extraQuantity;
                        $extraItem->total_profit = $subTotal2 - ($product->cost * $extraQuantity);
                        $extraItem->sell_type = 'via sell';
                        $extraItem->save();
                    }
                } else {
                    // Handle a normal sell for items within stock
                    $saleItem = new SaleItem;
                    $saleItem->sale_id = $saleId;
                    $saleItem->product_id = $item['product_id'];
                    $saleItem->rate = $item['unit_price'];
                    $saleItem->qty = $item['quantity'];
                    $saleItem->wa_status = $item['wa_status'];
                    $saleItem->wa_duration = $item['wa_duration'];
                    $saleItem->discount = $item['product_discount'];
                    $saleItem->sub_total = $item['total_price'];
                    $saleItem->total_purchase_cost = $product->cost * $item['quantity'];
                    $saleItem->total_profit = $item['total_price'] - ($product->cost * $item['quantity']);
                    $saleItem->sell_type = 'normal sell';
                    $saleItem->save();
                }

                // Update product stock and sales information
                if ($product->stock > 0 && $product->stock >= $item['quantity']) {
                    $product->stock -= $item['quantity'];
                } else {
                    $product->stock = 0;
                }
                $product->total_sold += $item['quantity'];
                $product->save();
            }


            // via sale
            $viaSaleItems = SaleItem::where('sale_id', $saleId)->Where('sell_type', 'via sell')->get();
            $viaSales = ViaSale::where('invoice_number', $request->invoice_number)->first();
            // dd($viaSaleItems->count());
            foreach ($viaSaleItems as $viaItem) {
                // dd($viaItem->total_purchase_cost / $viaItem->qty);
                if ($viaSales == null) {
                    $viaSale = new ViaSale;
                    $viaSale->invoice_date = Carbon::now();
                    $viaSale->branch_id =  Auth::user()->branch_id;
                    $viaSale->invoice_number = $request->invoice_number;
                    $viaSale->processed_by = Auth::user()->id;
                    $viaSale->supplier_name = 'Direct Sales';
                    $viaSale->product_id = $viaItem->product_id;
                    $viaSale->product_name = $viaItem->product->name;
                    $viaSale->quantity = $viaItem->qty;
                    $costPrice = $viaItem->total_purchase_cost / $viaItem->qty;
                    $viaSale->cost_price = $costPrice;
                    $viaSale->sale_price = $viaItem->rate;
                    $viaSale->sub_total = $viaItem->sub_total;
                    $viaTotalPay = $costPrice * $viaItem->qty;
                    $viaSale->paid = 0;
                    $viaSale->due = $viaTotalPay;
                    $viaSale->status  = 0;
                    $viaSale->save();
                }
            }

            // customer table CRUD
            $customer = Customer::findOrFail($request->customer_id);
            // dd($request->total - $request->paid);
            $customer->total_receivable = $customer->total_receivable + $request->total;
            $customer->total_payable = $customer->total_payable + $request->paid;
            $customer->wallet_balance = $customer->wallet_balance + ($request->total - $request->paid);
            $customer->save();

            // actual Payment
            $actualPayment = new ActualPayment;
            $actualPayment->branch_id =  Auth::user()->branch_id;
            $actualPayment->payment_type =  'receive';
            $actualPayment->payment_method =  $request->payment_method;
            $actualPayment->customer_id = $request->customer_id;
            $actualPayment->amount = $request->paid;
            $actualPayment->date = $request->sale_date;
            $actualPayment->save();

            // accountTransaction table
            $accountTransaction = new AccountTransaction;
            $accountTransaction->branch_id =  Auth::user()->branch_id;
            $accountTransaction->purpose =  'Sale';
            $accountTransaction->reference_id = $saleId;
            $accountTransaction->account_id =  $request->payment_method;
            $accountTransaction->credit = $request->paid;
            $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
            if ($oldBalance) {
                $accountTransaction->balance = $oldBalance->balance + $request->paid;
            } else {
                $accountTransaction->balance = $request->paid;
            }
            $accountTransaction->created_at = Carbon::now();
            $accountTransaction->save();

            $transaction = new Transaction;
            $transaction->date =  $request->sale_date;
            $transaction->processed_by =  Auth::user()->id;
            $transaction->payment_type = 'receive';
            $transaction->particulars = 'Sale#' . $saleId;
            $transaction->customer_id = $request->customer_id;
            $transaction->payment_method = $request->payment_method;
            $transaction->credit = $request->paid;
            $transaction->debit = $request->total;
            $transaction->balance = $request->total - $request->paid;
            $transaction->branch_id =  Auth::user()->branch_id;
            $transaction->save();

            return response()->json([
                'status' => 200,
                'saleId' => $saleId,
                'message' => 'successfully save',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages(),
            ]);
        }
    }
    public function invoice($id)
    {
        $sale = Sale::findOrFail($id);

        $branch = Branch::findOrFail($sale->branch_id);
        $customer = Customer::findOrFail($sale->customer_id);
        $products = SaleItem::where('sale_id', $sale->id)->get();

        if ($sale->sale_by) {
            $authName = User::findOrFail($sale->sale_by)->name;
        } else {
            $authName = "";
        }
        // $authName = User::findOrFail($sale->sale_by);
        return view('pos.sale.invoice', compact('sale', 'customer', 'products', 'authName'));
    }
    public function print($id)
    {
        $sale = Sale::findOrFail($id);
        return view('pos.sale.pos-print', compact('sale'));
    }

    public function view()
    {
        if (Auth::user()->id == 1) {
            $sales = Sale::latest()->get();
        } else {
            $sales = Sale::where('branch_id', Auth::user()->branch_id)->latest()->get();
        }
        // $sales = Sale::where('branch_id', Auth::user()->branch_id)->latest()->get();
        return view('pos.sale.view', compact('sales'));
    }
    // public function viewAll()
    // {
    //     $sales = Sale::where('branch_id', Auth::user()->branch_id)->get();
    //     return response()->json([
    //         'status' => 200,
    //         'allData' => $sales,
    //     ]);
    // }
    public function viewDetails($id)
    {
        $sale = Sale::findOrFail($id);
        return view('pos.sale.show', compact('sale'));
    }

    public function edit($id)
    {
        $sale = Sale::findOrFail($id);
        return view('pos.sale.edit', compact('sale'));
    }
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required',
            'products' => 'required',
            'sale_date' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->passes()) {

            // product Cost
            $productCost = 0;
            $productAll = $request->products;
            foreach ($productAll as $product) {
                $items = Product::findOrFail($product['product_id']);
                $productCost += $items->cost;
            }

            // Sale Table CRUD
            $sale = Sale::findOrFail($id);
            $sale->branch_id = Auth::user()->branch_id;
            $sale->customer_id = $request->customer_id;
            $sale->sale_date = $request->sale_date;
            $sale->sale_by = 0;
            $sale->invoice_number = rand(123456, 99999);
            $sale->order_type = "general";
            $sale->quantity = $request->quantity;
            $sale->total = $request->total_amount;
            $sale->discount = $request->discount;
            $sale->change_amount = $request->total;
            $sale->actual_discount = $request->actual_discount;
            $sale->tax = $request->tax;
            $sale->receivable = $request->change_amount;
            // $sale->paid = $request->paid;
            $sale->due = $request->due;
            if ($request->due < 0) {
                $sale->paid = $request->paid + $request->due;
            } else {
                $sale->paid = $request->paid;
            }
            // $sale->returned = $request->due;
            $sale->final_receivable = $request->change_amount;
            $sale->payment_method = $request->payment_method;
            $sale->profit = $request->change_amount - $productCost;
            $sale->note = $request->note;
            $sale->created_at = Carbon::now();
            $sale->save();


            // $saleId = $sale->id;

            // products table CRUD
            $products = $request->products;
            foreach ($products as $product) {
                $items2 = Product::findOrFail($product['product_id']);
                $items = new SaleItem;
                $items->sale_id = $sale->id;
                $items->product_id = $product['product_id']; // Access 'product_id' as an array key
                $items->rate = $product['unit_price']; // Access 'unit_price' as an array key
                $items->qty = $product['quantity'];
                $items->discount = $product['discount'];
                $items->sub_total = $product['total_price'];
                $items->total_purchase_cost = $items2->cost * $product['quantity'];
                $items->save();


                $items2->stock = $items2->stock - $product['quantity'];
                $items2->total_sold = $items2->total_sold + $product['quantity'];
                $items2->save();
            }

            // customer table CRUD
            $customer = Customer::findOrFail($request->customer_id);
            $customer->total_receivable = $customer->total_receivable + $request->change_amount;
            $customer->total_payable = $customer->total_payable + $request->paid;
            $customer->wallet_balance = $customer->wallet_balance + ($request->change_amount - $request->paid);
            $customer->save();

            // actual Payment
            $actualPayment = new ActualPayment;
            $actualPayment->branch_id =  Auth::user()->branch_id;
            $actualPayment->payment_type =  'receive';
            $actualPayment->payment_method =  $request->payment_method;
            $actualPayment->customer_id = $request->customer_id;
            $actualPayment->amount = $request->paid;
            $actualPayment->date = $request->sale_date;
            $actualPayment->save();

            // accountTransaction table
            $accountTransaction = new AccountTransaction;
            $accountTransaction->branch_id =  Auth::user()->branch_id;
            $accountTransaction->reference_id = $sale->id;
            $accountTransaction->purpose =  'Sale';
            $accountTransaction->account_id =  $request->payment_method;
            $accountTransaction->credit = $request->paid;
            $oldBalance = AccountTransaction::where('account_id', $request->bank_account_id)->latest('created_at')->first();
            $accountTransaction->balance = $oldBalance->balance + $request->paid;
            $accountTransaction->created_at = Carbon::now();
            $accountTransaction->save();

            $transaction = Transaction::where('customer_id', $request->customer_id)->first();

            if ($transaction) {
                // Update existing transaction
                $transaction->date =  $request->sale_date;
                $transaction->branch_id =  Auth::user()->branch_id;
                $transaction->processed_by =  Auth::user()->id;
                $transaction->payment_type = 'receive';
                $transaction->particulars = 'Sale#' . $sale->id;
                $transaction->credit = $transaction->credit + $request->change_amount;
                $transaction->debit = $transaction->debit + $request->paid;
                $transaction->balance = $transaction->balance + ($request->change_amount - $request->paid);
                $transaction->payment_method = $request->payment_method;
                $transaction->save();
            } else {
                // Create new transaction
                $transaction = new Transaction;
                $transaction->date =  $request->sale_date;
                $transaction->processed_by =  Auth::user()->id;
                $transaction->branch_id =  Auth::user()->branch_id;
                $transaction->payment_type = 'receive';
                $transaction->particulars = 'Sale#' . $sale->id;
                $transaction->customer_id = $request->customer_id;
                $transaction->credit = $request->change_amount;
                $transaction->debit = $request->paid;
                $transaction->balance = $request->change_amount - $request->paid;
                $transaction->payment_method = $request->payment_method;
                $transaction->save();
            }

            return response()->json([
                'status' => 200,
                'saleId' => $sale->id,
                'message' => 'successfully Updated',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages(),
            ]);
        }
    }
    public function destroy($id)
    {
        $sale = Sale::findOrFail($id);
        $sale->delete();
        return back()->with('message', "Sale successfully Deleted");
    }
    public function filter(Request $request)
    {
        // dd($request->all());
        $saleQuery = Sale::query();

        // Filter by product_id if provided
        if ($request->product_id != "Select Product") {
            $saleQuery->whereHas('saleItem', function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            });
        }

        // Filter by customer_id if provided
        if ($request->customer_id != "Select Customer") {
            $saleQuery->where('customer_id', $request->customer_id);
        }

        // Filter by date range if both start_date and end_date are provided
        if ($request->startDate && $request->endDate) {
            $saleQuery->whereBetween('sale_date', [$request->startDate, $request->endDate]);
        }

        // Execute the query
        $sales = $saleQuery->get();

        return view('pos.sale.table', compact('sales'))->render();
    }
    public function find($id)
    {
        // dd($id);
        // $purchaseId = 'Purchase#' + $id;
        $sale = Sale::findOrFail($id);
        return response()->json([
            'status' => 200,
            'data' => $sale
        ]);
    }
    public function saleTransaction(Request $request, $id)
    {
        dd($request->all());
        $validator = Validator::make($request->all(), [
            "transaction_account" => 'required',
            "amount" => 'required|',
        ]);

        $validator->after(function ($validator) use ($id, $request) {
            $sale = Sale::findOrFail($id);
            if ($request->amount > $sale->due) {
                $validator->errors()->add('amount', 'The amount cannot be greater than the due amount.');
            }
        });
        if ($validator->passes()) {
            $sales = Sale::all();
            $sale = Sale::findOrFail($id);
            $sale->paid = $sale->paid + $request->amount;
            $sale->due = $sale->due - $request->amount;
            $sale->save();

            $customer = Customer::findOrFail($sale->customer_id);
            $customer->total_payable = $customer->total_payable + $request->amount;
            $customer->wallet_balance = $customer->wallet_balance - $request->amount;
            $customer->save();

            // accountTransaction table
            $accountTransaction = new AccountTransaction;
            $accountTransaction->branch_id =  Auth::user()->branch_id;
            $accountTransaction->purpose =  'Sale';
            $accountTransaction->reference_id = $id;
            $accountTransaction->account_id =  $request->transaction_account;
            $accountTransaction->credit = $request->amount;
            $oldBalance = AccountTransaction::where('account_id', $request->transaction_account)->latest('created_at')->first();
            dd($oldBalance->balance);
            $accountTransaction->balance = $oldBalance->balance + $request->amount;
            $accountTransaction->created_at = Carbon::now();
            $accountTransaction->save();

            $transaction = new Transaction;
            $transaction->branch_id =  Auth::user()->branch_id;
            $transaction->processed_by =  Auth::user()->id;
            $transaction->date = $request->payment_date;
            $transaction->payment_type = 'receive';
            $transaction->particulars = 'Sale#' . $id;
            $transaction->customer_id = $customer->id;
            $transaction->debit = $transaction->debit + $request->amount;
            $transaction->balance = $transaction->balance + $request->amount;
            $transaction->payment_method = $request->transaction_account;
            $transaction->save();

            // return view('pos.sale.table', compact('sales'))->render();

            return response()->json([
                'status' => 200,
                'message' => "Update successful",
                'sales' => $sales
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'error' => $validator->errors()
            ]);
        }
    }

    public function findQty($id)
    {
        $product = Product::findOrFail($id);

        return response()->json([
            'status' => 200,
            'product' => $product
        ]);
    }


    public function saleCustomer($id)
    {

        $status = 'active';
        $customer = Customer::findOrFail($id);
        $promotionDetails = PromotionDetails::whereHas('promotion', function ($query) use ($status) {
            return $query->where('status', '=', $status);
        })->where('promotion_type', 'customers')->where('logic', 'like', '%' . $id . "%")->get();
        $promotions = [];
        foreach ($promotionDetails as $promo) {
            $promotions[] = $promo->promotion;
        }
        // dd($promotion);
        if ($promotions) {
            return response()->json([
                'status' => '200',
                'data' => $customer,
                'promotions' => $promotions,
            ]);
        } else {
            return response()->json([
                'status' => '200',
                'data' => $customer
            ]);
        }
    }

    public function salePromotions($id)
    {
        $promotions = Promotion::findOrFail($id);
        return response()->json([
            'status' => '200',
            'promotions' => $promotions
        ]);
    }


    public function findProductWithBarcode($id)
    {
        $status = 'active';
        $products = Product::where('branch_id', Auth::user()->branch_id)->where('barcode', $id)->latest()->first();

        if ($products) { // Check if $products is not null
            if ($products->stock > 0) {
                $promotionDetails = PromotionDetails::whereHas('promotion', function ($query) use ($status) {
                    return $query->where('status', '=', $status);
                })->where('promotion_type', 'products')->where('logic', 'like', '%' . $products->id . "%")->latest()->first();

                if ($promotionDetails) {
                    return response()->json([
                        'status' => '200',
                        'data' => $products,
                        'promotion' => $promotionDetails->promotion,
                    ]);
                } else {
                    return response()->json([
                        'status' => '200',
                        'data' => $products
                    ]);
                }
            } else if ($products->stock <= 0) {
                return response()->json([
                    'status' => '300',
                    'error' => 'Not Enough Stock Available'
                ]);
            }
        } else { // Handle the case where no product is found
            return response()->json([
                'status' => '500',
                'error' => 'Product Not Available'
            ]);
        }
    }


    // public function saleProductFind($id)
    // {
    //     $saleItems = SaleItem::where('sale_id', $id)->get();
    //     // $products = Product::where('branch_id', Auth::user()->branch_id)->where('stock', '>', 0)->where('barcode', $id)->latest()->first();

    //     return response()->json([
    //         'status' => '200',
    //         'data' => $saleItems
    //     ]);
    // }
    public function saleProductFind($id)
    {
        $status = 'active';
        $saleItems = SaleItem::where('sale_id', $id)->get();

        $items = $saleItems->map(function ($saleItem) use ($status) {
            $product = Product::find($saleItem->product_id);
            $promotionDetails = PromotionDetails::whereHas('promotion', function ($query) use ($status) {
                return $query->where('status', '=', $status);
            })->where('promotion_type', 'products')->where('logic', 'like', '%' . $product->id . "%")->latest()->first();
            // dd($saleItem->qty);
            return [
                'product' => $product,
                'promotion' => $promotionDetails ? $promotionDetails->promotion : null,
                'quantity' => $saleItem->qty,
            ];
        });

        return response()->json([
            'status' => '200',
            'items' => $items
        ]);
    }

    public function saleCustomerDue($id)
    {
        $customer = Customer::findOrFail($id);
        return response()->json([
            'status' => '200',
            'customer' => $customer
        ]);
    }
    public function saleViewProduct()
    {
        if (Auth::user()->id == 1) {
            $products = Product::all();
        } else {
            $products = Product::where('branch_id', Auth::user()->branch_id)->latest()->get();
        }
        return response()->json([
            'status' => '200',
            'products' => $products
        ]);
    }
    public function saleViaProductAdd(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'price' => 'required',
            'cost' => 'required',
            'stock' => 'required',
            'transaction_account' => 'required',
        ],);

        if ($validator->passes()) {
            $oldBalance = AccountTransaction::where('account_id', $request->transaction_account)->latest('created_at')->first();
            // dd($oldBalance->balance >= $request->via_paid);
            if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->via_paid) {

                // $maxBarcode = Product::where('branch_id', Auth::user()->branch_id)
                //     ->max('barcode');
                // $product = new Product;
                $viaSale = new ViaSale;
                if ($request->name) {
                    $viaProduct = new ViaProduct;
                    $viaProduct->product_name = $request->name;
                    $viaProduct->save();
                    // $product->name =  $request->name;
                    $viaSale->via_product_id = $viaProduct->id;
                } elseif ($request->via_product_name) {
                    // $viaProducts = ViaProduct::findOrFail($request->via_product_name);
                    // $product->name = $viaProducts->product_name;
                    $viaSale->via_product_id = $request->via_product_name;
                }
                // $product->branch_id =  Auth::user()->branch_id;
                // if ($maxBarcode > 0) {
                //     $product->barcode =  $maxBarcode + 1;
                // } else {
                //     $product->barcode =  00001;
                // }
                // $categoryExist = Category::where('slug', 'like', '%via%')->first();
                // if ($categoryExist) {
                //     $product->category_id =  $categoryExist->id;
                // } else {
                //     $category = new Category;
                //     $category->name =  "Via Sell";
                //     $category->slug = Str::slug("via-sell");
                //     $category->save();
                //     $product->category_id =  $category->id;
                // }
                // $subcategoryExist = SubCategory::where('slug', 'like', '%via%')->first();
                // if ($subcategoryExist) {
                //     $product->subcategory_id =  $subcategoryExist->id;
                // } else {
                //     $categoryExist = Category::where('slug', 'like', '%via%')->first();
                //     $subcategory = new SubCategory;
                //     $subcategory->category_id =  $categoryExist->id;
                //     $subcategory->name =  "Via Sell";
                //     $subcategory->slug = Str::slug("via-sell");
                //     $subcategory->save();
                //     $product->subcategory_id =  $subcategory->id;
                // }
                // $brandExist = Brand::where('slug', 'like', '%via%')->first();
                // if ($brandExist) {
                //     $product->brand_id =  $brandExist->id;
                // } else {
                //     $brand = new Brand;
                //     $brand->name =  "Via Sell";
                //     $brand->slug = Str::slug("via-sell");
                //     $brand->save();
                //     $product->brand_id =  $brand->id;
                // }
                // $product->cost  =  $request->cost;
                // $product->price  =  $request->price;
                // $unitExist = Unit::where('name', 'like', '%Piece%')->first();
                // if ($unitExist) {
                //     $product->unit_id =  $unitExist->id;
                // } else {
                //     $unit = new Unit;
                //     $unit->name =  "Piece";
                //     $unit->related_by = 1;
                //     $unit->save();
                //     $product->unit_id = $unit->id;
                // }
                // $product->stock = $request->stock;
                // $product->save();

                //Via sale

                $viaSale->invoice_date = Carbon::now();
                $viaSale->branch_id =  Auth::user()->branch_id;
                $viaSale->invoice_number = $request->invoice_number;
                $viaSale->processed_by = Auth::user()->id;
                $viaSale->supplier_name = $request->via_supplier_name;
                // $viaSale->product_id = $product->id;
                // if($request->name)
                // // $viaSale->product_name = 'empty';
                // else{
                //     $viaSale->via_product_id = $request->via_product_id;
                //  }


                $viaSale->quantity = $request->stock;
                $viaSale->cost_price = $request->cost;
                $viaSale->sale_price = $request->price;
                $viaSale->sub_total = $request->via_total_pay;
                if ($request->via_paid == null) {
                    $viaSale->paid = 0;
                    $viaSale->due = $request->via_total_pay;
                } else {
                    $viaSale->paid = $request->via_paid;
                    $viaSale->due = $request->via_due;
                }
                if ($request->via_paid >= $request->via_product_total) {
                    $viaSale->status  = 1;
                }
                $viaSale->save();
                $viaSale->load('viaProduct');
                // account Transaction crud
                $accountTransaction = new AccountTransaction;
                $accountTransaction->branch_id =  Auth::user()->branch_id;
                $accountTransaction->purpose =  'Via Purchase';
                $accountTransaction->reference_id = $viaSale->id;
                $accountTransaction->account_id =  $request->transaction_account;
                $accountTransaction->debit = $request->via_paid;
                $oldBalance = AccountTransaction::where('account_id', $request->transaction_account)->latest('created_at')->first();
                $accountTransaction->balance = $oldBalance->balance - $request->via_paid;
                $accountTransaction->created_at = Carbon::now();
                $accountTransaction->save();

                return response()->json([
                    'status' => 200,
                    'products' => $viaSale,
                    'message' => 'Via Product Save Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => 400,
                    'message' => 'Not Enough Balance in Account. Please choose Another Account or Deposit Account Balance',
                ]);
            }
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    }
}
