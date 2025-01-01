<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use  App\Models\Branch;
use App\Services\ImageService;
use function App\Helper\generateUniqueSlug;
use App\Repositories\RepositoryInterfaces\BranchInterface;

class BranchController extends Controller
{

    private $branchrepo;
    public function __construct(BranchInterface $branchInterface)
    {
        $this->branchrepo = $branchInterface;
    }

    // index function for branch
    public function index()
    {
        try {
            // Fetch and return the branches view
            return view('pos.branches.index');
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error('Error loading branches index: ' . $e->getMessage());

            // Pass the error message to a custom error blade
            return view('errors.500', ['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request, ImageService $imageService)
    {
        $request->validate([
            'name' => 'required|string|max:99',
            'phone' => 'required|max:19',
            'address' => 'required|string|max:200',
            'email' => 'required|email|unique:branches,email',
            'logo' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        try {
            $branch = new Branch;
            $branch->name = $request->name;
            $branch->slug = generateUniqueSlug($request->name, $branch);
            $branch->phone = $request->phone;
            $branch->address = $request->address;
            $branch->email = $request->email;
            if ($request->hasFile('logo')) {
                $destinationPath = public_path('uploads/branch/');
                $imageName = $imageService->resizeAndOptimize($request->file('logo'), $destinationPath);
                $branch->logo = $imageName;
            }
            $branch->save();

            $notification = array(
                'message' => 'Branch Created Successfully',
                'alert-type' => 'info'
            );
            return redirect()->route('branch.view')->with($notification);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }




    public function BranchView()
    {
        try {
            $branches = $this->branchrepo->getAllBranch(); // ডেটা ফেচ করার চেষ্টা

            // ভিউতে ডেটা পাঠানো
            return view('pos.branches.all_branches', compact('branches'));
        } catch (\Exception $e) {
            // যদি কোনো এরর হয়, তাহলে ব্যাকএন্ডের একটি এরর বার্তা শো করবে
            return back()->withErrors(['error' => 'Failed to retrieve branches: ' . $e->getMessage()]);
        }
    } //End Method


    public function BranchEdit($id)
    {
        try {
            // ব্রাঞ্চ ডেটা ফেচ করার চেষ্টা
            $branch = Branch::where('slug', $id)->first();
            // যদি সফল হয়, ভিউতে ডেটা পাঠানো
            return view('pos.branches.edit-branch', compact('branch'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // যদি ব্রাঞ্চ আইডি না পাওয়া যায়
            return redirect()->route('branch.view')->withErrors(['error' => 'Branch not found.']);
        } catch (\Exception $e) {
            // অন্য কোনো এরর হ্যান্ডলিং
            return redirect()->route('branch.view')->withErrors(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    } //End Method


    public function BranchUpdate(Request $request, $id, ImageService $imageService)
    {
        $request->validate([
            'name' => 'required|string|max:99',
            'phone' => 'required|max:19',
            'address' => 'required|string|max:200',
            'email' => 'required|email',
            'logo' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        $branch = Branch::find($id);
        $branch->name = $request->name;
        $branch->slug = generateUniqueSlug($request->name, $branch);
        $branch->phone = $request->phone;
        $branch->address = $request->address;
        $branch->email = $request->email;
        if ($request->hasFile('logo')) {
            $destinationPath = public_path('uploads/branch/');
            $imageName = $imageService->resizeAndOptimize($request->file('logo'), $destinationPath);

            $path = public_path('uploads/branch/' . $branch->logo);
            if (file_exists($path)) {
                @unlink($path);
            }
            $branch->logo = $imageName;
        }
        $branch->save();
        $notification = array(
            'message' => 'Branch Updated Successfully',
            'alert-type' => 'info'
        );
        return redirect()->route('branch.view')->with($notification);
    } //End Method

    public function BranchDelete($id)
    {
        $branch = Branch::findOrFail($id);
        $path = public_path('uploads/branch/' . $branch->logo);
        if (file_exists($path)) {
            @unlink($path);
        }
        $branch->delete();
        $notification = array(
            'message' => 'Branch Deleted Successfully',
            'alert-type' => 'info'
        );
        return redirect()->back()->with($notification);
    } //End Method
}
