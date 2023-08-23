<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DemographicInfo;
use Validator;
use Auth;
use Carbon\Carbon;

class UserController extends Controller
{
    public function updateDemographic(Request $request)
    {   
        try{
             $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|between:2,50|regex:/^[a-zA-Z\s]*$/',
                'middle_name' => 'nullable|string|between:2,50|regex:/^[a-zA-Z\s]*$/',
                'last_name' => 'nullable|string|between:2,50|regex:/^[a-zA-Z\s]*$/',
                'about_me' => 'required|string',
                'dob' => 'required|date|before:'.Carbon::now().'',
                'gender' => 'required|in:male,female,other',
                'profile_img' => 'required|mimes:jpeg,jpg,png,gif',
                'cover_img' => 'required|mimes:jpeg,jpg,png,gif',
            ]);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
            if(auth()->check())
            {
                if($request->hasFile('profile_img'))
                {
                  $filenameWithExt = $request->file('profile_img')->getClientOriginalName();
                  $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                  $extension = $request->file('profile_img')->getClientOriginalExtension();
                  $uploadProfileImage=$filename.'.'.$extension;
                  $path = $request->file('profile_img')->move(public_path('/images'), $uploadProfileImage);
                  $uploadProfile= $path;
                }

                if($request->hasFile('cover_img'))
                {
                  $filenameWithExt = $request->file('cover_img')->getClientOriginalName();
                  $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                  $extension = $request->file('cover_img')->getClientOriginalExtension();
                  $uploadCoverImage=$filename.'.'.$extension;
                  $path = $request->cover_img->move(public_path('/images'), $uploadCoverImage);
                  $uploadCover= $path;
                }
                $saveinfo = new DemographicInfo();        
                $saveinfo->user_id = Auth::id();
                $saveinfo->first_name = $request->first_name;
                $saveinfo->middle_name = $request->middle_name;
                $saveinfo->last_name = $request->last_name;
                $saveinfo->about_me = $request->about_me;
                $saveinfo->dob = $request->dob;
                $saveinfo->gender = $request->gender;
                $saveinfo->profile_img = $uploadProfileImage;
                $saveinfo->cover_img = $uploadCoverImage;
                $saveinfo->save();

                return response()->json(['success' => true, 'message' => 'Demographic Information saved successfully','data'=>$saveinfo], 200)->header('status', 200);
            }else{
                return response()->json(['error' => 'Unauthorized'], 401);
            }

        }catch (Exception $e) 
        {
            return $e;
        }

    }
    
}
