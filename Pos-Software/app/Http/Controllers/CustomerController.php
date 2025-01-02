<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Repositories\RepositoryInterfaces\CustomerInterfaces;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use function App\Helper\generateUniqueSlug;

class CustomerController extends Controller
{

    private $customer_repo;
    public function __construct(CustomerInterfaces $customer_interface)
    {
        $this->customer_repo = $customer_interface;
    }
    public function AddCustomer()
    {
        try {
            // Attempt to return the view
            return view('pos.customer.add_customer');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            // Log the error for debugging purposes
            Log::error('Error loading AddCustomer view: ' . $e->getMessage());

            // Optionally, return a fallback response, such as an error page or a custom message
            return response()->view('errors.500', ['message' => 'Failed to load the add customer page. Please try again later.'], 500);
        }
    }

    // public function CustomerStore(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'phone' => 'required|max:19',
    //         'email' => 'max:200|email|unique:customers,email',
    //         'address' => 'string|max:200',
    //         'business_name' => 'string|max:120',
    //         'customer_type' => 'required|string|max:120',
    //         'wallet_balance' => [
    //             'numeric',
    //             'regex:/^\d{1,10}(\.\d{1,2})?$/',
    //         ],
    //     ]);

    //     if ($validator->passes()) {
    //         $customer = new Customer;
    //         $customer->name = $request->name;
    //         $customer->slug = generateUniqueSlug($request->name, $customer);
    //         $customer->phone = $request->phone;
    //         $customer->email = $request->email;
    //         $customer->address = $request->address;
    //         $customer->customer_type = $request->customer_type;
    //         $customer->due_balance = $request->wallet_balance ?? 0;
    //         $customer->created_at = Carbon::now();
    //         $customer->save();

    //         $notification = array(
    //             'message' => 'Customer Created Successfully',
    //             'alert-type' => 'info'
    //         );
    //         return redirect()->route('customer.view')->with($notification);
    //     }
    // } //End Method

    // customer Store 
    public function CustomerStore(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|max:19',
                'email' => 'max:200|email|unique:customers,email',
                'address' => 'string|max:200',
                'business_name' => 'string|max:120',
                'customer_type' => 'required|string|max:120',
                'wallet_balance' => [
                    'numeric',
                    'regex:/^\d{1,10}(\.\d{1,2})?$/',
                ],
            ]);

            // If validation fails
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with([
                        'message' => 'Validation Failed. Please check the input fields.',
                        'alert-type' => 'error'
                    ]);
            }

            // Store customer data
            $customer = new Customer;
            $customer->name = $request->name;
            $customer->slug = generateUniqueSlug($request->name, $customer);
            $customer->phone = $request->phone;
            $customer->email = $request->email;
            $customer->address = $request->address;
            $customer->customer_type = $request->customer_type;
            $customer->due_balance = $request->wallet_balance ?? 0;
            $customer->created_at = Carbon::now();
            $customer->save();

            // Success notification
            $notification = [
                'message' => 'Customer Created Successfully',
                'alert-type' => 'info',
            ];
            return redirect()->route('customer.view')->with($notification);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error occurred in CustomerStore: ' . $e->getMessage());

            // Failure notification
            $notification = [
                'message' => 'An error occurred while creating the customer. Please try again.',
                'alert-type' => 'error',
            ];
            return redirect()->back()->withInput()->with($notification);
        }
    }

    public function CustomerView()
    {
        $customers = $this->customer_repo->ViewAllCustomer();
        return view('pos.customer.view_customer', compact('customers'));
    } //
    public function CustomerEdit($id)
    {
        $customer = $this->customer_repo->EditCustomer($id);
        return view('pos.customer.edit_customer', compact('customer'));
    } //
    public function CustomerUpdate(Request $request, $id)
    {
        $customer = Customer::find($id);
        $customer->branch_id = Auth::user()->branch_id;
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->email = $request->email;
        $customer->address = $request->address;
        // $customer->opening_receivable = $request->opening_receivable ?? 0;
        // $customer->opening_payable = $request->opening_payable ?? 0;
        // $customer->wallet_balance = $request->wallet_balance ?? 0;
        // $customer->total_receivable = $request->total_receivable ?? 0;
        // $customer->total_payable = $request->total_payable ?? 0;
        $customer->updated_at = Carbon::now();
        $customer->save();
        $notification = array(
            'message' => 'Customer Updated Successfully',
            'alert-type' => 'info'
        );
        return redirect()->route('customer.view')->with($notification);
    } //End Method
    public function CustomerDelete($id)
    {
        Customer::findOrFail($id)->delete();
        $notification = array(
            'message' => 'Customer Deleted Successfully',
            'alert-type' => 'info'
        );
        return redirect()->back()->with($notification);
    }
    public function CustomerProfile($id)
    {
        $data = Customer::findOrFail($id);
        $transactions = Transaction::where('customer_id', $data->id)->get();
        $branch = Branch::findOrFail($data->branch_id);
        $banks = Bank::latest()->get();
        $isCustomer = true;

        return view('pos.profiling.profiling', compact('data', 'transactions', 'branch', 'isCustomer', 'banks'));
    }
}
