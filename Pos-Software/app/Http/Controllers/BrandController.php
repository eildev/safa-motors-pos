<?php

namespace App\Http\Controllers;

use App\Models\Brand;
// use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Repositories\RepositoryInterfaces\BrandInterface;
use Illuminate\Support\Facades\Validator;
use function App\Helper\generateUniqueSlug;
class BrandController extends Controller
{
    private $brandRepo;
    public function __construct(BrandInterface $brandRepos){
        $this->brandRepo = $brandRepos;
    }
    public function index()
    {
        return view('pos.products.brand');
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        if ($validator->passes()) {
            $brand = new Brand;
            $brand->name =  $request->name;
            $brand->slug = generateUniqueSlug($request->name, $brand);

            // $brand->slug = Str::slug($request->name);
            $brand->save();
            return response()->json([
                'status' => 200,
                'message' => 'Brand Save Successfully',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    }
    public function view()
    {
        // $brands = Brand::all();
        $brands = $this->brandRepo->getAllBrand();
        // dd($brands);
        return response()->json([
            "status" => 200,
            "data" => $brands
        ]);
    }
    public function edit($id)
    {
        $brand = $this->brandRepo->editData($id);
        if ($brand) {
            return response()->json([
                'status' => 200,
                'brand' => $brand
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
            $brand = Brand::findOrFail($id);
            $brand->name =  $request->name;
            $brand->slug = generateUniqueSlug($request->name, $brand);
            // $brand->description = $request->description;
            // if ($request->image) {
            //     $imageName = rand() . '.' . $request->image->extension();
            //     $request->image->move(public_path('uploads/brand/'), $imageName);
            //     if ($brand->image) {
            //         $previousImagePath = public_path('uploads/brand/') . $brand->image;
            //         if (file_exists($previousImagePath)) {
            //             unlink($previousImagePath);
            //         }
            //     }
            //     $brand->image = $imageName;
            // }

            $brand->save();
            return response()->json([
                'status' => 200,
                'message' => 'Brand Update Successfully',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    }
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        if ($brand->image) {
            $previousImagePath = public_path('uploads/brand/') . $brand->image;
            if (file_exists($previousImagePath)) {
                unlink($previousImagePath);
            }
        }
        $brand->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Brand Deleted Successfully',
        ]);
    }
    public function status($id)
    {
        $brand = Brand::findOrFail($id);
        $newStatus = $brand->status === 'inactive' ? 'active' : 'inactive';
        $brand->update(['status' => $newStatus]);

        return response()->json([
            'status' => 200,
            'newStatus' => $newStatus,
            'message' => 'Status changed successfully.',
        ]);
    }

}
