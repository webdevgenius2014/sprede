<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\ContactInfo;
use App\Models\User;
use SplFixedArray;

class ContactInfoController extends Controller
{
    // public function __construct() {
    //     $this->middleware('auth:api');
    // }
    
    public function contactInfo(Request $request){
        $data = $request->all();

        $validator = Validator::make($request->all(), [
            'permanent_add_city' => 'required',
            'permanent_add_country' => 'required',
        ]);

        // $same_as_permanent_add = array_key_exists('same_as_permanent_add', $data);
        // $request->has('same_as_permanent_add') && ()
        if($request->has('same_as_permanent_add') && ($request->same_as_permanent_add == null || $request->same_as_permanent_add == "0" )){
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
            if($request->has('same_as_permanent_add') && ($request->same_as_permanent_add == null || $request->same_as_permanent_add == "0" )){
                $contact_info->same_as_permanent_add = '0';
                $contact_info->current_add_city = $request->current_add_city;
                $contact_info->current_add_country = $request->current_add_country;
            }else{
                $contact_info->same_as_permanent_add = '1';
                $contact_info->current_add_city = $request->permanent_add_city;
                $contact_info->current_add_country = $request->permanent_add_country;
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
    // getUsername function for getting valid and available usernames(ajax) // 
    public function getUsername(Request $request){
        $data = $request->all();
        $user_id = auth()->user()->id;
        $user = User::find($user_id);

        if($user->update_username == '1' || $user->username != null){
            return response()->json([
                'status' => 400,
                'success' => 'failed',
                'message' => 'You have already chosen the username.'
            ]);
        }
                
        $username = $data['username'];
        $username = str_replace(' ', '', $username);

        $validate_require = Validator::make($data, [
            'username' => 'required'
        ]);
        if($validate_require->fails()){
            return response()->json([$validate_require->errors()], 400);
        }
        
        $validate = Validator::make($data, [
            'username' => 'required|unique:users|min:5|regex:/^[A-Za-z0-9_]+$/'
        ],[
            'username.regex' => "Please enter valid username with letters, numbers and underscores."
        ]);

        $user_demographic = User::find($user_id)->demographicInfo;
        $fix_suggest_username = new SplFixedArray(6);
        if($user_demographic){
            if($user_demographic->first_name){
                if($user_demographic->last_name){
                    $first_name = $user_demographic->first_name;
                    $last_name = $user_demographic->last_name;
                    $fix_suggest_username[0] = $this->generate_username($first_name.$last_name, []);
                    $fix_suggest_username[1] = $this->generate_username($first_name."_".$last_name, []);
                }
            }
        }
        $array = $fix_suggest_username->toArray();
        $index = 0;
        if($array[1]){
            $index = 2;
        }
        // $suggest_username = [];

        if($validate->fails()){

            for($i=$index; $i<=5; $i++){

                $fix_suggest_username[$i] = $this->generate_username($username, $fix_suggest_username->toArray());
                // array_push($suggest_username, $this->generate_username($username));
            }
            return response()->json([$validate->errors(),
                                        'suggestions' => $fix_suggest_username,
                                    ], 400);
        }
        // $sggestion_without_error = [];

        for($i=$index; $i<=5; $i++){
            $fix_suggest_username[$i] = $this->without_error_generate_username($username, $fix_suggest_username->toArray());
            // array_push($sggestion_without_error, $this->without_error_generate_username($username));
        }

        return response()->json([
            'status' => 200,
            'message' => 'Username available.',
            'suggestions' => $fix_suggest_username
        ]);
    }

    // generate_username function return unique usernames if you got any error on getUsername function
    public function generate_username($name, $array){
        $username = $name;
        // dd($array);
        if(in_array($username, $array)){
            // dd("yes in array", $array);
            $uniqueUserName = $username.rand(0, 99);
            $username = $this->generate_username($uniqueUserName, $array);
        }elseif(User::where('username', '=', $name)->exists()){
            $uniqueUserName = $name.rand(0, 99);
            $username = $this->generate_username($uniqueUserName, $array);
        }
        return $username;
    }

    // generate_username function return unique usernames if you have valid username getUsername function
    public function without_error_generate_username($name, $array){
        $username = $name.rand(0, 99);
        // print_r($array);
        if(in_array($username, $array)){
            $uniqueUserName = $username.rand(0, 99);
            $username = $this->without_error_generate_username($uniqueUserName, $array);
        }elseif(User::where('username', '=', $username)->exists()){
            $uniqueUserName = $name.rand(0, 99);
            $username = $this->without_error_generate_username($uniqueUserName);
        }
        return $username;
    }

    public function storeUsername(Request $request){
        $data = $request->all();
        
        $user_id = auth()->user()->id;
        $user = User::find($user_id);

        if($user->update_username == '1' || $user->username != null){
            return response()->json([
                'status' => 400,
                'success' => 'failed',
                'message' => 'You have already chosen the username.'
            ]);
        }

        $validate = Validator::make($data, [
            'username' => 'required|unique:users|min:5|regex:/^[A-Za-z0-9_]+$/'
        ]);

        if($validate->fails()){
            return response()->json([
                'status' => "failed",
                'message' => $validate->errors()
            ], 400);
        }else{
            $user->username = $data['username'];
            $user->update_username = '1';
            if($user->save()){
                return response()->json([
                    'status' => 'success',
                    'message' => 'Username Updated successfully.'
                ], 200);
            }            
        }
    }

}
