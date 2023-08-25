<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubInterest;
use App\Models\Interest;
use DB;

class InterestController extends Controller
{
    public function getInterest(){
        \DB::enableQueryLog(); // Enable query log

        // Your Eloquent query executed by using get()

        //$sub_interest = SubInterest::where('default_sub_cat', 1)->with('interest')->get()->toArray();
        // $interests = Interest::with('subInterest', function ($query) {
        //     $query->where('default_sub_cat', '1');
        // })->get()->toArray();

        $interests = Interest::whereHas('subInterest', function ($query) {
            $query->where('default_sub_cat', '1');
        })->with('subInterest')->get()->toArray();

        // $posts = Interest::whereHas('subInterest', function ($query) {
        //     $query->where('default_sub_cat', '1');
        // })->with('subInterest')->get()->toArray();
            // dd($posts);

        $posts = Interest::join('sub_interests', 'interests.id', '=', 'sub_interests.interest_id')
                            ->where('sub_interests.default_sub_cat', '1')->with('subInterest')->get()->toArray();
        
        dd($posts);
        // ['interests.*', 'sub_interests.*']
        
        dd(\DB::getQueryLog()); 

        // $posts = Interest::with('subInterest')->get()->toArray();

    }
}
