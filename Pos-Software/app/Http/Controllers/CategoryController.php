<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Support\Facades\Validator;
use function App\Helper\generateUniqueSlug;
use Illuminate\Support\Str;
use App\Repositories\RepositoryInterfaces\CategoryInterface;

class CategoryController extends Controller
{
    private $CategoryRepo;
    public function __construct(CategoryInterface $CategoryRepo)
    {
        $this->CategoryRepo = $CategoryRepo;
    }
    public function index()
    {
        return view('pos.products.category');
    }


    public function store(Request $request, ImageService $imageService)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
            ]);

            if ($validator->passes()) {
                $category = new Category;
                $category->name = $request->name;
                $category->slug = generateUniqueSlug($request->name, $category);
                $category->save();

                return response()->json([
                    'status' => 200,
                    'message' => 'Category saved  successfully!',
                ]);
            } else {
                return response()->json([
                    'status' => 500,
                    'error' => $validator->messages(),
                ]);
            }
        } //end try
        catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'error' => 'An unexpected error occurred: ' . $e->getMessage(),
            ]);
        } //end catch
    }


    public function view()
    {
        $categories = $this->CategoryRepo->getAllCategory();
        // $categories = Category::all();
        return response()->json([
            "status" => 200,
            "data" => $categories
        ]);
    }
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        if ($category) {
            return response()->json([
                'status' => 200,
                'category' => $category
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'message' => "Data Not Found"
            ]);
        }
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        if ($validator->passes()) {
            $category = Category::findOrFail($id);
            $category->name =  $request->name;
            // $category->slug = Str::slug($request->name);
            $category->slug = generateUniqueSlug($request->name, $category);
            $category->save();
            return response()->json([
                'status' => 200,
                'message' => 'Category Update Successfully',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    }
    public function status($id)
    {
        $category = Category::findOrFail($id);
        $newStatus = $category->status === 'inactive' ? 'active' : 'inactive';
        $category->update(['status' => $newStatus]);

        return response()->json([
            'status' => 200,
            'newStatus' => $newStatus,
            'message' => 'Status changed successfully.',
        ]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Category Deleted Successfully',
        ]);
    }


    // public function categoryAll()
    // {
    //     $categories = Category::all();
    //     return  response()->json([
    //         'status' => 200,
    //         'categories' =>  $categories,
    //     ]);
    // }
}
