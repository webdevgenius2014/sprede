<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\ContactInfo;
use App\Models\User;

class ContactInfoController extends Controller
{
    // public function __construct() {
    //     $this->middleware('auth:api');
    // }
    
    public function contact_info(Request $request){
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'permanent_add_city' => 'required',
            'permanent_add_country' => 'required',
        ]);

        $same_as_permanent_add = array_key_exists('same_as_permanent_add', $data);

        if(!$same_as_permanent_add){
            $validator = Validator::make($request->all(), [
                'current_add_city' => 'required',
                'current_add_country' => 'required',
            ]);
        }

        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }else{ 
            $contact_info = new ContactInfo();        
            $contact_info->user_id = auth()->id();
            $contact_info->permanent_add_city = $request->permanent_add_city;
            $contact_info->permanent_add_country = $request->permanent_add_country;
            if(!$same_as_permanent_add){
                $contact_info->same_as_permanent_add = $request->same_as_permanent_add;
                $contact_info->current_add_city = $request->current_add_city;
                $contact_info->current_add_country = $request->current_add_country;
            }else{
                $contact_info->same_as_permanent_add = 0;
            }
            $contact_info->save();
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => 'Contact Infomation saved successfully.',
                'data'=>$contact_info
            ]);
        }
    }

    // public function get_username(Request $request){
    //     $data = $request->all();

    //     $validate = Validator::make($data, [
    //         'username' => 'required|unique:users'
    //     ]);

    //     if($validate->fails()){
            
    //         $user_given_name = $data['username'];

    //         $all_username = User::all('username')->toArray();
    //         $suggest_username = [];
    //         for($i=0; $i<6; $i++){
                
    //         }

    //         dd($all_username);  

    //         return response()->json([$validate->errors(),
    //                                 'usernames' => $all_username,
    //                                 ], 400);
    //     }
    // }

    // public function generateUserName($name){
    //     $username = Str::lower(Str::slug($name));
    //     if(User::where('username', '=', $username)->exists()){
    //         $uniqueUserName = $username.'-'.Str::lower(Str::random(4));
    //         $username = $this->generateUserName($uniqueUserName);
    //     }
    //     return $username;
    // }


}
