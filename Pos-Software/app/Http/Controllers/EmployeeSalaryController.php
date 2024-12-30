<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSalary;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Bank;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AccountTransaction;
use Illuminate\Support\Facades\Auth;
use function Laravel\Prompts\alert;

class EmployeeSalaryController extends Controller
{
    public function EmployeeSalaryAdd(Request $request)
    {
        // $employees = Employee::whereNotIn('id', function($query) {
        //     $query->select('employee_id')->from('employee_salaries')->whereYear('date', Carbon::now()->format('Y'))
        //     ->whereMonth('date', Carbon::now()->format('m'));;
        // })->get();
        $employees = Employee::latest()->get();
        $bank = Bank::latest()->get();
        $branch = Branch::latest()->get();
        return view('pos.employee_salary.add_employee_salary', compact('employees', 'branch', 'bank'));
    } //
    public function EmployeeSalaryStore(Request $request)
    {
        $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
        if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->debit) {
            $employeeSalary = new EmployeeSalary;
            $employeeSalary->employee_id =  $request->employee_id;
            $employeeSalary->branch_id =  $request->branch_id;
            $employeeSalary->date =  $request->date;
            $employee = Employee::findOrFail($request->employee_id);
            $employeeSalary->debit = $request->debit;
            $employeeSalary->creadit = $employee->salary;
            $employeeSalary->balance = $employee->salary - $request->debit;
            $employeeSalary->payment_method =  $request->payment_method;
            $employeeSalary->note =  $request->note;
            $employeeSalary->created_at = Carbon::now();
            $employeeSalary->save();

            //account Transaction Crud
            $accountTransaction = new AccountTransaction;
            $accountTransaction->branch_id =  Auth::user()->branch_id;
            $accountTransaction->reference_id = $employeeSalary->id;
            $accountTransaction->purpose =  'Employee Salary';
            $accountTransaction->account_id =  $request->payment_method;
            $accountTransaction->debit = $request->debit; // $request->Amount
            $accountTransaction->balance = $oldBalance->balance - $request->debit;
            $accountTransaction->created_at = Carbon::now();
            $accountTransaction->save();
            $notification = array(
                'message' => 'Employee Salary Send Successfully',
                'alert-type' => 'info'
            );
            return redirect()->route('employee.salary.view')->with($notification);
        } else {
            $notification = [
                'warning' => 'Your account Balance is low Please Select Another account',
                'alert-type' => 'warning'
            ];
            return redirect()->back()->with($notification);
        }
    }
    //
    public function EmployeeSalaryView()
    {
        $employeSalary = EmployeeSalary::all();
        return view('pos.employee_salary.view_employee_salary', compact('employeSalary'));
    } //
    public function EmployeeSalaryEdit($id)
    {
        $employeeSalary = EmployeeSalary::findOrFail($id);
        $employees = Employee::latest()->get();
        $bank = Bank::latest()->get();
        $branch = Branch::latest()->get();
        return view('pos.employee_salary.edit_employee_salary', compact('employeeSalary', 'employees', 'branch', 'bank'));
    } //Employee Salary Edit
    public function EmployeeSalaryUpdate(Request $request, $id)
    {
        // dd($request->all());
        $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
        if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->debit) {

            $employeeSalary = EmployeeSalary::findOrFail($id);
            $requiestDebit = $employeeSalary->debit = $employeeSalary->debit + $request->debit;
            $employeeSalary->date =  $request->date;
            $employeeSalary->balance = $employeeSalary->creadit - $requiestDebit;
            $employeeSalary->payment_method  = $request->payment_method;
            $employeeSalary->note  = $request->note;
            $employeeSalary->updated_at  = Carbon::now();
            $employeeSalary->update();
            //account Transaction Crud
            $accountTransaction = new AccountTransaction;
            $accountTransaction->branch_id =  Auth::user()->branch_id;
            $accountTransaction->reference_id = $employeeSalary->id;
            $accountTransaction->purpose =  'Employee Salary';
            $accountTransaction->account_id =  $request->payment_method;
            $accountTransaction->debit = $request->debit; // $request->Amount
            $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
            $accountTransaction->balance = $oldBalance->balance - $request->debit;
            $accountTransaction->created_at = Carbon::now();
            $accountTransaction->save();

            $notification = array(
                'message' => 'Employee Salary Update Successfully',
                'alert-type' => 'info'
            );
            return redirect()->route('employee.salary.view')->with($notification);
        } else {
            $notification = [
                'warning' => 'Your account Balance is low Please Select Another account',
                'alert-type' => 'warning'
            ];
            return redirect()->back()->with($notification);
        }
    } //
    public function EmployeeSalaryDelete($id)
    {
        EmployeeSalary::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Employee Salary Deleted Successfully',
            'alert-type' => 'info'
        );
        return redirect()->route('employee.salary.view')->with($notification);
    }

    ///////////////////////////Employee Salary Advanced //////////////////////////////

    public function EmployeeSalaryAdvancedAdd()
    {
        $employees = Employee::latest()->get();
        $bank = Bank::latest()->get();
        $branch = Branch::latest()->get();
        return view('pos.employee_salary.advanced_employee_salary_add', compact('employees', 'branch', 'bank'));
    } //End

    public function EmployeeSalaryAdvancedStore(Request $request)
    {
        $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
        if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->debit) {

            $requestMonth = Carbon::createFromFormat('Y-m-d', $request->date)->format('m');
            $requestYear = Carbon::createFromFormat('Y-m-d', $request->date)->format('Y');

            // Get the first and last day of the month
            $firstDayOfMonth = Carbon::create($requestYear, $requestMonth, 1)->startOfMonth();
            $lastDayOfMonth = Carbon::create($requestYear, $requestMonth, 1)->endOfMonth();
            $employeeSalary = EmployeeSalary::where('employee_id', $request->employee_id)
                ->where('branch_id', $request->branch_id)
                // ->where('date', $request->date)
                ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
                ->first();

            if ($employeeSalary) {
                if (!empty($employeeSalary) && (float) (($employeeSalary->balance - $request->debit) < 0)) {
                    $notification = [
                        'error' => 'This Month Full Advanced Already Paid Or Salary limit reached',
                        'alert-type' => 'error'
                    ];
                    return back()->with($notification);
                } else {
                    $employeeSalary->debit =  $employeeSalary->debit  +  $request->debit;
                    $employeeSalary->balance = $employeeSalary->balance - $request->debit;
                    $employeeSalary->update();
                    //account Transaction Crud
                    $accountTransaction = new AccountTransaction;
                    $accountTransaction->branch_id =  Auth::user()->branch_id;
                    $accountTransaction->reference_id = $employeeSalary->id;
                    $accountTransaction->purpose =  'Employee Salary';
                    $accountTransaction->account_id =  $request->payment_method;
                    $accountTransaction->debit = $request->debit; // $request->Amount
                    $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
                    $accountTransaction->balance = $oldBalance->balance - $request->debit;
                    $accountTransaction->created_at = Carbon::now();
                    $accountTransaction->save();
                    $notification = array(
                        'message' => 'Employee Salary Advanced Updated Successfully',
                        'alert-type' => 'info'
                    );
                    return redirect()->route('employee.salary.advanced.view')->with($notification);
                }
            } else {
                $employeeSalary = new EmployeeSalary;
                $employeeSalary->employee_id =  $request->employee_id;
                $employeeSalary->branch_id =  $request->branch_id;
                $requiestDebit =  $employeeSalary->debit =  $request->debit;
                $employeeSalary->date =  $request->date;
                $employee = Employee::findOrFail($request->employee_id);
                $employeeSalary->creadit = $employee->salary;
                $employeeSalary->balance = $employee->salary - $requiestDebit;
                $employeeSalary->payment_method =  $request->payment_method;
                $employeeSalary->note =  $request->note;
                $employeeSalary->created_at = Carbon::now();
                $employeeSalary->updated_at = NULL;
                $employeeSalary->save();
                //account Transaction Crud
                $accountTransaction = new AccountTransaction;
                $accountTransaction->branch_id =  Auth::user()->branch_id;
                $accountTransaction->reference_id = $employeeSalary->id;
                $accountTransaction->purpose =  'Employee Salary';
                $accountTransaction->account_id =  $request->payment_method;
                $accountTransaction->debit = $request->debit; // $request->Amount
                $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
                $accountTransaction->balance = $oldBalance->balance - $request->debit;
                $accountTransaction->created_at = Carbon::now();
                $accountTransaction->save();
                $notification = array(
                    'message' => 'Advanced Employee Salary Send Successfully',
                    'alert-type' => 'info'
                );
                return redirect()->route('employee.salary.advanced.view')->with($notification);
            }
        } else {
            $notification = [
                'warning' => 'Your account Balance is low Please Select Another account',
                'alert-type' => 'warning'
            ];
            return redirect()->back()->with($notification);
        }
    }
    public function EmployeeSalaryAdvancedView()
    {
        $employeSalary = EmployeeSalary::all();
        return view('pos.employee_salary.view_advanced_employee_salary', compact('employeSalary'));
    } //
    public function EmployeeSalaryAdvancedEdit($id)
    {
        $employeeSalary = EmployeeSalary::findOrFail($id);
        $employees = Employee::latest()->get();
        $bank = Bank::latest()->get();
        $branch = Branch::latest()->get();
        return view('pos.employee_salary.edit_advanced_employee_salary', compact('employeeSalary', 'employees', 'branch', 'bank'));
    }
    public function EmployeeSalaryAdvancedUpdate(Request $request, $id)
    {
        $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
        if ($oldBalance && $oldBalance->balance > 0 && $oldBalance->balance >= $request->debit) {

            $employeeSalary = EmployeeSalary::findOrFail($id);
            $requiestDebit = $employeeSalary->debit = $employeeSalary->debit + $request->debit;
            $employeeSalary->date =  $request->date;
            $employeeSalary->balance = $employeeSalary->creadit - $requiestDebit;
            $employeeSalary->note  = $request->note;
            $employeeSalary->updated_at  = Carbon::now();
            $employeeSalary->update();
            //account Transaction Crud
            $accountTransaction = new AccountTransaction;
            $accountTransaction->branch_id =  Auth::user()->branch_id;
            $accountTransaction->reference_id = $employeeSalary->id;
            $accountTransaction->purpose =  'Employee Salary';
            $accountTransaction->account_id =  $request->payment_method;
            $accountTransaction->debit = $request->debit; // $request->Amount
            $oldBalance = AccountTransaction::where('account_id', $request->payment_method)->latest('created_at')->first();
            $accountTransaction->balance = $oldBalance->balance - $request->debit;
            $accountTransaction->created_at = Carbon::now();
            $accountTransaction->save();
            $notification = array(
                'message' => 'Employee Salary Advanced Update Successfully',
                'alert-type' => 'info'
            );
            return redirect()->route('employee.salary.advanced.view')->with($notification);
        } else {
            $notification = [
                'warning' => 'Your account Balance is low Please Select Another account',
                'alert-type' => 'warning'
            ];
            return redirect()->back()->with($notification);
        }
    } //
    public function EmployeeSalaryAdvancedDelete($id)
    {
        EmployeeSalary::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Employee Advanced Salary Deleted Successfully',
            'alert-type' => 'info'
        );
        return redirect()->route('employee.salary.advanced.view')->with($notification);
    }
    //Dependancy
    public function BranchAjax($branch_id)
    {
        $branch = Employee::where('branch_id', $branch_id)->get();
        return  json_encode($branch);
    } //
    public function getEmployeeInfo(Request $request, $employee_id)
    {
        // dd($employee_id);
        $requestMonth = Carbon::createFromFormat('Y-m-d', $request->date)->format('m');
        $requestYear = Carbon::createFromFormat('Y-m-d', $request->date)->format('Y');
        // Get the first and last day of the month
        $firstDayOfMonth = Carbon::create($requestYear, $requestMonth, 1)->startOfMonth();
        $lastDayOfMonth = Carbon::create($requestYear, $requestMonth, 1)->endOfMonth();
        $employee = EmployeeSalary::where('employee_id', $employee_id)
            ->whereBetween('date', [$firstDayOfMonth, $lastDayOfMonth])
            ->latest()->first();
        // dd($employee);
        return response()->json([
            'data' => $employee
        ]);
    }
}
