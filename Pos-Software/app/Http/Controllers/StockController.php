<?php

namespace App\Http\Controllers;

use App\Models\Stocks;
use Illuminate\Http\Request;
use App\Models\Product;

class StockController extends Controller
{
    public function index()
    {
        try {

            $products = Product::get();

            // Attempt to load the view
            return view('pos.stock.index', compact('products'));
        } catch (\Exception $e) {
            // Handle any exceptions

            // Log the error
            Log::error('Error loading the bank view: ' . $e->getMessage());

            // Optionally return a custom error view or a simple error message
            return response()->view('errors.404', ['message' => $e->getMessage()], 404);
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

            $supplier = new Stocks;
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
}
