<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Psize;
use App\Models\Size;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function App\Helper\generateUniqueSlug;
use App\Repositories\RepositoryInterfaces\SubCategoryInterface;
// use Validator;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{

    private $subCategory;
    public function __construct(SubCategoryInterface $subCategory)
    {
        $this->subCategory = $subCategory;
    }

    public function index()
    {
        $categories = Category::get();
        // return view('pos.products.category',compact('categories'));
        return view('pos.products.subcategory', compact('categories'));
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'category_id' => 'required|max:255',
        ]);

        if ($validator->passes()) {
            try {
            $subcategory =  new SubCategory();
            $subcategory->name = $request->name;
            $subcategory->category_id  = $request->category_id;
            $subcategory->slug = generateUniqueSlug($request->name, $subcategory);
            $subcategory->save();
            return response()->json([
                'status' => 200,
                'message' => 'Sub Category Saved Successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        } //
    }
    //
    public function view()
    {
        //   $subcategories = SubCategory::all();
        $subcategories = $this->subCategory->getAllSubCategory();
        $subcategories->load('category');
        return response()->json([
            "status" => 200,
            "data" => $subcategories,
        ]);
    } //
    public function edit($id)
    {
        //  $category = SubCategory::findOrFail($id);
        $subcategory = $this->subCategory->editData($id);
        // $categories = Category::get();
        if ($subcategory) {
            return response()->json([
                'status' => 200,
                'subcategory' => $subcategory,
                // 'categories' => $categories
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => "Data Not Found"
            ]);
        }
    } //
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'category_id' => 'required',
        ]);
        if ($validator->passes()) {
            $subcategory = SubCategory::findOrFail($id);
            $subcategory->name =  $request->name;
            $subcategory->slug = generateUniqueSlug($request->name, $subcategory);
            $subcategory->category_id =  $request->category_id;
            $subcategory->save();
            return response()->json([
                'status' => 200,
                'message' => 'sub Category Update Successfully',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    } //
    public function destroy($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        if ($subcategory->image) {
            $previousImagePath = public_path('uploads/subcategory/') . $subcategory->image;
            if (file_exists($previousImagePath)) {
                unlink($previousImagePath);
            }
        }
        $subcategory->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Sub Category Deleted Successfully',
        ]);
    } //
    public function status($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $newStatus = $subcategory->status == 'inactive' ? 'active' : 'inactive';
        $subcategory->update([
            'status' => $newStatus
        ]);
        return response()->json([
            'status' => 200,
            'newStatus' => $newStatus,
            'message' => 'Status Changed Successfully',
        ]);
    }
    public function find($id)
    {
        $subcategory = SubCategory::where('category_id', $id)->get();
        $size = Size::where('category_id', $id)->get();
        return response()->json([
            'status' => 200,
            'data' => $subcategory,
            'size' => $size,
        ]);
    }
}
