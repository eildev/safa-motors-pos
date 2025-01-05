<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Transaction;
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

    // customer Store 
    public function CustomerStore(Request $request)
    {
        // dd($request->all());
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|max:19',
                'email' => 'nullable|max:200|email|unique:customers,email',
                'address' => 'nullable|string|max:200',
                'business_name' => 'nullable|string|max:120',
                'customer_type' => 'required|string|max:120',
                'wallet_balance' => [
                    'nullable', // 
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
                        'error' => 'Validation Failed. Please check the input fields.',
                        'alert-type' => 'error'
                    ]);
            }

            // Store customer data
            $customer = new Customer;
            $customer->name = $request->name;
            $customer->slug = generateUniqueSlug($request->name, $customer);
            $customer->phone = $request->phone;
            $customer->business_name = $request->business_name;
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
                'error' => 'An error occurred while creating the customer. Please try again.',
                'alert-type' => 'error',
            ];
            return redirect()->back()->withInput()->with($notification);
        }
    }

    public function CustomerView()
    {
        try {
            // Retrieve all customers using repository
            $customers = $this->customer_repo->ViewAllCustomer();

            // Return view with customers data
            return view('pos.customer.view_customer', compact('customers'));
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error occurred in CustomerView: ' . $e->getMessage());

            // Redirect back with an error notification
            $notification = [
                'error' => 'Failed to load customer data. Please try again later.',
                'alert-type' => 'error',
            ];
            return redirect()->back()->with($notification);
        }
    }

    public function CustomerEdit($id)
    {
        try {
            // Retrieve customer by slug
            $customer = Customer::where('slug', $id)->first();

            // If customer not found, throw an exception
            if (!$customer) {
                throw new \Exception('Customer not found.');
            }

            // Return edit view with customer data
            return view('pos.customer.edit_customer', compact('customer'));
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error occurred in CustomerEdit: ' . $e->getMessage());

            // Redirect back with an error notification
            $notification = [
                'error' => 'Failed to load customer details. Please try again.',
                'alert-type' => 'error',
            ];
            return redirect()->back()->with($notification);
        }
    }

    public function CustomerUpdate(Request $request, $id)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|max:19',
                'email' => 'nullable|max:200|email|unique:customers,email,' . $id, // To allow update of own email
                'address' => 'nullable|string|max:200',
                'business_name' => 'nullable|string|max:120',
                'customer_type' => 'required|string|max:120',
            ]);

            // If validation fails
            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with([
                        'error' => 'Validation Failed. Please check the input fields.',
                        'alert-type' => 'error'
                    ]);
            }

            // Find the customer and update
            $customer = Customer::findOrFail($id); // Will throw an exception if customer is not found
            $customer->name = $request->name;
            $customer->slug = generateUniqueSlug($request->name, $customer);
            $customer->phone = $request->phone;
            $customer->business_name = $request->business_name;
            $customer->email = $request->email;
            $customer->address = $request->address;
            $customer->customer_type = $request->customer_type;
            $customer->updated_at = Carbon::now();
            $customer->save();

            // Success notification
            $notification = array(
                'message' => 'Customer Updated Successfully',
                'alert-type' => 'info'
            );

            return redirect()->route('customer.view')->with($notification);
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error occurred in CustomerUpdate: ' . $e->getMessage());

            // Error notification
            $notification = array(
                'error' => 'Failed to update customer. Please try again.',
                'alert-type' => 'error'
            );

            // Redirect back with error notification
            return redirect()->back()->with($notification);
        }
    }

    public function CustomerDelete($id)
    {
        try {
            // Find and delete customer by id
            $customer = Customer::findOrFail($id); // Will throw an exception if customer not found
            $customer->delete();

            // Success notification
            $notification = array(
                'message' => 'Customer Deleted Successfully',
                'alert-type' => 'info'
            );

            return redirect()->back()->with($notification);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Error occurred in CustomerDelete: ' . $e->getMessage());

            // Error notification
            $notification = array(
                'error' => 'Failed to delete customer. Please try again.',
                'alert-type' => 'error'
            );

            // Redirect back with error notification
            return redirect()->back()->with($notification);
        }
    }

    public function CustomerProfile($id)
    {
        try {
            // Retrieve customer data using slug
            $data = Customer::where('slug', $id)->firstOrFail(); // Will throw exception if not found

            // Retrieve related transactions
            $transactions = Transaction::where('customer_id', $data->id)->get();

            // Retrieve branch details
            $branch = Branch::findOrFail($data->branch_id); // Will throw exception if not found

            // Retrieve bank details
            $banks = Bank::latest()->get();

            // Set flag to identify customer profile
            $isCustomer = true;

            // Return view with customer profile data
            return view('pos.profiling.profiling', compact('data', 'transactions', 'branch', 'isCustomer', 'banks'));
        } catch (\Exception $e) {
            // Log the exception message for debugging
            Log::error('Error occurred in CustomerProfile: ' . $e->getMessage());

            // Error notification
            $notification = array(
                'error' => 'Failed to retrieve customer profile. Please try again.',
                'alert-type' => 'error'
            );

            // Redirect back with error notification
            return redirect()->back()->with($notification);
        }
    }
}
