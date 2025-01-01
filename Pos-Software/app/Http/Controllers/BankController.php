<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\Bank;
use App\Repositories\RepositoryInterfaces\BankInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class BankController extends Controller
{
    private $bankrepo;
    public function __construct(BankInterface $bankInterface)
    {
        $this->bankrepo = $bankInterface;
    }
    public function index()
    {
        return view('pos.bank.bank');
    }
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:99',
                'opening_balance' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => '500',
                    'error' => $validator->messages()
                ]);
            }
            function generateSerialNumber()
            {
                $lastBank = Bank::latest('id')->first(); // Fetch the latest record
                $lastNumber = $lastBank ? intval($lastBank->bank_account_number) : 0;
                return str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT); // Pads to 6 digits
            }

            // If validation passes, proceed with saving the bank details
            $bank = new Bank;
            $bank->branch_id = Auth::user()->branch_id;
            $bank->name = $request->name;
            $bank->bank_branch_name = $request->bank_branch_name;
            $bank->bank_branch_manager_name = $request->bank_branch_manager_name;
            $bank->bank_branch_phone = $request->bank_branch_phone;
            // $bank->bank_account_number = $request->bank_account_number;
            $bank->bank_account_number = $request->bank_account_number ?? generateSerialNumber();

            $bank->bank_branch_email = $request->bank_branch_email;
            $bank->opening_balance = $request->opening_balance;
            $bank->current_balance =    $request->opening_balance;
            $bank->save();

            // Save the account transaction
            $accountTransaction = new AccountTransaction;
            $accountTransaction->branch_id = Auth::user()->branch_id;
            $accountTransaction->purpose = 'Bank';
            $accountTransaction->account_id = $bank->id;
            $accountTransaction->credit = $request->opening_balance;
            $accountTransaction->balance = $accountTransaction->balance + $request->opening_balance;
            $accountTransaction->save();

            return response()->json([
                'status' => 200,
                'message' => 'Bank Saved Successfully',
            ]);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error to saving bank details: ' . $e->getMessage());

            // Return the errors.500 view for internal server errors
            return response()->view('errors.500', [], 500);
        }
    }

    public function view()
    {
        // $banks = Bank::get();
        if (Auth::user()->id == 1) {
            $banks = $this->bankrepo->getAllBank();
        } else {
            $banks = Bank::where('branch_id', Auth::user()->branch_id)->latest()->get();
        }

        $banks->load('accountTransaction');

        // Add latest transaction to each bank
        foreach ($banks as $bank) {
            $bank->latest_transaction = $bank->accountTransaction()->latest()->first();
        }

        // dd($banks);
        return response()->json([
            "status" => 200,
            "data" => $banks
        ]);
    }
    public function edit($id)
    {
        $bank = $this->bankrepo->editBank($id);
        if ($bank) {
            return response()->json([
                'status' => 200,
                'bank' => $bank
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
            'name' => 'required|max:99',

        ]);
        try {
        if ($validator->passes()) {
            $bank = Bank::findOrFail($id);
            $bank->name =  $request->name;
            $bank->bank_branch_name = $request->bank_branch_name;
            $bank->bank_branch_manager_name = $request->bank_branch_manager_name;
            $bank->bank_branch_phone = $request->bank_branch_phone;
            $bank->bank_account_number = $request->bank_account_number;
            $bank->bank_branch_email = $request->bank_branch_email;
            $bank->save();

            return response()->json([
                'status' => 200,
                'message' => 'Bank Update Successfully',
            ]);
        } else {
            return response()->json([
                'status' => '500',
                'error' => $validator->messages()
            ]);
        }
    } catch (\Exception $e) {
        // Log the error message
        Log::error('Error to Updating bank details: ' . $e->getMessage());

        // Return the errors.500 view for internal server errors
        return response()->view('errors.500', [], 500);
    }
    }
    // public function destroy($id)
    // {
    //     $bank = Bank::findOrFail($id);
    //     $bank->delete();
    //     return response()->json([
    //         'status' => 200,
    //         'message' => 'Bank Deleted Successfully',
    //     ]);
    // }
    //Bank balance Add
    public function BankBalanceAdd(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'update_balance' => 'required',
        ]);

        if ($validator->passes()) {
            $bank = Bank::findOrFail($id);
            // dd($bank->update_balance);
            $bank->opening_balance = $bank->opening_balance + $request->update_balance;
            $bank->update_balance =  $request->update_balance;
            $bank->purpose = $request->purpose;
            $bank->update();

            $accountTransaction = new AccountTransaction;
            $accountTransaction->branch_id =  Auth::user()->branch_id;
            $accountTransaction->purpose =  'Add Bank Balance';
            $accountTransaction->account_id =  $bank->id;
            $accountTransaction->note =  $request->note;
            $accountTransaction->credit = $request->update_balance;
            $oldBalance = AccountTransaction::where('account_id', $id)->latest('created_at')->first();
            if ($oldBalance) {
                $accountTransaction->balance = $oldBalance->balance + $request->update_balance;
            } else {
                $accountTransaction->balance = $request->update_balance;
            }
            $accountTransaction->save();

            return response()->json([
                'status' => 200,
                'message' => 'Add Money Successfully',
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
        try {
            $bank = Bank::findOrFail($id);
            $newStatus = $bank->status === 'inactive' ? 'active' : 'inactive';
            $bank->update(['status' => $newStatus]);

            return response()->json([
                'status' => 200,
                'newStatus' => $newStatus,
                'message' => 'Status changed successfully.',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Bank record not found.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ]);
        }

    }
}
