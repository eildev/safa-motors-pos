<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\AccountTransaction;
use App\Models\Branch;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Investor;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function TransactionAdd()
    {
        if (Auth::user()->id == 1) {
            $supplier = Supplier::latest()->get();
            $customer = Customer::latest()->get();
            $paymentMethod = Bank::all();
            // dd($paymentMethod);
            $investors = Investor::latest()->get();
            $transaction = Transaction::latest()->get();
        } else {
            $supplier = Supplier::where('branch_id', Auth::user()->branch_id)->latest()->get();
            $customer = Customer::where('branch_id', Auth::user()->branch_id)->latest()->get();
            $paymentMethod = Bank::where('branch_id', Auth::user()->branch_id)->latest()->get();
            $investors = Investor::where('branch_id', Auth::user()->branch_id)->latest()->get();
            $transaction = Transaction::where('branch_id', Auth::user()->branch_id)->latest()->get();
        }
        return view('pos.transaction.transaction_add', compact('paymentMethod', 'supplier', 'customer', 'transaction', 'investors'));
    } //
    // public function TransactionView(){
    //     return view('pos.transaction.transaction_view');
    // }
    public function getDataForAccountId(Request $request)
    {
        $accountId = $request->input('id');
        $account_type = $request->input('account_type');
        //dd($accountId);
        if ($account_type == "supplier") {
            $info = Supplier::findOrFail($accountId);
            $count = Purchase::where('supplier_id', $accountId)->where('due', '>', 0)->count();
        } elseif ($account_type == "customer") {
            $info = Customer::findOrFail($accountId);
            $count = '-';
        } elseif ($account_type == "other") {
            $info = Investor::findOrFail($accountId);
            $count = '-';
        }
        return response()->json([
            "info" => $info,
            "count" => $count
        ]);
    } // End function
    public function TransactionStore(Request $request)
    {
        // dd($request->account_type);
        $request->validate([
            'debit' => ['numeric', 'max:12'],
            'credit' => ['numeric', 'max:12'],
        ]);

        // if ($request->account_type == 'supplier') {
        //     //Here change
        //     $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
        //     if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->amount) {
        //         //Here change End
        //         $supplier = Supplier::findOrFail($request->account_id);
        //         // dd($request->account_id);
        //         $currentBalance = $supplier->wallet_balance;
        //         $currentBalance = $currentBalance ?? 0;
        //         $newBalance = floatval($currentBalance) - floatval($request->amount);
        //         $supplier->wallet_balance = $newBalance;
        //         $newPayble = $supplier->total_payable ?? 0;
        //         $updatePaybele = floatval($newPayble) + floatval($request->amount);
        //         // dd($tracBalance->balance);
        //         $supplier->total_payable = $updatePaybele;
        //         $tracBalance = Transaction::where('supplier_id', $supplier->id)->latest()->first();
        //         if ($tracBalance !== null) {
        //             $debitBalance = floatval($tracBalance->balance);
        //             $updateTraBalance = ($debitBalance ?? 0) - floatval($request->amount);
        //         } else {
        //             $updateTraBalance = floatval($request->amount); // Set to default value or handle
        //         }
        //         // dd($updateTraBalance);
        //         $transaction = Transaction::create([
        //             'branch_id' => Auth::user()->branch_id,
        //             'date' => $request->date,
        //             'payment_type' => 'pay',
        //             'particulars' => 'PurchaseDue',
        //             'debit' => $request->amount,
        //             'payment_method' => $request->payment_method,
        //             'balance' => $updateTraBalance,
        //             'note' => $request->note,
        //             'supplier_id' => $request->account_id
        //         ]);

        //         $supplier->update([
        //             'wallet_balance' => $newBalance,
        //             'total_payable' => $updatePaybele
        //         ]);
        //         //account Transaction Crud
        //         $accountTransaction = new AccountTransaction;
        //         $accountTransaction->branch_id =  Auth::user()->branch_id;
        //         $accountTransaction->reference_id = $transaction->id;
        //         $accountTransaction->purpose =  'PurchaseDue';
        //         $accountTransaction->account_id =  $request->payment_method;
        //         $accountTransaction->debit = $request->amount;
        //         $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
        //         $accountTransaction->balance = $oldBalance->balance - $request->amount;
        //         $accountTransaction->created_at = Carbon::now();
        //         $accountTransaction->save();
        //         $notification = [
        //             'message' => 'Transaction Payment Successful',
        //             'alert-type' => 'info'
        //         ];
        //         return redirect()->back()->with($notification);
        //     } else {
        //         $notification = [
        //             'warning' => 'Your account Balance is low Please Select Another account',
        //             'alert-type' => 'warning'
        //         ];
        //         return redirect()->back()->with($notification);
        //     }
        //     //End
        // }
        // else if ($request->account_type == 'customer') {
        //     //Customer Table Update
        //     $customer = Customer::findOrFail($request->account_id);
        //     $newBalance = $customer->wallet_balance - $request->amount;
        //     $newPayable = $customer->total_payable + $request->amount;
        //     $customer->update([
        //         'wallet_balance' => $newBalance,
        //         'total_payable' => $newPayable
        //     ]);

        //     // transaction crud Update
        //     $tracsBalance = Transaction::where('customer_id', $customer->id)->latest()->first();
        //     $transBalance = $tracsBalance->balance ?? 0;
        //     $newTrasBalance = $transBalance + $request->amount;
        //     $transaction = Transaction::create([
        //         'branch_id' => Auth::user()->branch_id,
        //         'date' => $request->date,
        //         'payment_type' => 'receive',
        //         'particulars' => 'SaleDue',
        //         'credit' => $request->amount,
        //         'payment_method' => $request->payment_method,
        //         'note' => $request->note,
        //         'balance' => $newTrasBalance,
        //         'customer_id' => $request->account_id
        //     ]);

        //     //account Transaction Crud
        //     $accountTransaction = new AccountTransaction;
        //     $accountTransaction->branch_id =  Auth::user()->branch_id;
        //     $accountTransaction->reference_id = $transaction->id;
        //     $accountTransaction->purpose =  'SaleDue';
        //     $accountTransaction->account_id =  $request->payment_method;
        //     $accountTransaction->credit = $request->amount;
        //     $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
        //     if ($oldBalance) {
        //         $accountTransaction->balance = $oldBalance->balance + $request->amount;
        //     } else {
        //         $accountTransaction->balance = $request->amount;
        //     }
        //     $accountTransaction->created_at = Carbon::now();
        //     $accountTransaction->save();
        //     $notification = [
        //         'message' => 'Transaction Payment Successful',
        //         'alert-type' => 'info'
        //     ];
        //     return redirect()->back()->with($notification);
        // } else
        if ($request->account_type == 'other') {
            $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
            if ($request->transaction_type == 'pay') {
                if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->amount) {
                    Transaction::create([
                        'branch_id' => Auth::user()->branch_id,
                        'date' => $request->date,
                        'processed_by' => Auth::user()->id,
                        'payment_type' => $request->transaction_type,
                        'particulars' => 'OthersPayment',
                        'debit' => $request->amount,
                        'payment_method' => $request->payment_method,
                        'note' => $request->note,
                        'balance' => $request->amount,
                        'others_id' => $request->account_id,
                    ]);
                    $investor = Investor::findOrFail($request->account_id);
                    $currentBalance = $investor->balance;
                    $newBalance = $currentBalance  - $request->amount;
                    $oldDebit = $investor->debit  + $request->amount;
                    $investor->update([
                        'type' => $request->type,
                        'debit' =>  $oldDebit,
                        'balance' => $newBalance,
                    ]);
                    // account transaction
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->branch_id =  Auth::user()->branch_id;
                    $accountTransaction->reference_id = $investor->id;
                    $accountTransaction->purpose =  'OthersPayment';
                    $accountTransaction->account_id =  $request->payment_method;
                    $accountTransaction->debit = $request->amount;
                    $accountTransaction->balance = $oldBalance->balance - $request->amount;
                    $accountTransaction->created_at = Carbon::now();
                    $accountTransaction->save();

                    $notification = [
                        'message' => 'Transaction Others Successful',
                        'alert-type' => 'info'
                    ];
                    return redirect()->back()->with($notification);
                } else {
                    $notification = [
                        'warning' => 'Your account Balance is low Please Select Another account',
                        'alert-type' => 'warning'
                    ];
                    return redirect()->back()->with($notification);
                }
            } else if ($request->transaction_type == 'receive') {
                Transaction::create([
                    'branch_id' => Auth::user()->branch_id,
                    'date' => $request->date,
                    'processed_by' => Auth::user()->id,
                    'payment_type' => $request->transaction_type,
                    'particulars' => 'OthersReceive',
                    'credit' => $request->amount,
                    'payment_method' => $request->payment_method,
                    'note' => $request->note,
                    'balance' => -$request->amount,
                    'others_id' => $request->account_id,
                ]);
                $investor = Investor::findOrFail($request->account_id);
                $currentBalance = $investor->balance;
                $newBalance = $currentBalance  + $request->amount;
                $oldCredit = $investor->credit + $request->amount;
                $investor->update([
                    'type' => $request->type,
                    'credit' =>  $oldCredit,
                    'balance' => $newBalance,
                ]);

                // account Transaction
                $accountTransaction = new AccountTransaction;
                $accountTransaction->branch_id =  Auth::user()->branch_id;
                $accountTransaction->reference_id = $investor->id;
                $accountTransaction->purpose =  'OthersReceive';
                $accountTransaction->account_id =  $request->payment_method;
                $accountTransaction->credit = $request->amount;
                $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
                if ($oldBalance) {
                    $accountTransaction->balance = $oldBalance->balance + $request->amount;
                } else {
                    $accountTransaction->balance = $request->amount;
                }
                $accountTransaction->created_at = Carbon::now();
                $accountTransaction->save();

                $notification = [
                    'message' => 'Transaction Others Successful',
                    'alert-type' => 'info'
                ];
                return redirect()->back()->with($notification);
            }
        }
    } //
    public function TransactionDelete($id)
    {
        Transaction::find($id)->delete();
        $notification = [
            'message' => 'Transaction Deleted Successfully',
            'alert-type' => 'info'
        ];
        return redirect()->back()->with($notification);
    } //
    public function TransactionFilterView(Request $request)
    {
        // $customerName="";
        // $suplyerName="";
        // if($request->filterCustomer == 'Select Customer'){
        //     $customerName = null;
        // }
        // if($request->filterSupplier == 'Select Supplier'){
        //     $suplyerName = null;
        // }
        $transaction = Transaction::when($request->filterCustomer != 'Select Customer', function ($query) use ($request) {
            return $query->where('customer_id', $request->filterCustomer);
        })
            ->when($request->filterSupplier != 'Select Supplier', function ($query) use ($request) {
                return $query->where('supplier_id', $request->filterSupplier);
            })
            ->when($request->startDate && $request->endDate, function ($query) use ($request) {
                return $query->whereBetween('date', [$request->startDate, $request->endDate]);
            })
            ->get();
        return view('pos.transaction.transaction-filter-rander-table', compact('transaction'))->render();
    }
    public function TransactionInvoiceReceipt($id)
    {
        $transaction = Transaction::findOrFail($id);
        if ($transaction->customer_id) {
            $data = Customer::findOrFail($transaction->customer_id);
        } else if ($transaction->supplier_id) {
            $data = Supplier::findOrFail($transaction->supplier_id);
        } else if ($transaction->others_id) {
            $data = Investor::findOrFail($transaction->others_id);
        } else {
            $data = "";
        }

        if ($transaction->processed_by) {
            $authName = User::findOrFail($transaction->processed_by)->name;
        } else {
            $authName = "";
        }

        return view('pos.transaction.invoice', compact('transaction', 'data', 'authName'));
    }
    public function InvestmentStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
        ]);
        if ($validator->passes()) {
            $investor = new Investor;
            $investor->branch_id = Auth::user()->branch_id;
            $investor->name = $request->name;
            $investor->phone = $request->phone;
            $investor->created_at = Carbon::now();
            $investor->save();
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
    public function GetInvestor()
    {
        if (Auth::user()->id == 1) {
            $data = Investor::latest()->get();
        } else {
            $data = Investor::where('branch_id', Auth::user()->branch_id)->latest()->get();
        }
        return response()->json([
            'status' => 200,
            'message' => 'Successfully save',
            'allData' => $data
        ]);
    }
    public function InvestorInvoice($id)
    {
        $investors = Investor::findOrFail($id);
        return view('pos.investor.investor-invoice', compact('investors'));
    }


    public function invoicePaymentStore(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'payment_balance' => 'required',
            'account' => 'required',
        ]);

        if ($validator->passes()) {

            // transaction 
            $transaction = new Transaction;
            $transaction->branch_id = Auth::user()->branch_id;
            $transaction->date = Carbon::now();
            $transaction->processed_by =  Auth::user()->id;
            $transaction->payment_type = 'receive';
            $transaction->payment_method = $request->account;
            $transaction->credit = $request->payment_balance;
            $transaction->debit = 0;
            $transaction->balance = $transaction->debit - $transaction->credit ?? 0;

            //Account Transaction Table
            $accountTransaction = new AccountTransaction;
            $accountTransaction->branch_id =  Auth::user()->branch_id;
            $accountTransaction->account_id =  $request->account;


            $oldBalance = AccountTransaction::where('account_id', $request->account)->latest('created_at')->first();

            if ($request->isCustomer == 1) {
                // transaction 
                $transaction->particulars = 'SaleDue';
                $transaction->customer_id = $request->data_id;

                //Customer Table
                $customer = Customer::findOrFail($request->data_id);
                $newBalance = $customer->wallet_balance - $request->payment_balance;
                $newPayable = $customer->total_payable + $request->payment_balance;
                $customer->update([
                    'wallet_balance' => $newBalance,
                    'total_payable' => $newPayable
                ]);

                $accountTransaction->purpose =  'SaleDue';
                $accountTransaction->credit = $request->payment_balance;
                if ($oldBalance) {
                    $accountTransaction->balance = $oldBalance->balance + $request->payment_balance;
                } else {
                    $accountTransaction->balance = $request->payment_balance;
                }
            } else {
                if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->payment_balance) {
                    // transaction update 
                    $transaction->particulars = 'PurchaseDue';
                    $transaction->supplier_id = $request->data_id;


                    // supplier Crud 
                    $supplier = Supplier::findOrFail($request->data_id);
                    $newBalance = $supplier->wallet_balance - $request->payment_balance;
                    $newPayable = $supplier->total_payable + $request->payment_balance;
                    $supplier->update([
                        'wallet_balance' => $newBalance,
                        'total_payable' => $newPayable
                    ]);


                    $accountTransaction->purpose =  'PurchaseDue';
                    $accountTransaction->debit = $request->payment_balance;
                    $accountTransaction->balance = $oldBalance->balance - $request->payment_balance;
                } else {
                    return response()->json([
                        'status' => 400,
                        'message' => 'Your account Balance is low Please Select Another account or Add Balance on your Account',
                    ]);
                }
            }
            $accountTransaction->save();
            $transaction->save();

            return response()->json([
                'status' => 200,
                'message' => 'Successfully Payment',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    }

    public function investorDetails($id)
    {
        $investor = Investor::findOrFail($id);
        $branch = Branch::findOrFail($investor->branch_id);
        $transactions = Transaction::where(function ($query) {
            $query->where('particulars', 'OthersPayment')
                ->orWhere('particulars', 'OthersReceive');
        })->where('others_id', $id)->get();
        $banks = Bank::get();
        return view('pos.investor.investorDetails', compact('investor', 'branch', 'transactions', 'banks'));
    }
}
