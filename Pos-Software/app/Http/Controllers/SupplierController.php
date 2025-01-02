<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Branch;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use App\Models\ViaSale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function App\Helper\generateUniqueSlug;

class SupplierController extends Controller
{
    public function index()
    {
        try {
            // Attempt to load the view
            return view('pos.supplier.supplier');
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->view('errors.general', ['message' => $e->getMessage()], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|max:19',
                'email' => 'nullable|max:200|email|unique:suppliers,email',
                'address' => 'nullable|string|max:200',
                'business_name' => 'nullable|string|max:120',
                'supplier_type' => 'required|string|max:120',
                'due_balance' => [
                    'nullable',
                    'numeric',
                    'regex:/^\d{1,10}(\.\d{1,2})?$/',
                ],
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'error' => $validator->messages()
                ]);
            }

            $supplier = new Supplier;
            $supplier->name = $request->name;
            $supplier->slug = generateUniqueSlug($request->name, $supplier);
            $supplier->phone = $request->phone;
            $supplier->business_name = $request->business_name;
            $supplier->email = $request->email;
            $supplier->address = $request->address;
            $supplier->supplier_type = $request->supplier_type;
            $supplier->due_balance = $request->due_balance ?? 0;
            $supplier->created_at = Carbon::now();
            $supplier->save();

            return response()->json([
                'status' => 200,
                'message' => 'Supplier Saved Successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving supplier: ' . $e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'An unexpected error occurred while saving the supplier. Please try again.',
            ], 500);
        }
    }


    public function view()
    {
        try {
            // Retrieve all suppliers
            $suppliers = Supplier::get();

            // Return response with suppliers data
            return response()->json([
                "status" => 200,
                "data" => $suppliers
            ]);
        } catch (\Exception $e) {
            // Log the exception message for debugging
            Log::error('Error occurred in Supplier view: ' . $e->getMessage());

            // Return error response
            return response()->json([
                "status" => 500,
                "message" => 'Failed to retrieve suppliers. Please try again.'
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);

            return response()->json([
                'status' => 200,
                'supplier' => $supplier,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Supplier not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error fetching supplier: ' . $e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'An unexpected error occurred. Please try again.',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|max:19',
                'email' => 'nullable|max:200|email',
                'address' => 'nullable|string|max:200',
                'business_name' => 'nullable|string|max:120',
                'supplier_type' => 'required|string|max:120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 400,
                    'error' => $validator->messages()
                ]);
            }

            $supplier = Supplier::findOrFail($id);
            $supplier->name = $request->name;
            $supplier->slug = generateUniqueSlug($request->name, $supplier);
            $supplier->phone = $request->phone;
            $supplier->business_name = $request->business_name;
            $supplier->email = $request->email;
            $supplier->address = $request->address;
            $supplier->supplier_type = $request->supplier_type;
            $supplier->save();
            return response()->json([
                'status' => 200,
                'message' => 'Supplier Update Successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving supplier: ' . $e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'An unexpected error occurred while saving the supplier. Please try again.',
            ], 500);
        }
    }
    public function destroy($id)
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Supplier Deleted Successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Supplier not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error while deleting supplier: ' . $e->getMessage());

            return response()->json([
                'status' => 500,
                'message' => 'An error occurred while trying to delete the supplier. Please try again later.',
            ], 500);
        }
    }


    public function SupplierProfile($id)
    {
        $data = Supplier::where('slug', $id)->first();
        $transactions = Transaction::where('supplier_id', $data->id)->get();
        $branch = Branch::findOrFail($data->branch_id);
        $banks = Bank::latest()->get();
        $isCustomer = false;
        return view('pos.profiling.profiling', compact('data', 'transactions', 'branch', 'isCustomer', 'banks',));
    }
}//End