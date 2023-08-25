<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\OnlyOneTrue;
use Validator;
use App\Models\EducationInfo;

class EductionInfoController extends Controller
{
    public function storeEduction(Request $req){

        $data = $req->all();
        // dd($data['education']);
        $validate_array = [
            'education' => 'required|array',
            'education.*.education' => 'required',
            'education.*.university' => 'required',
            'education.*.from' => 'required',
            'education.*.to' => 'required',
            'education.*.current_pursuing' => new OnlyOneTrue()
        ];

        $validate = Validator::make($data, $validate_array);

        if($validate->fails()){
            return response()->json($validate->errors(), 400);
        }else{
            $auth_id = auth()->user()->id;
            $education = $data['education'];
            
            foreach($education as $edu){
                $education_info = new EducationInfo();

                $education_info->user_id = $auth_id;
                $education_info->education = $edu['education'];
                $education_info->university = $edu['university'];
                $education_info->from = $edu['from'];
                $education_info->to = $edu['to'];
                $education_info->current_pursuing = $edu['current_pursuing'];
                $education_info->save();

            }
            return response()->json([
                'status' => 200,
                'message' => 'Successfully added Education Information.'
            ]);           
        }
    }
}
