<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubInterest;
use App\Models\Interest;
use App\Models\UserInterest;
use DB;

class InterestController extends Controller
{
    // return default sub interests as per seeder
    public function getDefaultInterest(){ 

        // \DB::enableQueryLog(); // Enable query log
        // $interests = Interest::whereHas('subInterest', function ($query) {
        //     $query->select()->where('default_sub_cat', '1');
        // })->with('subInterest')->get()->toArray();
        // dd(\DB::getQueryLog()); 
        $interests = Interest::with('default_sub_cat')->get()->toArray();

        if($interests){
            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $interests
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Failed', 
            ]);
        }
    }

    // return default and user added sub interests
    public function storeSubDefaultInterestByUser(Request $req){

        $data = $req->all();
        $sub_interest = SubInterest::create([
            'interest_id' => $data['interest_id'],
            'user_id' => auth()->user()->id,
            'default_sub_cat' => 0,
            'name' => $data['name']
        ]);

        // $user_interest = UserInterest::create([
        //     'user_id' => auth()->user()->id,
        //     'sub_interest_id' => $sub_interest->id
        // ]);

        if($sub_interest){

            $sub_interest_with_user = SubInterest::where('default_sub_cat', 1)->orwhere('user_id', auth()->user()->id)->get();

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $sub_interest_with_user
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'failed'
            ]);
        }
        
    }

    public function storeUserInterest(Request $req){

        $data = $req->all();
        // dd($data['sub_interest']);
        $sub_interest = $data['sub_interest'];

        foreach($sub_interest as $si){
            $user_interest = new UserInterest();
            $user_interest->user_id = auth()->user()->id;
            $user_interest->sub_interest_id = $si;
            $user_interest->save();
        }

        if($user_interest){
            $user_sub_interest = UserInterest::where('user_id', auth()->user()->id)->get();
            return response()->json([
                'success' => 200,
                'data' => $user_sub_interest
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'failed'
            ]);
        }        

    }
}
