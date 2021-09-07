<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Mail;

use Illuminate\Support\Facades\Validator;



class MailController extends Controller
{
    //

    public function send_mail(Request $request) {

        $email = $request->input("email");

        $rules = [
            "email" => "required",
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()) {
            return response()->json([
                'status' => "400",
                'message' => "Email Is Required"
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'status' => "400",
                'message' => "Email is invalid"
            ]);
         }


        $data = array('name'=>"Admin");
     
        Mail::send(['text'=>'mail'], $data, function($message) use ($email) {
           $message->to($email, 'Signup Email')->subject
              ('Signup Mail');
           $message->from('zohaibzebi66@gmail.com','Admin');
        });
        
        return response()->json([
            'status' => "200",
            'message' => "Email Sent Successfully"
        ]);
     }

}
