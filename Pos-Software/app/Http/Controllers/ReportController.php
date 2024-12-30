<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\EmployeeSalary;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Employee;
use App\Models\SubCategory;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Sms;
use App\Models\Damage;
use Illuminate\Http\Request;
use App\Models\AccountTransaction;
use App\Models\Bank;
use App\Models\Branch;
use App\Models\Returns;
use App\Models\ViaSale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    // today report function
    public function todayReport()
    {

        $todayDate = now()->toDateString();

        $branchData = [];
        //Today Invoice
        if (Auth::user()->id == 1) {
            $branches = Branch::all();

            foreach ($branches as $branch) {
                $branchId = $branch->id;
                $todayInvoiceAmount = Sale::whereDate('sale_date', $todayDate)->where('branch_id', $branchId)->sum('receivable');
                $today_grand_total = Purchase::whereDate('purchase_date', $todayDate)->where('branch_id', $branchId)->sum('grand_total');
                $todayExpenseAmount = Expense::whereDate('expense_date', $todayDate)->where('branch_id', $branchId)->sum('amount');
                $totalSalary = EmployeeSalary::whereDate('created_at', $todayDate)->where('branch_id', $branchId)->sum('debit');
                $branchData[$branchId] = [
                    'todayInvoiceAmount' => $todayInvoiceAmount,
                    'today_grand_total' => $today_grand_total,
                    'todayExpenseAmount' => $todayExpenseAmount,
                    'totalSalary' => $totalSalary,
                    'branch' => $branch,
                ];
            }
            $saleItemsForDate = SaleItem::whereDate('created_at', $todayDate);
            $todaySaleItemsToday = $saleItemsForDate->sum('qty');
            $totalInvoiceToday = Sale::whereDate('sale_date', $todayDate)->count();
            $totalSales = Sale::whereDate('sale_date', $todayDate)->get();
            $todayTotalSaleAmount = Sale::whereDate('sale_date', $todayDate)->sum('receivable');
            $todayTotalSaleQty = Sale::whereDate('sale_date', $todayDate)->sum('quantity');
            $todayTotalSaleDue = Sale::whereDate('sale_date', $todayDate)->sum('due');
            
             // Today sales cash report
            $totalSaleCashReport = Transaction::where('customer_id', '!=', null)
                ->whereDate('created_at', $todayDate)
                ->get();

            //Today Purchase
            $todayPurchaseItems = PurchaseItem::whereDate('created_at', $todayDate);
            $purchases = Purchase::whereDate('created_at', $todayDate)->get();
            $todayPurchaseItemsToday = $todayPurchaseItems->sum('quantity');
            // $todayPurchaseToday = Purchase::whereDate('purchase_date', $todayDate)->get();
            // dd($todayPurchaseToday);
            // $today_grand_total = $todayPurchaseToday->sum('grand_total');
            $todayTotalPurchaseAmount = Purchase::whereDate('purchase_date', $todayDate)->sum('grand_total');
            $todayTotalPurchaseQty = Purchase::whereDate('purchase_date', $todayDate)->sum('total_quantity');
            $todayTotalPurchaseDue = Purchase::whereDate('purchase_date', $todayDate)->sum('due');

            //Today invoice product
            $todayInvoiceProductItems = Sale::whereDate('sale_date', $todayDate);
            $todayInvoiceProductTotal = $todayInvoiceProductItems->sum('quantity');
            $todayInvoiceProductAmount = $todayInvoiceProductItems->sum('final_receivable');
            //today invoice amount
            $totalInvoiceTodaySum = Sale::whereDate('sale_date', $todayDate);
            // $todayInvoiceAmount = $totalInvoiceTodaySum->sum('receivable');
            $todayProfit = $totalInvoiceTodaySum->sum('profit');
            //today expenses
            // $todayExpenseDate = Expense::whereDate('expense_date', $todayDate);
            // $todayExpenseAmount = $todayExpenseDate->sum('amount');
            //Today Customer
            $todayCustomer = Customer::whereDate('created_at', $todayDate);
            //Sale Profit
            $saleProfitAmount = $totalInvoiceTodaySum->sum('profit');

            $expense = Expense::whereDate('expense_date', $todayDate)->get();
            $expenseAmount = $expense->sum('amount');
            $salary = EmployeeSalary::whereDate('created_at', $todayDate)->get();
            // $totalSalary = $salary->sum('debit');
            $totalSalaryDue = $salary->sum('balance');
        } else {
            //for Branch
            $saleItemsForDate = SaleItem::whereHas('saleId', function ($query) {
                $query->where('branch_id', Auth::user()->branch_id);
            })
                ->whereDate('created_at', $todayDate);
            $todaySaleItemsToday = $saleItemsForDate->sum('qty');
            $totalInvoiceToday = Sale::where('branch_id', Auth::user()->branch_id)->whereDate('sale_date', $todayDate)->count();
            $totalSales = Sale::where('branch_id', Auth::user()->branch_id)
                ->whereDate('sale_date', $todayDate)->get();
            $todayTotalSaleAmount = Sale::where('branch_id', Auth::user()->branch_id)
                ->whereDate('sale_date', $todayDate)->sum('receivable');
            $todayTotalSaleQty = Sale::where('branch_id', Auth::user()->branch_id)
                ->whereDate('sale_date', $todayDate)->sum('quantity');
            $todayTotalSaleDue = Sale::where('branch_id', Auth::user()->branch_id)
                ->whereDate('sale_date', $todayDate)->sum('due');
                
            // Today sales cash report
            $totalSaleCashReport = Transaction::where('branch_id', Auth::user()->branch_id)->where('customer_id', '!=', null)
                ->whereDate('created_at', $todayDate)
                ->get();

            //Today Purchase
            $todayPurchaseItems = PurchaseItem::whereHas('Purchas', function ($query) {
                $query->where('branch_id', Auth::user()->branch_id);
            })
                ->whereDate('created_at', $todayDate);
            $purchases = Purchase::whereDate('created_at', $todayDate)->get();
            $todayPurchaseItemsToday = $todayPurchaseItems->sum('quantity');
            $todayPurchaseToday = Purchase::where('branch_id', Auth::user()->branch_id)
                ->whereDate('purchase_date', $todayDate)->get();
            // dd($todayPurchaseToday);
            $today_grand_total = $todayPurchaseToday->sum('grand_total');
            $todayTotalPurchaseAmount = Purchase::where('branch_id', Auth::user()->branch_id)
                ->whereDate('purchase_date', $todayDate)->sum('grand_total');
            $todayTotalPurchaseQty = Purchase::where('branch_id', Auth::user()->branch_id)
                ->whereDate('purchase_date', $todayDate)->sum('total_quantity');
            $todayTotalPurchaseDue = Purchase::whereDate('purchase_date', $todayDate)->sum('due');

            //Today invoice product
            $todayInvoiceProductItems = Sale::where('branch_id', Auth::user()->branch_id)
                ->whereDate('sale_date', $todayDate);
            $todayInvoiceProductTotal = $todayInvoiceProductItems->sum('quantity');
            $todayInvoiceProductAmount = $todayInvoiceProductItems->sum('final_receivable');
            //today invoice amount
            $totalInvoiceTodaySum = Sale::where('branch_id', Auth::user()->branch_id)
                ->whereDate('sale_date', $todayDate);
            $todayInvoiceAmount = $totalInvoiceTodaySum->sum('receivable');
            $todayProfit = $totalInvoiceTodaySum->sum('profit');
            //today expenses
            $todayExpenseDate = Expense::where('branch_id', Auth::user()->branch_id)
                ->whereDate('expense_date', $todayDate);
            $todayExpenseAmount = $todayExpenseDate->sum('amount');
            //Today Customer//
            $todayCustomer = Customer::where('branch_id', Auth::user()->branch_id)
                ->whereDate('created_at', $todayDate);
            //Sale Profit
            $saleProfitAmount = $totalInvoiceTodaySum->sum('profit');

            $expense = Expense::where('branch_id', Auth::user()->branch_id)
                ->whereDate('expense_date', $todayDate)->get();
            $expenseAmount = $expense->sum('amount');
            $salary = EmployeeSalary::where('branch_id', Auth::user()->branch_id)
                ->whereDate('created_at', $todayDate)->get();
            $totalSalary = $salary->sum('debit');
            $totalSalaryDue = $salary->sum('balance');
        }
        return view('pos.report.today.today', compact('todayInvoiceAmount', 'totalSales', 'today_grand_total', 'todayExpenseAmount', 'totalSalary', 'expense', 'todayTotalSaleAmount', 'todayTotalSaleDue', 'todayTotalSaleQty', 'purchases', 'todayTotalPurchaseDue', 'todayTotalPurchaseQty', 'todayTotalPurchaseAmount', 'salary', 'branchData', 'totalSaleCashReport'));
    }
    // summary report function
    public function summaryReport()
    {
        $branchData = [];
        if (Auth::user()->id == 1) {
            $branches = Branch::all();
            foreach ($branches as $branch) {
                $branchId = $branch->id;

                $sale = Sale::where('branch_id', $branchId)->get();
                $saleAmount = $sale->sum('receivable');
                $purchase = Purchase::where('branch_id', $branchId)->get();
                $purchaseAmount = $purchase->sum('grand_total');
                $expense =  Expense::where('branch_id', $branchId)->get();
                $expenseAmount = $expense->sum('amount');
                $sellProfit = $sale->sum('profit');
                $salary = EmployeeSalary::where('branch_id', $branchId)->get();
                $totalSalary = $salary->sum('debit');

                $branchData[$branchId] = [
                    'saleAmount' => $saleAmount,
                    'purchaseAmount' => $purchaseAmount,
                    'sellProfit' => $sellProfit,
                    'expenseAmount' => $expenseAmount,
                    'totalSalary' => $totalSalary,
                    'branch' => $branch,
                ];
            }
            $products = Product::orderBy('total_sold', 'desc')
                ->take(20)
                ->get();
            $expense =  Expense::all();
            $supplier = Transaction::whereNotNull('supplier_id')->get();
            $customer = Transaction::whereNotNull('customer_id')->get();
        } else {
            $products = Product::where('branch_id', Auth::user()->branch_id)
                ->orderBy('total_sold', 'desc')
                ->take(20)
                ->get();
            // $expense =  Expense::all();
            $supplier = Transaction::where('branch_id', Auth::user()->branch_id)->whereNotNull('supplier_id')->get();
            $customer = Transaction::where('branch_id', Auth::user()->branch_id)->whereNotNull('customer_id')->get();
            $sale = Sale::where('branch_id', Auth::user()->branch_id)->get();
            $saleAmount = $sale->sum('receivable');
            $purchase = Purchase::where('branch_id', Auth::user()->branch_id)->get();
            $purchaseAmount = $purchase->sum('grand_total');
            $expense = Expense::where('branch_id', Auth::user()->branch_id)->get();
            $expenseAmount = $expense->sum('amount');
            $sellProfit = $sale->sum('profit');
            $salary = EmployeeSalary::where('branch_id', Auth::user()->branch_id)->get();
            $totalSalary = $salary->sum('debit');
        }
        return view('pos.report.summary.summary', compact('saleAmount', 'purchaseAmount', 'expenseAmount', 'sellProfit', 'totalSalary', 'products', 'expense', 'supplier', 'customer', 'branchData'));
    }
    // customer due report function
    public function customerDue()
    {
        if (Auth::user()->id == 1) {
            $customer = Customer::where('wallet_balance', '>', 0)
                ->get();
        } else {
            $customer = Customer::where('branch_id', Auth::user()->branch_id)
                ->where('wallet_balance', '>', 0)
                ->get();
        }
        return view('pos.report.customer.customer_due', compact('customer'));
    }
    function damageReportPrint(Request $request)
    {
        // dd($request->all());

        $damageItem = Damage::when($request->startdatepurches && $request->enddatepurches, function ($query) use ($request) {
            return $query->whereBetween('date', [$request->startdatepurches, $request->enddatepurches]);
        })
            ->when($request->filterProduct, function ($query) use ($request) {
                return $query->where('product_id', $request->filterProduct);
            })
            ->when($request->branchId, function ($query) use ($request) {
                return $query->where('branch_id', $request->branchId);
            })
            ->get();

        if ($damageItem->isEmpty()) {
            $damageItem = Damage::all();
        }

        return view('pos.report.damages.print', compact('damageItem'));
    }
    // customer due filter function
    public function customerDueFilter(Request $request)
    {
        if (Auth::user()->id == 1) {
            $customer = Customer::where('id', $request->customerId)->get();
        } else {
            $customer = Customer::where('branch_id', Auth::user()->branch_id)->where('id', $request->customerId)->get();
        }
        return view("pos.report.customer.table", compact('customer'))->render();
    }
    // supplier due report function
    public function supplierDueReport()
    {
        if (Auth::user()->id == 1) {
            $customer = Supplier::where('wallet_balance', '>', 0)
                ->get();
        } else {
            $customer = Supplier::where('branch_id', Auth::user()->branch_id)
                ->where('wallet_balance', '>', 0)
                ->get();
        }
        return view('pos.report.supplier.supplier_due', compact('customer'));
    }
    // supplier due filter function
    public function supplierDueFilter(Request $request)
    {
        if (Auth::user()->id == 1) {
            $customer = Supplier::where('id', $request->customerId)->get();
        } else {
            $customer = Supplier::where('branch_id', Auth::user()->branch_id)
                ->where('id', $request->customerId)->get();
        }
        return view("pos.report.customer.table", compact('customer'))->render();
    }
    // low stock report function
    public function lowStockReport()
    {
        if (Auth::user()->id == 1) {
            $products = Product::where('stock', '<=', 10)
                ->get();
        } else {
            $products = Product::where('branch_id', Auth::user()->branch_id)
                ->where('stock', '<=', 10)
                ->get();
        }
        return view('pos.report.products.low_stock', compact('products'));
    }
    // Top Products  function
    public function topProducts()
    {
        if (Auth::user()->id == 1) {
            $products = Product::orderBy('total_sold', 'desc')
                ->take(20)
                ->get();
        } else {
            $products = Product::where('branch_id', Auth::user()->branch_id)
                ->orderBy('total_sold', 'desc')
                ->take(20)
                ->get();
        }
        return view('pos.report.products.top_products', compact('products'));
    }

    // purchase Report function
    public function purchaseReport()
    {
        if (Auth::user()->id == 1) {
            $purchaseItem = PurchaseItem::all();
        } else {
            $purchaseItem = PurchaseItem::whereHas('purchas', function ($query) {
                $query->where('branch_id', Auth::user()->branch_id);
            })->get();
        }
        return view('pos.report.purchase.purchase', compact('purchaseItem'));
    }

    public function PurchaseProductFilter(Request $request)
    {

        $purchaseItem = PurchaseItem::when($request->filterProduct, function ($query) use ($request) {
            return $query->where('product_id', $request->filterProduct);
        })

            ->when($request->startDatePurches && $request->endDatePurches, function ($query) use ($request) {
                $query->whereHas('Purchas', function ($query) use ($request) {
                    return $query->whereBetween('purchase_date', [$request->startDatePurches, $request->endDatePurches]);
                });
            })
            // ->when($request->startDate && $request->endDate, function ($query) use ($request) {
            //     return $query->whereBetween('purchase_date', [$request->startDate, $request->endDate]);
            // })
            ->get();
        return view('pos.report.purchase.purchase-filter-table', compact('purchaseItem'))->render();
    } //
    public function PurchaseDetailsInvoice($id)
    {
        $purchase = Purchase::findOrFail($id);
        return view('pos.report.purchase.purchase_invoice', compact('purchase'));
        return view('pos.report.purchase.purchase');
    }

    //damage reports starting

    public function damageReport()
    {
        if (Auth::user()->id == 1) {
            $damageItem = Damage::all();
        } else {
            $damageItem = Damage::where('branch_id', Auth::user()->branch_id)->get();
        }
        return view('pos.report.damages.damage', compact('damageItem'));
    }

    public function DamageProductFilter(Request $request)
    {
        // dd($request);
        $damageItem = Damage::when($request->startDatePurches && $request->endDatePurches, function ($query) use ($request) {
            return $query->whereBetween('date', [$request->startDatePurches, $request->endDatePurches]);
        })
            ->when($request->filterProduct != "Select Product", function ($query) use ($request) {
                return $query->where('product_id', $request->filterProduct);
            })
            ->when($request->branchId != "Select Branch", function ($query) use ($request) {
                return $query->where('branch_id', $request->branchId);
            })
            ->get();
        return view('pos.report.damages.damage-filter-table', compact('damageItem'))->render();
    } //

    // customer Ledger report function
    public function customerLedger()
    {
        return view('pos.report.customer.customer_ledger');
    }
    // customer Ledger Filter function
    public function customerLedgerFilter(Request $request)
    {
        $transactionQuery = Transaction::query();
        // Filter by supplier_id if provided
        if ($request->customerId != "Select Customer") {
            $transactionQuery->where('customer_id', $request->customerId);
        }
        // Filter by date range if both start_date and end_date are provided
        if ($request->startDate && $request->endDate) {
            $transactionQuery->whereBetween('date', [$request->startDate, $request->endDate]);
        }
        $transactions = $transactionQuery->get();
        $customer = Customer::findOrFail($request->customerId);
        return response()->json([
            'status' => 200,
            'transactions' => $transactions,
            'customer' => $customer,
        ]);
        // return view("pos.report.supplier.show_ledger", compact('supplier', 'transactions'))->render();
    }
    // supplier Ledger report function
    public function supplierLedger()
    {
        return view('pos.report.supplier.supplier_ledger');
    }
    // supplier Ledger Filter function
    public function supplierLedgerFilter(Request $request)
    {
        $transactionQuery = Transaction::query();
        // Filter by supplier_id if provided
        if ($request->supplierId != "Select Supplier") {
            $transactionQuery->where('supplier_id', $request->supplierId);
        }
        // Filter by date range if both start_date and end_date are provided
        if ($request->startDate && $request->endDate) {
            $transactionQuery->whereBetween('date', [$request->startDate, $request->endDate]);
        }
        $transactions = $transactionQuery->get();
        $supplier = Supplier::findOrFail($request->supplierId);
        return response()->json([
            'status' => 200,
            'transactions' => $transactions,
            'supplier' => $supplier,
        ]);

        // return view("pos.report.supplier.show_ledger", compact('supplier', 'transactions'))->render();
    }
    // bank Report function
    public function bankReport()
    {
        return view('pos.report.bank.bank');
    }
    //stock Report function
    public function stockReport()
    {
        if (Auth::user()->id == 1) {
            $products = Product::all();
        } else {
            $products = Product::where('branch_id', Auth::user()->branch_id)->get();
        }
        return view('pos.report.products.stock', compact('products'));
    } //

    ////////////////Account Transaction Method  //////////////
    public function AccountTransactionView()
    {
        $accountTransaction = AccountTransaction::latest()->get();
        return view('pos.report.account_transaction.account_transaction_ledger', compact('accountTransaction'));
    }

    public function AccountTransactionFilter(Request $request)
    {
        // dd($request->all());
        $accountTransaction = AccountTransaction::when($request->accountId, function ($query) use ($request) {
            return $query->where('account_id', $request->accountId);
        })
            ->when($request->startDate && $request->endDate, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
            })
            ->get();
        return view('pos.report.account_transaction.account_transaction_table', compact('accountTransaction'))->render();
    }
    //////////////////Rexpense Report MEthod //////////////
    public function ExpenseReport()
    {
        if (Auth::user()->id == 1) {
            $expense = Expense::latest()->get();
        } else {
            $expense = Expense::where('branch_id', Auth::user()->branch_id)->get();
        }
        return view('pos.report.expense.expense', compact('expense'));
    } //
    public function ExpenseReportFilter(Request $request)
    {
        //dd($request->all());
        $expense = Expense::when($request->startDate && $request->endDate, function ($query) use ($request) {
            return $query->whereBetween('expense_date', [$request->startDate, $request->endDate]);
        })->get();
        return view('pos.report.expense.expense-table', compact('expense'))->render();
    }
    //////////////////Employee Salary Report MEthod //////////////
    public function EmployeeSalaryReport()
    {
        if (Auth::user()->id == 1) {
            $employeeSalary = EmployeeSalary::all();
        } else {
            $employeeSalary = EmployeeSalary::where('branch_id', Auth::user()->branch_id)->get();
        }
        return view('pos.report.employee_salary.employee_salary', compact('employeeSalary'));
    } //
    public function EmployeeSalaryReportFilter(Request $request)
    {
        $employeeSalary = EmployeeSalary::when($request->salaryId, function ($query) use ($request) {
            return $query->where('employee_id', $request->salaryId);
        })
            ->when($request->startDate && $request->endDate, function ($query) use ($request) {
                return $query->whereBetween('date', [$request->startDate, $request->endDate]);
            })
            ->get();
        return view('pos.report.employee_salary.employee_salary-table', compact('employeeSalary'))->render();
    }
    ////////Product Info Report /////
    public function ProductInfoReport()
    {
        if (Auth::user()->id == 1) {
            $productInfo = Product::all();
        } else {
            $productInfo = Product::where('branch_id', Auth::user()->branch_id)->latest()->get();
        }

        return view('pos.report.products.product_info_report', compact('productInfo'));
    } //
    // public function ProductSubCategoryShow($categoryId){
    //     $subCategory =SubCategory::where('category_id',$categoryId)->get();
    //     return  json_encode($subCategory);
    // }

    public function ProductInfoFilter(Request $request)
    {
        // dd($request->filterBrand);
        $productInfo = Product::when($request->filterStartPrice, function ($query) use ($request) {
            return $query->where('price', '<=', (float) $request->filterStartPrice);
        })
            ->when($request->filterBrand != "Select Brand", function ($query) use ($request) {
                return $query->where('brand_id', $request->filterBrand);
            })
            ->when($request->FilterCat != "Select Category", function ($query) use ($request) {
                return $query->where('category_id', $request->FilterCat);
            })
            ->when($request->filterSubcat != "Select Sub Category", function ($query) use ($request) {
                return $query->where('subcategory_id', $request->filterSubcat);
            })
            ->get();
        return view('pos.report.products.product-info-filter-rander-table', compact('productInfo'))->render();
    }
    ///SMS Report Method
    public function SmsView()
    {
        $smsAll = Sms::all();
        return view('pos.report.sms.sms_report', compact('smsAll'));
    } //
    public function SmsReportFilter(Request $request)
    {
        $smsAll = Sms::when($request->customerId != "Select Customer", function ($query) use ($request) {
            return $query->where('customer_id', $request->customerId);
        })
            ->when($request->startDate && $request->endDate, function ($query) use ($request) {
                return $query->whereBetween('created_at', [$request->startDate, $request->endDate]);
            })
            ->get();
        return view('pos.report.sms.sms-filter-table', compact('smsAll'))->render();
    }
    public function monthlyReport()
    {
        $dailyReports = [];
        $banks = Bank::all();
        if (Auth::user()->id == 1) {
            for ($i = 0; $i < 30; $i++) { // Loop for the last 30 days
                // Calculate the start and end dates for the day
                $date = now()->subDays($i)->toDateString();

                // Calculate the totals for the day
                //   incoming value
                $viaSale = ViaSale::whereDate('created_at', $date)->sum('sub_total');
                $totalSaleAmount = Sale::whereDate('sale_date', $date)->sum('paid');
                $totalSale = $totalSaleAmount - $viaSale;
                $dueCollection = Transaction::where('particulars', 'SaleDue')
                    ->whereDate('created_at', $date)
                    ->sum('credit');
                $otherCollection = Transaction::where('particulars', 'OthersReceive')
                    ->whereDate('created_at', $date)
                    ->sum('credit');
                $adjustDueCollection = Transaction::where('particulars', 'Adjust Due Collection')
                    ->where('payment_type', 'pay')
                    ->whereDate('created_at',  $date)
                    ->sum('credit');
                $addBalance = AccountTransaction::where(function($query) {
                    $query->where('purpose', 'Add Bank Balance')
                          ->orWhere('purpose', 'Bank');
                    })
                    ->whereDate('created_at',  $date)
                    ->sum('credit');
                $previousDayBalance = 0;
                foreach ($banks as $bank) {
                    $transaction = AccountTransaction::where('account_id', $bank->id)
                        ->whereDate('created_at', '<', $date)
                        ->latest('created_at')
                        ->first();

                    if ($transaction) {
                        $previousDayBalance += $transaction->balance;
                    }
                }

                $totalIngoing =
                    $previousDayBalance +
                    $totalSale +
                    $dueCollection +
                    $otherCollection +
                    $addBalance +
                    $adjustDueCollection +
                    $viaSale;

                // outgoing Value//
                $totalPurchaseCost = Purchase::whereDate('purchase_date', $date)->sum('paid');
                $totalExpense = Expense::whereDate('expense_date', $date)->sum('amount');
                $totalSalary = EmployeeSalary::whereDate('date', $date)->sum('debit');
                $purchaseDuePay = Transaction::where('particulars', 'PurchaseDue')
                    ->whereDate('created_at', $date)
                    ->sum('debit');
                $otherPaid = Transaction::where('particulars', 'OthersPayment')
                    ->whereDate('created_at', $date)
                    ->sum('debit');
                $viaPayment = AccountTransaction::where('purpose', 'Via Payment')
                    ->whereDate('created_at', $date)
                    ->sum('debit');
                $return = Returns::whereDate('created_at', $date)->sum('refund_amount');
                $todayReturnAmount = $return - $adjustDueCollection;

                $totalOutgoing =
                    $totalPurchaseCost +
                    $totalExpense +
                    $totalSalary +
                    $todayReturnAmount +
                    $purchaseDuePay +
                    $otherPaid +
                    $viaPayment;

                // profit Calculation//
                $totalProfit = Sale::whereDate('sale_date', $date)->sum('profit');
                $finalProfit = $totalProfit - ($totalExpense + $totalSalary);
                $totalBalance = $totalIngoing - $totalOutgoing;
                $dayName = now()->subDays($i)->format('d F Y');
                // Store the report data in the array
                $dailyReports[now()->subDays($i)->format('Y-m-d')] = [
                    'id' => now()->subDays($i)->format('Ymd'),
                    'date' => $dayName,
                    // incoming
                    'totalSale' => $totalSale,
                    'dueCollection' => $dueCollection,
                    'otherCollection' => $otherCollection,
                    'adjustDueCollection' => $adjustDueCollection,
                    'addBalance' => $addBalance,
                    'viaSale' => $viaSale,
                    'previousDayBalance' => $previousDayBalance,
                    'totalIngoing' => $totalIngoing,

                    // outgoing
                    'totalPurchaseCost' => $totalPurchaseCost,
                    'totalExpense' => $totalExpense,
                    'totalSalary' => $totalSalary,
                    'purchaseDuePay' => $purchaseDuePay,
                    'todayReturnAmount' => $todayReturnAmount,
                    'viaPayment' => $viaPayment,
                    'otherPaid' => $otherPaid,
                    'totalOutgoing' => $totalOutgoing,

                    // profit
                    'totalProfit' => $totalProfit,
                    'finalProfit' => $finalProfit,
                    'totalBalance' => $totalBalance,
                ];
            }
        } else {
            for ($i = 0; $i < 30; $i++) { // Loop for the last 30 days
                // Calculate the start and end dates for the day
                $date = now()->subDays($i)->toDateString();

                // Calculate the totals for the day
                //   incoming value
                $viaSale = ViaSale::where('branch_id', Auth::user()->branch_id)
                    ->whereDate('created_at', $date)->sum('sub_total');
                $totalSaleAmount = Sale::where('branch_id', Auth::user()->branch_id)
                    ->whereDate('sale_date', $date)->sum('paid');
                $totalSale = $totalSaleAmount - $viaSale;
                $dueCollection = Transaction::where('branch_id', Auth::user()->branch_id)
                    ->where('particulars', 'SaleDue')
                    ->whereDate('created_at', $date)
                    ->sum('credit');
                $otherCollection = Transaction::where('branch_id', Auth::user()->branch_id)
                    ->where('particulars', 'OthersReceive')
                    ->whereDate('created_at', $date)
                    ->sum('credit');
                $adjustDueCollection = Transaction::where('branch_id', Auth::user()->branch_id)
                    ->where('particulars', 'Adjust Due Collection')
                    ->where('payment_type', 'pay')
                    ->whereDate('created_at',  $date)
                    ->sum('credit');
                $addBalance = AccountTransaction::where('branch_id', Auth::user()->branch_id)
                    ->where(function($query) {
                    $query->where('purpose', 'Add Bank Balance')
                          ->orWhere('purpose', 'Bank');
                    })
                    ->whereDate('created_at',  $date)
                    ->sum('credit');
                $previousDayBalance = 0;
                foreach ($banks as $bank) {
                    $transaction = AccountTransaction::where('account_id', $bank->id)
                        ->whereDate('created_at', '<', $date)
                        ->latest('created_at')
                        ->first();

                    if ($transaction) {
                        $previousDayBalance += $transaction->balance;
                    }
                }

                $totalIngoing =
                    $previousDayBalance +
                    $totalSale +
                    $dueCollection +
                    $otherCollection +
                    $addBalance +
                    $adjustDueCollection +
                    $viaSale;

                // outgoing Value
                $totalPurchaseCost = Purchase::where('branch_id', Auth::user()->branch_id)
                    ->whereDate('purchase_date', $date)->sum('paid');
                $totalExpense = Expense::where('branch_id', Auth::user()->branch_id)
                    ->whereDate('expense_date', $date)->sum('amount');
                $totalSalary = EmployeeSalary::where('branch_id', Auth::user()->branch_id)
                    ->whereDate('date', $date)->sum('debit');
                $purchaseDuePay = Transaction::where('branch_id', Auth::user()->branch_id)
                    ->where('particulars', 'PurchaseDue')
                    ->whereDate('created_at', $date)
                    ->sum('debit');
                $otherPaid = Transaction::where('branch_id', Auth::user()->branch_id)
                    ->where('particulars', 'OthersPayment')
                    ->whereDate('created_at', $date)
                    ->sum('debit');
                $viaPayment = AccountTransaction::where('branch_id', Auth::user()->branch_id)
                    ->where('purpose', 'Via Payment')
                    ->whereDate('created_at', $date)
                    ->sum('debit');
                $return = Returns::where('branch_id', Auth::user()->branch_id)
                    ->whereDate('created_at', $date)->sum('refund_amount');
                $todayReturnAmount = $return - $adjustDueCollection;

                $totalOutgoing =
                    $totalPurchaseCost +
                    $totalExpense +
                    $totalSalary +
                    $todayReturnAmount +
                    $purchaseDuePay +
                    $otherPaid +
                    $viaPayment;

                // profit Calculation//
                $totalProfit = Sale::where('branch_id', Auth::user()->branch_id)
                    ->whereDate('sale_date', $date)->sum('profit');
                $finalProfit = $totalProfit - ($totalExpense + $totalSalary);
                $totalBalance = $totalIngoing - $totalOutgoing;
                $dayName = now()->subDays($i)->format('d F Y');
                // Store the report data in the array
                $dailyReports[now()->subDays($i)->format('Y-m-d')] = [
                    'id' => now()->subDays($i)->format('Ymd'),
                    'date' => $dayName,
                    // incoming
                    'totalSale' => $totalSale,
                    'dueCollection' => $dueCollection,
                    'otherCollection' => $otherCollection,
                    'adjustDueCollection' => $adjustDueCollection,
                    'addBalance' => $addBalance,
                    'viaSale' => $viaSale,
                    'previousDayBalance' => $previousDayBalance,
                    'totalIngoing' => $totalIngoing,

                    // outgoing
                    'totalPurchaseCost' => $totalPurchaseCost,
                    'totalExpense' => $totalExpense,
                    'totalSalary' => $totalSalary,
                    'purchaseDuePay' => $purchaseDuePay,
                    'todayReturnAmount' => $todayReturnAmount,
                    'viaPayment' => $viaPayment,
                    'otherPaid' => $otherPaid,
                    'totalOutgoing' => $totalOutgoing,

                    // profit
                    'totalProfit' => $totalProfit,
                    'finalProfit' => $finalProfit,
                    'totalBalance' => $totalBalance,
                ];
            }
        }


        // Pass the daily reports array to the view
        return view('pos.report.monthly.monthly', compact('dailyReports'));
    }

    public function monthlyReportView($id)
    {
        // dd($id);
        $date = \DateTime::createFromFormat('Ymd', $id);

        $banks = Bank::all();

        if (Auth::user()->id == 1) {
            //  Calculate the totals for the day //
            $viaSale = ViaSale::whereDate('created_at', $date)->sum('sub_total');
            $totalSaleAmount = Sale::whereDate('sale_date', $date)->sum('paid');
            $totalSale = $totalSaleAmount - $viaSale;
            $dueCollection = Transaction::where('particulars', 'SaleDue')
                ->whereDate('created_at', $date)
                ->sum('credit');
            $otherCollection = Transaction::where('particulars', 'OthersReceive')
                ->whereDate('created_at', $date)
                ->sum('credit');
            $adjustDueCollection = Transaction::where('particulars', 'Adjust Due Collection')
                ->where('payment_type', 'pay')
                ->whereDate('created_at',  $date)
                ->sum('credit');
            $addBalance = AccountTransaction::where(function($query) {
                    $query->where('purpose', 'Add Bank Balance')
                          ->orWhere('purpose', 'Bank');
                })
                ->whereDate('created_at',  $date)
                ->sum('credit');
            $previousDayBalance = 0;
            foreach ($banks as $bank) {
                $transaction = AccountTransaction::where('account_id', $bank->id)
                    ->whereDate('created_at', '<', $date)
                    ->latest('created_at')
                    ->first();

                if ($transaction) {
                    $previousDayBalance += $transaction->balance;
                }
            }

            $totalIngoing =
                $previousDayBalance +
                $totalSale +
                $dueCollection +
                $otherCollection +
                $addBalance +
                $adjustDueCollection +
                $viaSale;

            // outgoing Value
            $totalPurchaseCost = Purchase::whereDate('purchase_date', $date)->sum('paid');
            $totalExpense = Expense::whereDate('expense_date', $date)->sum('amount');
            $totalSalary = EmployeeSalary::whereDate('date', $date)->sum('debit');
            $purchaseDuePay = Transaction::where('particulars', 'PurchaseDue')
                ->whereDate('created_at', $date)
                ->sum('debit');
            $otherPaid = Transaction::where('particulars', 'OthersPayment')
                ->whereDate('created_at', $date)
                ->sum('debit');
            $viaPayment = AccountTransaction::where('purpose', 'Via Payment')
                ->whereDate('created_at', $date)
                ->sum('debit');
            $return = Returns::whereDate('created_at', $date)->sum('refund_amount');
            $todayReturnAmount = $return - $adjustDueCollection;

            $totalOutgoing =
                $totalPurchaseCost +
                $totalExpense +
                $totalSalary +
                $todayReturnAmount +
                $purchaseDuePay +
                $otherPaid +
                $viaPayment;

            // profit Calculation
            $totalProfit = Sale::whereDate('sale_date', $date)->sum('profit');
            $finalProfit = $totalProfit - ($totalExpense + $totalSalary);
            $totalBalance = $totalIngoing - $totalOutgoing;
        } else {
            //  Calculate the totals for the day //
            $viaSale = ViaSale::where('branch_id', Auth::user()->branch_id)
                ->whereDate('created_at', $date)->sum('sub_total');
            $totalSaleAmount = Sale::where('branch_id', Auth::user()->branch_id)
                ->whereDate('sale_date', $date)->sum('paid');
            $totalSale = $totalSaleAmount - $viaSale;
            $dueCollection = Transaction::where('branch_id', Auth::user()->branch_id)
                ->where('particulars', 'SaleDue')
                ->whereDate('created_at', $date)
                ->sum('credit');
            $otherCollection = Transaction::where('branch_id', Auth::user()->branch_id)
                ->where('particulars', 'OthersReceive')
                ->whereDate('created_at', $date)
                ->sum('credit');
            $adjustDueCollection = Transaction::where('branch_id', Auth::user()->branch_id)
                ->where('particulars', 'Adjust Due Collection')
                ->where('payment_type', 'pay')
                ->whereDate('created_at',  $date)
                ->sum('credit');
            $addBalance = AccountTransaction::where('branch_id', Auth::user()->branch_id)
                ->where(function($query) {
                    $query->where('purpose', 'Add Bank Balance')
                          ->orWhere('purpose', 'Bank');
                })
                ->whereDate('created_at',  $date)
                ->sum('credit');
            $previousDayBalance = 0;
            foreach ($banks as $bank) {
                $transaction = AccountTransaction::where('account_id', $bank->id)
                    ->whereDate('created_at', '<', $date)
                    ->latest('created_at')
                    ->first();

                if ($transaction) {
                    $previousDayBalance += $transaction->balance;
                }
            }

            $totalIngoing =
                $previousDayBalance +
                $totalSale +
                $dueCollection +
                $otherCollection +
                $addBalance +
                $adjustDueCollection +
                $viaSale;

            // outgoing Value
            $totalPurchaseCost = Purchase::where('branch_id', Auth::user()->branch_id)
                ->whereDate('purchase_date', $date)->sum('paid');
            $totalExpense = Expense::where('branch_id', Auth::user()->branch_id)
                ->whereDate('expense_date', $date)->sum('amount');
            $totalSalary = EmployeeSalary::where('branch_id', Auth::user()->branch_id)
                ->whereDate('date', $date)->sum('debit');
            $purchaseDuePay = Transaction::where('branch_id', Auth::user()->branch_id)
                ->where('particulars', 'PurchaseDue')
                ->whereDate('created_at', $date)
                ->sum('debit');
            $otherPaid = Transaction::where('branch_id', Auth::user()->branch_id)
                ->where('particulars', 'OthersPayment')
                ->whereDate('created_at', $date)
                ->sum('debit');
            $viaPayment = AccountTransaction::where('branch_id', Auth::user()->branch_id)
                ->where('purpose', 'Via Payment')
                ->whereDate('created_at', $date)
                ->sum('debit');
            $return = Returns::where('branch_id', Auth::user()->branch_id)
                ->whereDate('created_at', $date)->sum('refund_amount');
            $todayReturnAmount = $return - $adjustDueCollection;

            $totalOutgoing =
                $totalPurchaseCost +
                $totalExpense +
                $totalSalary +
                $todayReturnAmount +
                $purchaseDuePay +
                $otherPaid +
                $viaPayment;

            // profit Calculation //
            $totalProfit = Sale::where('branch_id', Auth::user()->branch_id)
                ->whereDate('sale_date', $date)->sum('profit');
            $finalProfit = $totalProfit - ($totalExpense + $totalSalary);
            $totalBalance = $totalIngoing - $totalOutgoing;
        }

        $formattedDate = $date->format('d F Y');
        $report = [
            'date' => $formattedDate,
            'totalSale' => $totalSale,
            'dueCollection' => $dueCollection,
            'otherCollection' => $otherCollection,
            'adjustDueCollection' => $adjustDueCollection,
            'addBalance' => $addBalance,
            'viaSale' => $viaSale,
            'previousDayBalance' => $previousDayBalance,
            'totalIngoing' => $totalIngoing,

            // outgoing
            'totalPurchaseCost' => $totalPurchaseCost,
            'totalExpense' => $totalExpense,
            'totalSalary' => $totalSalary,
            'purchaseDuePay' => $purchaseDuePay,
            'todayReturnAmount' => $todayReturnAmount,
            'viaPayment' => $viaPayment,
            'otherPaid' => $otherPaid,
            'totalOutgoing' => $totalOutgoing,

            // profit
            'totalProfit' => $totalProfit,
            'finalProfit' => $finalProfit,
            'totalBalance' => $totalBalance,
        ];
        return response()->json([
            'status' => '200',
            'report' => $report
        ]);
    }
    public function yearlyReport()
    {
        $monthlyReports = [];

        for ($i = 0; $i < 12; $i++) {
            // Calculate the start and end dates for the month
            $startOfMonth = now()->subMonths($i)->startOfMonth()->toDateString();
            $endOfMonth = now()->subMonths($i)->endOfMonth()->toDateString();

            // Calculate the totals for the month
            if (Auth::user()->id == 1) {
                $totalPurchaseCost = Purchase::whereBetween('purchase_date', [$startOfMonth, $endOfMonth])
                    ->sum('grand_total');
                $totalSale = Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])
                    ->sum('receivable');
                $totalProfit = Sale::whereBetween('sale_date', [$startOfMonth, $endOfMonth])
                    ->sum('profit');
                $totalExpense = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');
                $totalSalary = EmployeeSalary::whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->sum('debit');
                $finalProfit = $totalProfit - ($totalExpense + $totalSalary);
            } else {
                $totalPurchaseCost = Purchase::whereBetween('purchase_date', [$startOfMonth, $endOfMonth])
                    ->sum('grand_total');
                $totalSale = Sale::where('branch_id', Auth::user()->branch_id)
                    ->whereBetween('sale_date', [$startOfMonth, $endOfMonth])
                    ->sum('receivable');
                $totalProfit = Sale::where('branch_id', Auth::user()->branch_id)
                    ->whereBetween('sale_date', [$startOfMonth, $endOfMonth])
                    ->sum('profit');
                $totalExpense = Expense::where('branch_id', Auth::user()->branch_id)
                    ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
                    ->sum('amount');
                $totalSalary = EmployeeSalary::where('branch_id', Auth::user()->branch_id)
                    ->whereBetween('date', [$startOfMonth, $endOfMonth])
                    ->sum('debit');
                $finalProfit = $totalProfit - ($totalExpense + $totalSalary);
            }

            $monthName = now()->subMonths($i)->format('F Y');

            // Store the report data in the array
            $monthlyReports[now()->subMonths($i)->format('Y-m')] = [
                'month' => $monthName,
                'totalPurchaseCost' => $totalPurchaseCost,
                'totalSale' => $totalSale,
                'totalProfit' => $totalProfit,
                'totalExpense' => $totalExpense,
                'totalSalary' => $totalSalary,
                'totalSalary' => $totalSalary,
                'finalProfit' => $finalProfit
            ];
        }
        // Pass the monthly reports array to the view
        return view('pos.report.yearly.yearly', compact('monthlyReports'));
    }
}
