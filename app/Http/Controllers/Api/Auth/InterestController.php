<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubInterest;
use App\Models\Interest;
use App\Models\UserInterest;
// use DB;

class InterestController extends Controller
{
    public function getInterest(){
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

    public function storeSubDefaultInterest(Request $req){
       
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
            return response()->json([
                'success' => 200,
                'message' => 'success',
                'data' => $sub_interest
            ]);
        }else{
            return response()->json([
                'success' => 400,
                'message' => 'failed',
            ]);
        }
        
    }
}
