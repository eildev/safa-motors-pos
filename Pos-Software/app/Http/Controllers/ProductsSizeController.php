<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Size;
use App\Models\Category;
class ProductsSizeController extends Controller
{
    public function ProductSizeView(){
        $productSize = Size::latest()->get();
        return view('pos.products.product-size.all_products_size',compact('productSize'));
    }//End Method

    public function ProductSizeAdd(){
        $productSize = Size::latest()->get();
        $allCategory = Category::latest()->get();
        return view('pos.products.product-size.add_products_size',compact('allCategory','productSize'));
    }//End Method

    public function ProductSizeStore(Request $request) {
        try {
            $productSize = new Size;
            $productSize->category_id = $request->category_id;
            $productSize->size = $request->size;
            $productSize->save();

            $notification = [
                'message' => 'Product Size Added Successfully',
                'alert-type' => 'info'
            ];

            return response()->json([
                'message' => $notification['message'],
                'redirect_url' => route('product.size.view')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while adding the product size: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }

    }

    public function ProductSizeEdit($id){
        $productSize = Size::findOrFail($id);
        $allCategory = Category::latest()->get();
        return view('pos.products.product-size.edit_products_size',compact('productSize','allCategory'));
    }//
    public function ProductSizeUpdate(Request $request,$id){
        $productSize = Size::findOrFail($id);
        $productSize->category_id = $request->category_id;
        $productSize->size = $request->size;
        $productSize->save();
        $notification = array(
            'message' =>'Product Size Updated Successfully',
            'alert-type'=> 'info'
         );
        return redirect()->route('product.size.view')->with($notification);
    }//End Method
    public function ProductSizeDelete($id){
        $productSize = Size::findOrFail($id);
        $productSize->delete();
        $notification = array(
            'message' =>'Product Size Deleted Successfully',
            'alert-type'=> 'info'
         );
        return redirect()->route('product.size.view')->with($notification);
    }//End Method

}
