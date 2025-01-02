<?php

namespace App\Http\Controllers;

use App\Models\Tags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Services\ImageService;
use function App\Helper\generateUniqueSlug;
class TagController extends Controller
{
    public function index(){
        return view('pos.products.tag.tags');
    }


    public function store(Request $request, ImageService $imageService)
    {
        try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        if ($validator->passes()) {
            $tags = new Tags();
            $tags->name = $request->name;
            $tags->slug= generateUniqueSlug($request->name, $tags);
            $tags->save();

            return response()->json([
                'status' => 200,
                'message' => 'Tags Saved  Successfully!',
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
        $tags = Tags::all(); //
        return response()->json([
            "status" => 200,
            "data" => $tags
        ]);
    }
    public function edit($id)
    {
        $tags = Tags::findOrFail($id);
        if ($tags) {
            return response()->json([
                'status' => 200,
                'tags' => $tags
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

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        try {
        if ($validator->passes()) {
            $category = Tags::findOrFail($id);
            $category->name =  $request->name;
            $category->slug = Str::slug($request->name);
            $category->save();
            return response()->json([
                'status' => 200,
                'message' => 'Tags Update Successfully',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'error' => 'Something went wrong! ' . $e->getMessage(),
        ]);
    }
    }
    public function destroy($id)
    {
        $tags = Tags::findOrFail($id);
        $tags->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Tags Deleted Successfully',
        ]);
    }
    public function status($id)
    {
        try {
            $tags = Tags::findOrFail($id);
            $newStatus = $tags->status === 'inactive' ? 'active' : 'inactive';
            $tags->update(['status' => $newStatus]);

            return response()->json([
                'status' => 200,
                'newStatus' => $newStatus,
                'message' => 'Status changed successfully.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Handles cases where the tag with the given ID is not found
            return response()->json([
                'status' => 404,
                'message' => 'Tag not found.',
            ]);
        } catch (\Exception $e) {
            // Handles any other general exceptions
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong! ' . $e->getMessage(),
            ]);
        }

    }

}
