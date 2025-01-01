<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function index()
    {
        return view('pos.products.unit');
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:39',

        ]);

        if ($validator->passes()) {
            $unit = new Unit;
            $unit->name =  $request->name;

            $unit->save();
            return response()->json([
                'status' => 200,
                'message' => 'Unit Save Successfully',
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
        $units = Unit::get();
        return response()->json([
            "status" => 200,
            "data" => $units
        ]);
    }
    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        if ($unit) {
            return response()->json([
                'status' => 200,
                'unit' => $unit
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
            'name' => 'required|max:39',

        ]);
        dd( $request->name);
        if ($validator->passes()) {
            $unit = Unit::findOrFail($id);
            $unit->name =  $request->name;

            $unit->save();
            return response()->json([
                'status' => 200,
                'message' => 'Unit Update Successfully',
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
        $unit = Unit::findOrFail($id);
        $unit->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Unit Deleted Successfully',
        ]);
    }
    public function status($id)
    {
        $unit = Unit::findOrFail($id);
        $newStatus = $unit->status == 'inactive' ? 'active' : 'inactive';
        $unit->update([
            'status' => $newStatus
        ]);
        return response()->json([
            'status' => 200,
            'newStatus' => $newStatus,
            'message' => 'Status Changed Successfully',
        ]);
    }
}
