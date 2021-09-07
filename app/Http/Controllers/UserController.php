<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Illuminate\Support\Facades\Validator;

use Mail;

use Session;

use Storage;

use Illuminate\Http\File;

use Auth;

use App\Models\User;

use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //

    public function registerUser(Request $request){
        
        $rules = [
            "user_name" => "required|min:4|max:20|unique:users",
            "password" => "required",
            "email" => "required|unique:users"
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => "400",
                'message' => $validator->messages()->first(),
            ]);
        }

        $verification_pin = mt_rand(100000, 999999);

        $userData = [
            'user_name' => $request->get('user_name'),
            'password' => Hash::make($request->get("password")),
            'email' => $request->get("email"),
            'verification_pin' => $verification_pin,
            'user_role' => "user"
        ];

        $user = User::create($userData);

        if($user)
        {
            $data = array("pin" => $user->verification_pin);
            $email = $user->email;
            Mail::send(['text'=>'pin'], $data, function($message) use ($email) {
                $message->to($email, 'Signup Email')->subject
                   ('Signup Mail');
                $message->from('zohaibzebi66@gmail.com','Admin');
             });

            session_start();
            $_SESSION['pin'] = $verification_pin;
            $_SESSION['user_id'] = $user->id;
        }

        $response['status'] = "200";
        $response['message'] = 'Registered Successfully. A Pin Code is sent to  your email please veiry that';
        return response()->json($response);
    }


    public function validatePin(Request $request) {
        session_start();
       
      if($request->input('pin') == $_SESSION['pin']){
          $user = User::find($_SESSION['user_id']);
          $user->verified = true;
          $user->update();

        $response['status'] = "200";
        $response['message'] = 'Successfully Validated Now you can login';
        return response()->json($response);
      }else{
        $response['status'] = "400";
        $response['message'] = 'Validation Failed';
        return response()->json($response);
      }
    }


    public function userLogin(Request $request){

        $rules = [
            "email" => "required",
            "password" => "required"
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => "400",
                'message' => $validator->messages()->first(),
            ]);
        }

        $user = User::where('email', $request->input('email'))->first();

        if($user == null)
        {
            $response['status'] = "400";
            $response['message'] = 'User Not Found';
            return response()->json($response);  
        }

        if($user->verified != true){
        $response['status'] = "400";
        $response['message'] = 'Account Not Verified';
        return response()->json($response);
        }


        $userdata = array(
            'email'     => $request->input('email'),
            'password'  => $request->input('password')
        );

        if (Auth::attempt($userdata)) {
        $token = mt_rand(100000, 999999);
        $user->token = $token;
        $user->update();
        $response['token'] = $token;
        $response['status'] = "200";
        $response['message'] = 'Login Success';

        return response()->json($response);
    
        } else {        
    
        $response['status'] = "400";
        $response['message'] = 'Login Faild';
        return response()->json($response);
    
        }

    }


    public function updateProfile(Request $request)
    {
        $user = User::where('token', $request->input('token'))->first();
        if($user != null)
        {
            $avatar = 'avatar';
            $user->name = $request->input('name');
            if ($request->hasFile('avatar')) {

                if(!Storage::exists($avatar)){
                    Storage::makeDirectory($avatar);
                }
    
                $avatarURL = Storage::putFile($avatar, new File($request->file('avatar')));
                $user->avatar = $avatarURL;
            }

            $user->update();

            $response['status'] = "200";
            $response['message'] = 'Profile Updated Successfully';
            return response()->json($response);
        

        }else{
        $response['status'] = "400";
        $response['message'] = 'Something Went wrong';
        return response()->json($response);
     
        }
       
    }

}
