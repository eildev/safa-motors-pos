<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Support\Facades\Auth;
use App\Services\ImageService;
use Illuminate\Support\Facades\Log;


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
    public function EmployeeStore(Request $request ,ImageService $imageService)
    {

        try {
        $employee = new Employee();
        $employee->branch_id = Auth::user()->branch_id;
        $employee->full_name = $request->full_name;
        $employee->email = $request->email;
        $employee->phone = $request->phone;
        $employee->address = $request->address;
        $employee->salary = $request->salary;
        $employee->nid = $request->nid;
        $employee->designation = $request->designation;
        if ($request->hasFile('image')) {
            $destinationPath = public_path('uploads/employee');
            $imageName = $imageService->resizeAndOptimize($request->file('image'), $destinationPath);
            $employee->pic = $imageName;
        }
        $employee->created_at = Carbon::now();
        $employee->save();
        $notification = array(
            'message' => 'Employee Added Successfully',
            'alert-type' => 'info'
        );
        return redirect()->route('employee.view')->with($notification);
    } catch (\Exception $e) {
        // Log the exception for debugging
        Log::error('Error adding employee: ' . $e->getMessage());
        // Redirect back with an error notification
        $notification = [
            'error' => 'An error occurred while adding the employee. Please try again.',
            'alert-type' => 'error',
        ];

        return redirect()->back()->withInput()->with($notification);
    }
    } //
    public function EmployeeEdit($id)
    {
        $employees =  $this->employee_repo->EditEmployee($id);
        return view('pos.employee.edit_employee', compact('employees'));
    } //
    public function EmployeeUpdate(Request $request, $id, ImageService $imageService)
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
       
        if ($request->hasFile('image')) {
            $destinationPath = public_path('uploads/employee');
            $imageName = $imageService->resizeAndOptimize($request->file('image'), $destinationPath);

            $path = public_path('uploads/employee/' . $employee->pic);
            if (file_exists($path)) {
                @unlink($path);
            }
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
    public function updateStatus($id, $status)
    {
        $employee = Employee::findOrFail($id);

        if ($employee) {
            $employee->status = $status;
            $employee->save();

            return response()->json([
                'success' => true,
                'status' => $employee->status,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Employee not found']);
    }

}
