<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use  App\Models\Branch;
use App\Services\ImageService;
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


    // public function store(Request $request, ImageService $imageService)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:99',
    //         'phone' => 'required|max:19',
    //         'address' => 'required|string|max:200',
    //         'email' => 'required|email|unique:branches,email',
    //         'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
    //     ]);

    //     if ($request->hasFile('logo')) {
    //         $destinationPath = public_path('uploads/branch/');
    //         $imageName = $imageService->resizeAndOptimize($request->file('logo'), $destinationPath);

    //         $bracnch = new Branch;
    //         $bracnch->name = $request->name;
    //         $bracnch->phone = $request->phone;
    //         $bracnch->address = $request->address;
    //         $bracnch->email = $request->email;
    //         $bracnch->logo = $imageName;
    //         $bracnch->save();
    //         $notification = array(
    //             'message' => 'Branch Created Successfully',
    //             'alert-type' => 'info'
    //         );
    //         return redirect()->route('branch.view')->with($notification);
    //     }
    // } //End Method
    public function store(Request $request, ImageService $imageService)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:99',
                'phone' => 'required|max:19',
                'address' => 'required|string|max:200',
                'email' => 'required|email|unique:branches,email',
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            ]);

            $imageName = null;

            if ($request->hasFile('logo')) {
                $destinationPath = public_path('uploads/branch/');
                $imageName = $imageService->resizeAndOptimize($request->file('logo'), $destinationPath);
            }

            Branch::create(array_merge($validatedData, ['logo' => $imageName]));

            return redirect()->route('branch.view')->with([
                'message' => 'Branch Created Successfully',
                'alert-type' => 'info',
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }


    public function BranchView()
    {
        $branches = $this->branchrepo->getAllBranch();
        // $branches = Branch::latest()->get();
        return view('pos.branches.all_branches', compact('branches'));
    } //End Method
    public function BranchEdit($id)
    {
        // $branch = $this->branchrepo->editBranch();
        $branch = Branch::findOrFail($id);
        return view('pos.branches.edit-branch', compact('branch'));
    } //End Method
    public function BranchUpdate(Request $request, $id)
    {

        if ($request->hasFile('logo')) {
            $request->validate([
                'name' => 'required|max:200',
                'phone' => 'required',
                'address' => 'required',
                'email' => 'required',
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif',
            ]);

            $imageName = rand() . '.' . $request->logo->extension();
            $request->logo->move(public_path('uploads/branch/'), $imageName);
            $branch = Branch::find($id);
            $path = public_path('uploads/branch/' . $branch->logo);
            if (file_exists($path)) {
                @unlink($path);
            }
            $branch->name = $request->name;
            $branch->phone = $request->phone;
            $branch->address = $request->address;
            $branch->email = $request->email;
            $branch->logo = $imageName;
            $branch->save();
            $notification = array(
                'message' => 'Branch Updated Successfully',
                'alert-type' => 'info'
            );
            return redirect()->route('branch.view')->with($notification);
        } else {
            $request->validate([
                'name' => 'required|max:200',
                'phone' => 'required',
                'address' => 'required',
                'email' => 'required',
            ]);
            $branch = Branch::findOrFail($id);
            $branch->name = $request->name;
            $branch->phone = $request->phone;
            $branch->address = $request->address;
            $branch->email = $request->email;
            $branch->save();
            $notification = array(
                'message' => 'Branch Updated  Successfully without logo ',
                'alert-type' => 'info'
            );
            return redirect()->route('branch.view')->with($notification);
        }
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
