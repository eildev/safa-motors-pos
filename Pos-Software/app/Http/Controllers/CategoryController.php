<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Support\Facades\Validator;
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
    // public function store(Request $request)
    // {
    //     // dd($request->all());
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|max:255',
    //     ]);
    //     if ($validator->passes()) {
    //         $category = new Category;
    //         if ($request->image) {
    //             $imageName = rand() . '.' . $request->image->extension();
    //             $request->image->move(public_path('uploads/category/'), $imageName);
    //             $category->image = $imageName;
    //         }
    //         $category->name =  $request->name;
    //         $category->slug = Str::slug($request->name);
    //         $category->save();
    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Category Save Successfully',
    //         ]);
    //     } else {
    //         return response()->json([
    //             'status' => '500',
    //             'error' => $validator->messages()
    //         ]);
    //     }
    // }

    // using spati image optimizer
    // public function store(Request $request)
    // {
    //     // Validate incoming request
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|max:255',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Ensure image validation
    //     ]);

    //     if ($validator->passes()) {
    //         $category = new Category;

    //         if ($request->hasFile('image')) {
    //             // Generate unique image name
    //             $imageName = rand() . '.' . $request->image->extension();

    //             // Move the uploaded file to the public/uploads/category directory
    //             $imagePath = public_path('uploads/category/' . $imageName);
    //             $request->image->move(public_path('uploads/category/'), $imageName);

    //             // Optimize the image
    //             ImageOptimizer::optimize($imagePath);

    //             // Save the image name in the database
    //             $category->image = $imageName;
    //         }

    //         // Save category details
    //         $category->name = $request->name;
    //         $category->slug = Str::slug($request->name);
    //         $category->save();

    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Category saved and image optimized successfully!',
    //         ]);
    //     } else {
    //         return response()->json([
    //             'status' => 500,
    //             'error' => $validator->messages()
    //         ]);
    //     }
    // }




    // public function store(Request $request)
    // {
    //     // Validate incoming request
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|max:255',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB পর্যন্ত অনুমোদিত
    //     ]);

    //     if ($validator->passes()) {
    //         $category = new Category;

    //         if ($request->hasFile('image')) {
    //             // Generate unique image name
    //             $imageName = rand() . '.' . $request->image->extension();

    //             // Define image paths
    //             $destinationPath = public_path('uploads/category/');
    //             $imagePath = $destinationPath . $imageName;

    //             // Resize and save the image using Intervention
    //             $image = Image::make($request->file('image'))
    //                 ->resize(800, 600, function ($constraint) {
    //                     $constraint->aspectRatio(); // Maintain aspect ratio
    //                     $constraint->upsize(); // Prevent upsizing
    //                 })
    //                 ->save($imagePath, 75); // Save with 75% quality

    //             // Optimize the resized image using Spatie
    //             ImageOptimizer::optimize($imagePath);

    //             // Save the image name to the database
    //             $category->image = $imageName;
    //         }

    //         // Save category details
    //         $category->name = $request->name;
    //         $category->slug = Str::slug($request->name);
    //         $category->save();

    //         return response()->json([
    //             'status' => 200,
    //             'message' => 'Category saved and image resized & optimized successfully!',
    //         ]);
    //     } else {
    //         return response()->json([
    //             'status' => 500,
    //             'error' => $validator->messages()
    //         ]);
    //     }
    // }

    public function store(Request $request, ImageService $imageService)
    {
        try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        if ($validator->passes()) {
            $category = new Category;

            // if ($request->hasFile('image')) {
            //     $destinationPath = public_path('uploads/category/');
            //     $imageName = $imageService->resizeAndOptimize($request->file('image'), $destinationPath);
            //     $category->image = $imageName;
            // }

            $category->name = $request->name;
            $category->slug = Str::slug($request->name);
            $category->save();

            return response()->json([
                'status' => 200,
                'message' => 'Category saved and image resized & optimized successfully!',
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
    }//end catch
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
            $category->slug = Str::slug($request->name);
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
