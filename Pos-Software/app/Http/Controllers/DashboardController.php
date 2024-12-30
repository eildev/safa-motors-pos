<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\Bank;
use App\Models\EmployeeSalary;
use App\Models\Expense;
use App\Models\Purchase;
use App\Models\Returns;
use App\Models\Sale;
use App\Models\Transaction;
use App\Models\ViaSale;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // today summary
        $branchData = [];
        $banks = Bank::get();
        if (Auth::user()->id == 1) {
            $branches = Branch::get();
            foreach ($branches as $branch) {
                $branchId = $branch->id;
                $viaSale = ViaSale::where('branch_id', $branchId)
                    ->whereDate('created_at', Carbon::now())->get();
                $todaySalesData = Sale::where('branch_id', $branchId)
                    ->whereDate('created_at', Carbon::now())->get();
                $todaySales = $todaySalesData->sum('paid') - $viaSale->sum('sub_total');

                $todayPurchase = Purchase::where('branch_id', $branchId)
                    ->whereDate('created_at', Carbon::now())->get();
                $todayExpanse = Expense::where('branch_id', $branchId)
                    ->whereDate('created_at', Carbon::now())->get();
                $dueCollection = Transaction::where('branch_id', $branchId)
                    ->where('particulars', 'SaleDue')
                    ->whereDate('created_at', Carbon::now())
                    ->get();
                $otherCollection = Transaction::where('branch_id', $branchId)
                    ->where('particulars', 'OthersReceive')
                    ->whereDate('created_at', Carbon::now())
                    ->get();
                $otherPaid = Transaction::where('branch_id', $branchId)
                    ->where('particulars', 'OthersPayment')
                    ->whereDate('created_at', Carbon::now())
                    ->get();
                $purchaseDuePay = Transaction::where('branch_id', $branchId)
                    ->where('particulars', 'PurchaseDue')
                    ->whereDate('created_at', Carbon::now())
                    ->get();
                $todayBalance = AccountTransaction::where('branch_id', $branchId)
                    ->whereDate('created_at', Carbon::now())
                    ->latest()
                    ->first();
                $previousDayBalance = 0;
                $date = Carbon::now();
                foreach ($banks as $bank) {
                    $transaction = AccountTransaction::where('branch_id', $branchId)
                        ->where('account_id', $bank->id)
                        ->whereDate('created_at', '<', $date)
                        ->latest('created_at')
                        ->first();

                    if ($transaction) {
                        $previousDayBalance += $transaction->balance;
                    }
                }
                $addBalance = AccountTransaction::where('branch_id', $branchId)
                    ->where(function ($query) {
                        $query->where('purpose', 'Add Bank Balance')
                            ->orWhere('purpose', 'Bank');
                    })
                    ->whereDate('created_at', Carbon::now())
                    ->get();
                $todayEmployeeSalary = EmployeeSalary::where('branch_id', $branchId)
                    ->whereDate('created_at', Carbon::now())->get();
                $adjustDueCollection = Transaction::where('branch_id', $branchId)
                    ->where('particulars', 'Adjust Due Collection')
                    ->where('payment_type', 'receive')
                    ->whereDate('created_at', Carbon::now())
                    ->sum('credit');
                $todayReturnAmount = Transaction::where('branch_id', $branchId)
                    ->where('particulars', 'Return')
                    ->where('payment_type', 'pay')
                    ->whereDate('created_at', Carbon::now())
                    ->sum('debit');
                $viaPayment = AccountTransaction::where('branch_id', $branchId)
                    ->where('purpose', 'Via Payment')
                    ->whereDate('created_at', Carbon::now())
                    ->get();

                $totalIngoing =
                    $previousDayBalance +
                    $todaySales +
                    $dueCollection->sum('credit') +
                    $otherCollection->sum('credit') +
                    $addBalance->sum('credit') +
                    $adjustDueCollection +
                    $viaSale->sum('sub_total');
                $totalOutgoing =
                    $todayPurchase->sum('paid') +
                    $todayExpanse->sum('amount') +
                    $todayEmployeeSalary->sum('debit') +
                    $todayReturnAmount +
                    $purchaseDuePay->sum('debit') +
                    $otherPaid->sum('debit') +
                    $viaPayment->sum('debit');
                // Store the data for the current branch
                $branchData[$branchId] = [
                    'previousDayBalance' => $previousDayBalance,
                    'todaySales' => $todaySales,
                    'todayPurchase' => $todayPurchase->sum('paid'),
                    'todayExpanse' => $todayExpanse->sum('amount'),
                    'dueCollection' => $dueCollection->sum('credit'),
                    'otherCollection' => $otherCollection->sum('credit'),
                    'otherPaid' => $otherPaid->sum('debit'),
                    'purchaseDuePay' => $purchaseDuePay->sum('debit'),
                    'addBalance' => $addBalance->sum('credit'),
                    'todayEmployeeSalary' => $todayEmployeeSalary->sum('debit'),
                    'todayReturnAmount' => $todayReturnAmount,
                    'adjustDueCollection' => $adjustDueCollection,
                    'viaSale' => $viaSale->sum('sub_total'),
                    'viaPayment' => $viaPayment->sum('debit'),
                    'branch' => $branch,
                    'totalIngoing' => $totalIngoing,
                    'totalOutgoing' => $totalOutgoing

                    // Add other relevant data here
                ];
            } //End ForEach
        } //end if
        else {
            $branchId = Auth::user()->branch_id;
            $viaSale = ViaSale::where('branch_id', $branchId)
                ->whereDate('created_at', Carbon::now())->get();
            $todaySalesData = Sale::where('branch_id', $branchId)
                ->whereDate('created_at', Carbon::now())->get();
            $todaySales = $todaySalesData->sum('paid') - $viaSale->sum('sub_total');

            $todayPurchase = Purchase::where('branch_id', $branchId)
                ->whereDate('created_at', Carbon::now())->get();
            $todayExpanse = Expense::where('branch_id', $branchId)
                ->whereDate('created_at', Carbon::now())->get();
            $dueCollection = Transaction::where('branch_id', $branchId)
                ->where('particulars', 'SaleDue')
                ->whereDate('created_at', Carbon::now())
                ->get();
            $otherCollection = Transaction::where('branch_id', $branchId)
                ->where('particulars', 'OthersReceive')
                ->whereDate('created_at', Carbon::now())
                ->get();
            $otherPaid = Transaction::where('branch_id', $branchId)
                ->where('particulars', 'OthersPayment')
                ->whereDate('created_at', Carbon::now())
                ->get();
            $purchaseDuePay = Transaction::where('branch_id', $branchId)
                ->where('particulars', 'PurchaseDue')
                ->whereDate('created_at', Carbon::now())
                ->get();
            $todayBalance = AccountTransaction::where('branch_id', $branchId)
                ->whereDate('created_at', Carbon::now())
                ->latest()
                ->first();
            $previousDayBalance = 0;
            $date = Carbon::now();
            foreach ($banks as $bank) {
                $transaction = AccountTransaction::where('branch_id', $branchId)
                    ->where('account_id', $bank->id)
                    ->whereDate('created_at', '<', $date)
                    ->latest('created_at')
                    ->first();

                if ($transaction) {
                    $previousDayBalance += $transaction->balance;
                }
            }
            $addBalance = AccountTransaction::where('branch_id', $branchId)
                ->where(function ($query) {
                    $query->where('purpose', 'Add Bank Balance')
                        ->orWhere('purpose', 'Bank');
                })
                ->whereDate('created_at', Carbon::now())
                ->get();
            $todayEmployeeSalary = EmployeeSalary::where('branch_id', $branchId)
                ->whereDate('created_at', Carbon::now())->get();
            $viaPayment = AccountTransaction::where('branch_id', $branchId)
                ->where('purpose', 'Via Payment')
                ->whereDate('created_at', Carbon::now())
                ->get();
            $adjustDueCollection = Transaction::where('branch_id', $branchId)
                ->where('particulars', 'Adjust Due Collection')
                ->where('payment_type', 'receive')
                ->whereDate('created_at', Carbon::now())
                ->sum('credit');
            $todayReturnAmount = Transaction::where('branch_id', $branchId)
                ->where('particulars', 'Return')
                ->where('payment_type', 'pay')
                ->whereDate('created_at', Carbon::now())
                ->sum('debit');

            $totalIngoing =
                $previousDayBalance +
                $todaySales +
                $dueCollection->sum('credit') +
                $otherCollection->sum('credit') +
                $addBalance->sum('credit') +
                $adjustDueCollection +
                $viaSale->sum('sub_total');
            $totalOutgoing =
                $todayPurchase->sum('paid') +
                $todayExpanse->sum('amount') +
                $todayEmployeeSalary->sum('debit') +
                $todayReturnAmount +
                $purchaseDuePay->sum('debit') +
                $otherPaid->sum('debit') +
                $viaPayment->sum('debit');
        } //End else

        // today Total Summary
        $todayTotalViaSale = ViaSale::whereDate('created_at', Carbon::now())->sum('sub_total');
        $todaySaleTotal = Sale::whereDate('created_at', Carbon::now())->sum('paid');
        $todayTotalSales = $todaySaleTotal - $todayTotalViaSale;

        $todayTotalPurchase = Purchase::whereDate('created_at', Carbon::now())->sum('paid');
        $todayTotalExpanse = Expense::whereDate('created_at', Carbon::now())->sum('amount');
        $todayTotalDueCollection = Transaction::where('particulars', 'SaleDue')
            ->whereDate('created_at', Carbon::now())
            ->sum('credit');
        $todayTotalOtherCollection = Transaction::where('particulars', 'OthersReceive')
            ->whereDate('created_at', Carbon::now())
            ->sum('credit');
        $todayTotalOtherPaid = Transaction::where('particulars', 'OthersPayment')
            ->whereDate('created_at', Carbon::now())
            ->sum('debit');
        $todayTotalPurchaseDuePay = Transaction::where('particulars', 'PurchaseDue')
            ->whereDate('created_at', Carbon::now())
            ->sum('debit');
        $todayBalance = AccountTransaction::whereDate('created_at', Carbon::now())
            ->latest()
            ->first();
        $previousDayTotalBalance = 0;
        $date = Carbon::now();
        foreach ($banks as $bank) {
            $transaction = AccountTransaction::where('account_id', $bank->id)
                ->whereDate('created_at', '<', $date)
                ->latest('created_at')
                ->first();

            if ($transaction) {
                $previousDayTotalBalance += $transaction->balance;
            }
        }
        $todayTotalAddBalance = AccountTransaction::where(function ($query) {
            $query->where('purpose', 'Add Bank Balance')
                ->orWhere('purpose', 'Bank');
        })
            ->whereDate('created_at', Carbon::now())
            ->sum('credit');
        $todayTotalEmployeeSalary = EmployeeSalary::whereDate('created_at', Carbon::now())->sum('debit');
        $todayTotalAdjustDueCollection = Transaction::where('particulars', 'Adjust Due Collection')
            ->where('payment_type', 'receive')
            ->whereDate('created_at', Carbon::now())
            ->sum('credit');
        $todayTotalReturnAmount = Transaction::where('particulars', 'Return')
            ->where('payment_type', 'pay')
            ->whereDate('created_at', Carbon::now())
            ->sum('debit');
        $todayTotalViaPayment = AccountTransaction::where('purpose', 'Via Payment')
            ->whereDate('created_at', Carbon::now())
            ->sum('debit');

        $todayTotalIngoing =
            $previousDayTotalBalance +
            $todayTotalSales +
            $todayTotalDueCollection +
            $todayTotalOtherCollection +
            $todayTotalAddBalance +
            $todayTotalAdjustDueCollection +
            $todayTotalViaSale;
        $todayTotalOutgoing =
            $todayTotalPurchase +
            $todayTotalExpanse +
            $todayTotalEmployeeSalary +
            $todayTotalReturnAmount +
            $todayTotalPurchaseDuePay +
            $todayTotalOtherPaid +
            $todayTotalViaPayment;


        // Total Summary
        if (Auth::user()->id == 1) {
            $sales = Sale::get();
            $purchase = Purchase::get();
            $expanse = Expense::get();
            $salary = EmployeeSalary::get();
            $bankLabels = [];
            $grandTotal = 0;
            foreach ($banks as $bank) {
                $transaction = AccountTransaction::where('account_id', $bank->id)
                    ->latest('created_at')
                    ->first();
                // dd($transaction);
                if ($transaction) {
                    $bankData = [
                        'name' => $bank->name,
                        'amount' => number_format($transaction->balance, 2), // Accessing the balance attribute
                    ];
                    array_push($bankLabels, $bankData);
                    $grandTotal += $transaction->balance;
                }
            }

            $totalCustomerDue = $sales->sum('change_amount') - $sales->sum('paid');
            $totalSupplierDue = $purchase->sum('sub_total') - $purchase->sum('paid');
        } else {
            $sales = Sale::where('branch_id', Auth::user()->branch_id)->get();
            $purchase = Purchase::where('branch_id', Auth::user()->branch_id)->get();
            $expanse = Expense::where('branch_id', Auth::user()->branch_id)->get();
            $salary = EmployeeSalary::where('branch_id', Auth::user()->branch_id)->get();
            $bankLabels = [];
            $grandTotal = 0;
            foreach ($banks as $bank) {
                $transaction = AccountTransaction::where('branch_id', Auth::user()->branch_id)
                    ->where('account_id', $bank->id)
                    ->latest('created_at')
                    ->first();
                // dd($transaction);
                if ($transaction) {
                    $bankData = [
                        'name' => $bank->name,
                        'amount' => number_format($transaction->balance, 2), // Accessing the balance attribute
                    ];
                    array_push($bankLabels, $bankData);
                    $grandTotal += $transaction->balance;
                }
            }

            $totalCustomerDue = $sales->sum('change_amount') - $sales->sum('paid');
            $totalSupplierDue = $purchase->sum('sub_total') - $purchase->sum('paid');
        }

        // weekly update Chart
        $salesByDay = [];
        $salesProfitByDay = [];
        $purchaseByDay = [];
        if (Auth::user()->id == 1) {

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                $dailySales = Sale::whereDate('sale_date', $date)->sum('receivable');
                $dailyProfit = Sale::whereDate('sale_date', $date)->sum('profit');
                $dailyPurchase = Purchase::whereDate('purchase_date', $date)->sum('grand_total');

                $salesByDay[$date] = $dailySales;
                $salesProfitByDay[$date] = $dailyProfit;
                $purchaseByDay[$date] = $dailyPurchase;
            }
        } else {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                $dailySales = Sale::where('branch_id', Auth::user()->branch_id)
                    ->whereDate('sale_date', $date)->sum('receivable');
                $dailyProfit = Sale::where('branch_id', Auth::user()->branch_id)
                    ->whereDate('sale_date', $date)->sum('profit');
                $dailyPurchase = Purchase::where('branch_id', Auth::user()->branch_id)
                    ->whereDate('purchase_date', $date)->sum('grand_total');

                $salesByDay[$date] = $dailySales;
                $salesProfitByDay[$date] = $dailyProfit;
                $purchaseByDay[$date] = $dailyPurchase;
            }
        }
        // monthly update chart
        $salesByMonth = [];
        $profitsByMonth = [];
        $purchasesByMonth = [];
        if (Auth::user()->id == 1) {
            for ($i = 0; $i < 12; $i++) {
                $monthStart = now()->subMonths($i)->startOfMonth();
                $monthEnd = now()->subMonths($i)->endOfMonth();

                $monthlySales = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])->sum('receivable');
                $monthlyProfit = Sale::whereBetween('sale_date', [$monthStart, $monthEnd])->sum('profit');
                $monthlyPurchase = Purchase::whereBetween('purchase_date', [$monthStart, $monthEnd])->sum(
                    'grand_total',
                );

                $salesByMonth[$monthStart->format('Y-m')] = $monthlySales;
                $profitsByMonth[$monthStart->format('Y-m')] = $monthlyProfit;
                $purchasesByMonth[$monthStart->format('Y-m')] = $monthlyPurchase;
            }
        } else {
            for ($i = 0; $i < 12; $i++) {
                $monthStart = now()->subMonths($i)->startOfMonth();
                $monthEnd = now()->subMonths($i)->endOfMonth();

                $monthlySales = Sale::where('branch_id', Auth::user()->branch_id)
                    ->whereBetween('sale_date', [$monthStart, $monthEnd])->sum('receivable');
                $monthlyProfit = Sale::where('branch_id', Auth::user()->branch_id)
                    ->whereBetween('sale_date', [$monthStart, $monthEnd])->sum('profit');
                $monthlyPurchase = Purchase::where('branch_id', Auth::user()->branch_id)
                    ->whereBetween('purchase_date', [$monthStart, $monthEnd])->sum(
                        'grand_total',
                    );

                $salesByMonth[$monthStart->format('Y-m')] = $monthlySales;
                $profitsByMonth[$monthStart->format('Y-m')] = $monthlyProfit;
                $purchasesByMonth[$monthStart->format('Y-m')] = $monthlyPurchase;
            }
        }
        return view('dashboard.dashboard', compact(
            // today summary 
            'branchData',
            'totalIngoing',
            'previousDayBalance',
            'todaySales',
            'dueCollection',
            'otherCollection',
            'addBalance',
            'adjustDueCollection',
            'viaSale',
            'totalOutgoing',
            'todayPurchase',
            'todayExpanse',
            'todayEmployeeSalary',
            'todayReturnAmount',
            'purchaseDuePay',
            'otherPaid',
            'viaPayment',

            // today Total Summary 
            'todayTotalIngoing',
            'previousDayTotalBalance',
            'todayTotalSales',
            'todayTotalDueCollection',
            'todayTotalOtherCollection',
            'todayTotalAddBalance',
            'todayTotalAdjustDueCollection',
            'todayTotalViaSale',
            'todayTotalOutgoing',
            'todayTotalPurchase',
            'todayTotalExpanse',
            'todayTotalEmployeeSalary',
            'todayTotalReturnAmount',
            'todayTotalPurchaseDuePay',
            'todayTotalOtherPaid',
            'todayTotalViaPayment',

            // total Summary 
            'sales',
            'purchase',
            'expanse',
            'salary',
            'bankLabels',
            'grandTotal',
            'totalCustomerDue',
            'totalSupplierDue',

            // weekly summary 
            'salesByDay',
            'salesProfitByDay',
            'purchaseByDay',

            // monthly summary 
            'salesByMonth',
            'profitsByMonth',
            'purchasesByMonth',
        ));
    }
}
