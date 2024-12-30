<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Repositories\RepositoryInterfaces\EmployeeInterface;

class EmployeeController extends Controller
{

    private $employee_repo;
    public function __construct(EmployeeInterface $employee_interface)
    {
        $this->employee_repo = $employee_interface;
    }

    public function EmployeeView()
    {
        $employees = $this->employee_repo->ViewAllEmployee();
        return view('pos.employee.view_employee', compact('employees'));
    } //
    public function EmployeeAdd()
    {
        return view('pos.employee.add_employee');
    } //
    public function EmployeeStore(Request $request)
    {

        if ($request->image) {
            $employee = new Employee();
            $imageName = rand() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/employee'), $imageName);
            $employee->pic = $imageName;
        }
        $employee = new Employee();
        $employee->branch_id = Auth::user()->branch_id;
        $employee->full_name = $request->full_name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->address = $request->address;
        $employee->salary = $request->salary;
        $employee->nid = $request->nid;
        $employee->designation = $request->designation;
        $employee->status = 0;
        // $employee->pic = $imageName;
        $employee->created_at = Carbon::now();
        $employee->save();
        $notification = array(
            'message' => 'Employee Added Successfully',
            'alert-type' => 'info'
        );
        return redirect()->route('employee.view')->with($notification);
    } //
    public function EmployeeEdit($id)
    {
        $employees =  $this->employee_repo->EditEmployee($id);
        return view('pos.employee.edit_employee', compact('employees'));
    } //
    public function EmployeeUpdate(Request $request, $id)
    {
        // dd($request->all());
        $employee = Employee::findOrFail($id);
        $employee->branch_id = Auth::user()->branch_id;
        $employee->full_name = $request->full_name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->address = $request->address;
        $employee->salary = $request->salary;
        $employee->nid = $request->nid;
        $employee->designation = $request->designation;
        $employee->status = 0;
        if ($request->image) {
            $imageName = rand() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/employee'), $imageName);
            $employee->pic = $imageName;
        }
        $employee->save();

        $notification = array(
            'message' => 'Employee Updated Successfully',
            'alert-type' => 'info'
        );
        return redirect()->route('employee.view')->with($notification);
    } //
    public function EmployeeDelete($id)
    {
        $employee = Employee::findOrFail($id);
        $path = public_path('uploads/employee/' . $employee->image);
        if (file_exists($path)) {
            @unlink($path);
        }
        $employee->delete();
        $notification = array(
            'message' => 'Employee Deleted Successfully',
            'alert-type' => 'info'
        );
        return redirect()->route('employee.view')->with($notification);
    }
    public function EmployeeDetails($id)
    {
        $employee = Employee::findOrFail($id);
        $salaries = EmployeeSalary::where('employee_id', $employee->id)->latest()->get();
        $branch = Branch::findOrFail($employee->branch_id);
        return view('pos.employee.view-details', compact('employee', 'salaries', 'branch'));
    } //

}