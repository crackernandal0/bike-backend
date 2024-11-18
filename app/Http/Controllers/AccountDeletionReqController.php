<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AccountDeletionReqController extends Controller
{
    public function accountDeletionRequest()
    {
        return view('pages.account-deletion-req');
    }
    public function submitAccountDeletionRequest(Request $request)
    {

        $data = [
            "title" => "Account deletion request",
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "email" => $request->email,
            "phone_number" => $request->phone_number,
            "reason" => $request->reason,
            "type" => $request->type,
            "details" => $request->details,
        ];
        Mail::send('mails.account-del-req-mail', ['data' => $data], function ($message) use ($data) {
            $message->to('harshwebsitedev@gmail.com')->subject($data['title']);
        });

        return redirect()->back()->with('success', 'Request successfully submitted!');
    }
}
