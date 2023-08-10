<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Mail;
use Carbon\Carbon;
use Auth;
use Hash;


class ForgetpasswordController extends Controller
{
    public function sendOtp(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $user = User::where('email',$request->email)->first();
            if($user)
            {
                $otp= rand(100000, 999999);
                $userUpdate = User::where('email',$request->email)->update([
                    'email_otp'=> $otp,
                ]);
                $mail = Mail::send('emails.email', ['otp' => $otp,'user'=>$user], function($message) use($request){
                    $message->to($request->email);
                    $message->subject('verification for Forgot Password.');
                });

                return response()->json(['success' => false, 'message' => 'OTP send for verification'], 200)->header('status', 200);
            }else{
                return response()->json(['success' => false, 'message' => 'User does not exist'], 404)->header('status', 404);
            }

        }catch (Exception $e) 
        {
            return $e;
        }
    }

    public function verifyOtp(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp' =>'required|digits:6',
                'password' => 'required|string|confirmed|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            $user = User::where('email',$request->email)->first();
            if($user)
            {
                if($user->email_otp == $request->otp)
                {
                    $user->email_verified_at = Carbon::now();
                    $user->password = Hash::make($request->password);
                    $user->save();
                    $token = Auth::login($user);
                    return response()->json(['success' => false, 'message' => 'Email verified successfully','data' => $user,'access_token'=>$token], 200)->header('status', 200);
                }else{
                    return response()->json(['success' => false, 'error' => 'Invalid OTP'], 422)->header('status', 422);
                }
            }else{
                return response()->json(['success' => false, 'message' => 'User does not exist'], 404)->header('status', 404);
            }

        }catch (Exception $e) 
        {
            return $e;
        }
    }

}
