<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use App\Rules\OnlyOneTrue;
use App\Models\EmploymentInfo;

class EmploymentController extends Controller
{
    public function storeEmploymentInfo(Request $request){
        $data = $request->all();
        $validate_array = [
            'organisation' => 'required|array',
            'organisation.*.name' => 'required',
            'organisation.*.designation' => 'required',
            'organisation.*.from' => 'required',
            'organisation.*.to' => 'required',
            'organisation.*.current_work_here' => new OnlyOneTrue()
        ];

        if(array_key_exists("on_privacy", $data) && $data["on_privacy"] == 0){
            $validate_on_privacy = [   
                'organisation.*.org_city' => 'required',
                'organisation.*.org_country' => 'required'
            ];
            $validate_array = array_merge($validate_array, $validate_on_privacy);
        }

        $validation = Validator::make($data, $validate_array, [
            'organisation_name.*.required' => 'Organisation Name is required.',
            'designation.*.required' => 'Designation is required.',
            'from.*.required' => 'From is required.',
            'to.*.required' => 'To is required.',
        ]);


        if($validation->fails()){
            return response()->json($validation->errors(), 400);
        }else{
            $auth_id = auth()->user()->id;
            $organisation = $data['organisation'];
            
            foreach($organisation as $org){
                $employment_info = new EmploymentInfo();

                $employment_info->user_id = $auth_id;
                $employment_info->is_defence = $data['is_defence'];
                $employment_info->on_privacy = $data['on_privacy'];

                $employment_info->organization = $org['name'];
                $employment_info->designation = $org['designation'];
                $employment_info->from = $org['from'];
                $employment_info->to = $org['to'];
                if(!array_key_exists('current_work_here', $org) || $org['current_work_here'] == "" ){
                    $employment_info->current_work_here = "0";
                }else{
                    $employment_info->current_work_here = $org['current_work_here'];
                }

                if(array_key_exists("on_privacy", $data) && $data["on_privacy"] == 0){
                    $employment_info->org_city = $org['org_city'];
                    $employment_info->org_country = $org['org_country'];
                }

                $employment_info->save();

            }
            return response()->json([
                'status' => 200,
                'message' => 'Successfully added Employmet Information.'
            ]);           
        }
    }
}
