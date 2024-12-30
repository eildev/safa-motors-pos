<?php

namespace App\Http\Controllers;

use App\Models\SmsCategory;
// use Validator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Mail\BulkMail;
use App\Models\User;
use App\Models\Customer;
use App\Jobs\SendBulkEmails;
use App\Models\Sms;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CRMController extends Controller
{
    function smsToCustomerPage()
    {
        return view('pos.crm.sms-marketing');
    }
    function smsToCustomer(Request $request)
    {
        // Assuming $request->number and $request->sms are provided correctly
        $url = "http://bulksmsbd.net/api/smsapimany";
        $api_key = "0yRu5BkB8tK927YQBA8u";
        $senderid = "8809617615171";
        $numbers = explode(",", $request->number);
        $messages = [];
        $sms = $request->sms;
        foreach ($numbers as $number) {
            $messages[] = [
                "to" => $number,
                "message" => $sms
            ];
        }

        // Construct the full data payload
        $data = [
            "api_key" => $api_key,
            "senderid" => $senderid,
            "messages" => $messages // This should be an array of messages
        ];

        // JSON encode the entire data array
        $jsonData = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData); // Send JSON data
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json')); // Set appropriate content type
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);

        // Store SMS details in the database
        foreach ($numbers as $number) {
            $customer = Customer::where('phone', $number)->first();
            if ($customer) {
                // Create SMS record
                try {
                    Sms::create([
                        'customer_id' => $customer->id,
                        'purpose' => $request->purpose,
                        'number' => $number,
                        'message' => $sms,
                        // You may want to store additional information like API response or status
                    ]);
                } catch (\Exception $e) {
                    // Optionally, you can also add a flash message to inform the user about the error
                    return back()->with('error', $e->getMessage());
                }
            }
        }

        // Handle response and return
        if (isset($error_message)) {
            return back()->with('error', $error_message);
        } else {
            return back()->with('message', 'SMS Submitted Successfully');
        }
    }
    public function smsCategoryStore(Request $request)
    {
        // dd($request->all());
    }
    public function emailToCustomerPage()
    {
        return view('pos.crm.email.compose');
    }
    public function emailToCustomerSend(Request $request)
    {

        $content = $request->message;
        $mails = $request->mails;
        $subject = $request->subject;
        // dd($mails);
        foreach ($mails as $mail) {
            Mail::to($mail)->queue(new BulkMail($content, $subject));
        }

        // SendBulkEmails::dispatch($mails,$subject,$content);

        $notification = array(
            'message' => 'Email successfully sent',
            'alert-type' => 'info'
        );
        return back()->with($notification);
    }
    public function storeSmsCat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        if ($validator->passes()) {
            $smsCat = new SmsCategory;
            $smsCat->name = $request->name;
            $smsCat->save();

            return response()->json([
                'status' => 200,
                'data' => $smsCat,
                'message' => "Successfully saved"
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'error' => $validator->messages()
            ]);
        }
    }
    public function viewSmsCat()
    {
        $smsCat = SmsCategory::get();
        return response()->json([
            'status' => 200,
            'data' => $smsCat,
        ]);
    }
    public function updateSmsCat(Request $request, $id)
    {
        // dd($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);
        if ($validator->passes()) {
            $smsCat = SmsCategory::findOrFail($id);
            $smsCat->name = $request->name;
            $smsCat->save();

            return response()->json([
                'status' => 200,
                'message' => "Successfully Updated"
            ]);
        } else {
            return response()->json([
                'status' => 500,
                'error' => $validator->messages()
            ]);
        }
    } //

    public function deleteSmsCat($id)
    {
        $smsCat = SmsCategory::findOrFail($id);
        $smsCat->delete();
        return response()->json([
            'status' => 200,
            'message' => "Successfully Deleted"
        ]);
    }
    public function CustomerlistView()
    {
        $customer =  Customer::all();
        // $customer =  Customer::latest()->get();
        return view('pos.crm.customize_customer.customize_customer', compact('customer'));
    } //
    public function CustomerlistFilterView(Request $request)
    {

        if (is_numeric($request->filterCustomer)) {
            $monthsToSubtract = intval($request->filterCustomer);
            $oneMonthAgo = Carbon::now()->subMonths($monthsToSubtract);
        } else {
            // Handle the error or default case
            $oneMonthAgo = Carbon::now();  // Default to current time or other appropriate default
        }

        $customerQuery = Customer::query();

        // Check if the filter should not include customers who made a purchase after a specific date.
        if ($request->filterCustomer != "Did not purchase") {
            $customerQuery->whereDoesntHave('sales', function ($query1) use ($oneMonthAgo) {
                $query1->where('created_at', '>', $oneMonthAgo);
            });
        }
        // Apply a date range filter if both start and end dates are provided.
        if ($request->start_date && $request->end_date) {
            $customerQuery->whereDoesntHave('sales', function ($query1) use ($request) {
                $query1->whereBetween('created_at', [$request->start_date, $request->end_date]);
            });
        }
        // Execute the query
        $customer = $customerQuery->get();
        return view('pos.crm.customize_customer.customize_customer-table', compact('customer'))->render();
    }
}
