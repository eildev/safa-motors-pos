<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ReturnItem;
use App\Models\Returns;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReturnController extends Controller
{
    public function Return($id)
    {
        $sale = Sale::findOrFail($id);
        $customer = Customer::findOrFail($sale->customer_id);
        $sale_items = SaleItem::where('sale_id', $sale->id)->get();
        return view('pos.return.return', compact('sale', 'customer', 'sale_items'));
    }
    public function ReturnItems($id)
    {
        $sales = SaleItem::findOrFail($id);
        $sales->load('product');
        return response()->json([
            'status' => '200',
            'sale_items' => $sales
        ]);
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'sale_id' => 'required',
            'customer_id' => 'required',
            'formattedReturnDate' => 'required',
            'refund_amount' => 'required',
            'paymentMethod' => 'required',
        ]);
        if ($validator->passes()) {
            $oldBalance = AccountTransaction::where('account_id', $request->paymentMethod)->latest('created_at')->first();
            // dd($oldBalance);
            if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->refund_amount ?? 0) {
                $total_return_profit = 0;
                $totalQuantity = 0;
                $productCost = 0;
                foreach ($request->sale_items as $sale_item) {
                    $saleItem = SaleItem::findOrFail($sale_item['product_id']);

                    $actual_total_return_price = $saleItem->rate * $sale_item['quantity'];
                    $return_profit = $actual_total_return_price - $sale_item['total_price'];
                    $total_return_profit += $return_profit;

                    $product = Product::findOrFail($saleItem->product_id);
                    $total = $product->cost * ($saleItem->qty - $sale_item['quantity']);
                    // dd($total);
                    $productCost += $total;
                    $totalQuantity += $sale_item['quantity'];
                }
                // dd($total_return_profit);

                $returns = new Returns;
                $returns->return_invoice_number = rand(123456, 99999);
                $returns->branch_id = Auth::user()->branch_id;
                $returns->sale_id = $request->sale_id;
                $returns->customer_id = $request->customer_id;
                $returns->return_date = $request->formattedReturnDate;
                $returns->refund_amount = $request->refund_amount;
                $returns->return_reason = $request->note ?? '';

                $returns->total_return_profit = $total_return_profit;
                $returns->status = 1;
                $returns->processed_by = Auth::user()->id;
                $returns->save();


                // dd($request->refund_amount);

                foreach ($request->sale_items as $sale_item) {
                    $product = Product::findOrFail($saleItem->product_id);

                    $returnItems = new ReturnItem;
                    $returnItems->return_id = $returns->id;
                    $returnItems->product_id = $product->id;
                    $returnItems->quantity = (int)$sale_item['quantity'];
                    $returnItems->return_price = (int)$sale_item['return_price'];
                    $returnItems->product_total = (int)$sale_item['total_price'];
                    $returnItems->save();

                    $product->stock = $product->stock + $sale_item['quantity'];
                    $product->total_sold = $product->total_sold - $sale_item['quantity'];
                    $product->save();
                }

                $sale = Sale::findOrFail($request->sale_id);
                $sale->returned = $request->refund_amount;
                $sale->save();

                // customer crud
                $customer = Customer::findOrFail($request->customer_id);
                $customerDue = $customer->wallet_balance;

                // account Transaction Crud
                $lastTransaction = AccountTransaction::where('account_id', $request->paymentMethod)->latest('created_at')->first();
                $accountTransaction =  new AccountTransaction;
                $accountTransaction->branch_id =  Auth::user()->branch_id;
                $accountTransaction->reference_id = $returns->id;
                $accountTransaction->account_id =  $request->paymentMethod;
                $accountTransaction->created_at = Carbon::now();

                // transaction CRUD
                // $lastTransactionData = Transaction::latest('created_at')->first();
                $transaction = new Transaction;
                $transaction->date = $request->formattedReturnDate;
                $transaction->others_id = $returns->id;
                $transaction->processed_by =  Auth::user()->id;
                $transaction->branch_id = Auth::user()->branch_id;
                $transaction->payment_method = $request->paymentMethod;
                $transaction->created_at = Carbon::now();
                if ($request->adjustDue == 'yes') {
                    if ($customerDue > $request->refund_amount) {
                        $customer->total_payable = $customer->total_payable + $request->refund_amount;
                        $customer->wallet_balance = $customer->wallet_balance - $request->refund_amount;

                        $accountTransaction->purpose =  'Adjust Due';
                        $accountTransaction->credit = $request->refund_amount;
                        $accountTransaction->balance = $lastTransaction->balance + $request->refund_amount;

                        $transaction->particulars = 'Adjust Due Collection';
                        $transaction->customer_id = $customer->id;
                        $transaction->payment_type = 'receive';
                        $transaction->credit = $request->refund_amount;
                        $transaction->debit = 0;
                        $transaction->balance = $transaction->debit - $transaction->credit;
                    } else {
                        $returnBalance = $request->refund_amount - $customerDue;

                        $customer->wallet_balance = 0;

                        $accountTransaction->purpose =  'Return';
                        $accountTransaction->debit = $returnBalance;
                        $accountTransaction->balance = $lastTransaction->balance - $returnBalance;

                        $transaction->particulars = 'Return';
                        $transaction->customer_id = $customer->id;
                        $transaction->payment_type = 'pay';
                        $transaction->credit = 0;
                        $transaction->debit = $returnBalance;
                        $transaction->balance = $transaction->debit - $transaction->credit;
                    }
                } else {

                    $accountTransaction->purpose =  'Return';
                    $accountTransaction->debit = $request->refund_amount;
                    $accountTransaction->balance = $lastTransaction->balance - $request->refund_amount;

                    $transaction->particulars = 'Return';
                    $transaction->customer_id = $customer->id;
                    $transaction->payment_type = 'pay';
                    $transaction->credit = 0;
                    $transaction->debit = $request->refund_amount;
                    $transaction->balance = $transaction->debit - $transaction->credit;
                }
                $customer->save();
                $accountTransaction->save();
                $transaction->save();

                return response()->json([
                    'status' => '200',
                    'message' => 'Product Return successful',
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
                'error' => $validator->messages(),
            ]);
        }
    }
    public function returnProductsList()
    {
        if (Auth::user()->id == 1) {
            $returns = Returns::latest()->get();
        } else {
            $returns = Returns::where('branch_id', Auth::user()->branch_id)->latest()->get();
        }

        return view('pos.return.return-view', compact('returns'));
    }
    public function returnProductsInvoice($id)
    {
        $return = Returns::findOrFail($id);
        $branch = Branch::findOrFail($return->branch_id);
        $customer = Customer::findOrFail($return->customer_id);
        $return_items = ReturnItem::where('return_id', $return->id)->get();
        if ($return->processed_by) {
            $authName = User::findOrFail($return->processed_by)->name;
        } else {
            $authName = "";
        }


        return view('pos.return.invoice', compact('return', 'branch', 'customer', 'return_items', 'authName'));
    }
}
