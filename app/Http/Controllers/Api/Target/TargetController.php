<?php

namespace App\Http\Controllers\Api\Target;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Interest;
use App\Models\SubInterest;
use App\Models\User;
use App\Models\Target;
use App\Models\TargetInvite;
use Validator;

class TargetController extends Controller
{
    public function index(Request $req){
        $org_id = $req->org_id;

        if($org_id){
            // $targets = Target::with(['interest:id,name', 'sub_interest:id,name', 'user', 'event:id,name,category,location,event_date,start_time,meridiem,cover_photo,target_id'])            
            //                     ->where('organization_id', $org_id)->paginate(10);
            $targets = Target::select('id', 'interest_id', 'sub_interest_id', 'title', 'from', 'to', 'description', 'target_units')
                                ->with(['interest:id,name', 'sub_interest:id,name'])
                                ->where('organization_id', $org_id)
                                ->paginate(10);
        }else{
            $targets = Target::select('id', 'interest_id', 'sub_interest_id', 'title', 'from', 'to', 'description', 'target_units')
                                ->with(['interest:id,name', 'sub_interest:id,name'])
                                ->where('user_id', Auth::id())
                                ->paginate(10);
            
            // $targets = Target::where('user_id', auth()->user()->id)
            //                         ->with(['interest:id,name', 'sub_interest:id,name'])
            //                         ->get(['id', 'interest_id', 'sub_interest_id', 'title', 'from', 'to', 'description', 'incentive_prize', 'target_unique_id']);
        }       

        return response()->json([
            'status' => 200,
            'message' => 'Successfully got data.',
            'data' => $targets
        ]);

    }

    public function view_more(Request $req, $id){
        $target = Target::with(['interest:id,name', 'sub_interest:id,name'])
                        ->find($id, ['title', 'description', 'interest_id', 'sub_interest_id', 'incentive_prize', 'target_units', 'to', 'from', 'target_unique_id', 'type']);
        if($target){
            return response()->json([
                'status' => 200,
                'message' => 'Target found successfully.',
                'data' => $target
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Failed to find the target.',
            ]);  
        }
    }

    public function toggle_public_private_type(Request $req){
        // 0 => public, 1 => private, default => 1
        $data = $req->all();
        $target = Target::find($data['target_id']);
        $target->type = $data['type'];
        if($target->save()){
            return response()->json([
                'status' => 200,
                'message' => 'Target Type changed successfully.',
                'data' => $target
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Failed to update target type.'
            ]);
        }
    }

    public function interests(){
        $interests = Interest::get();

        return response()->json([
            'status' => 200,
            'message' => 'Successfully got interests.',
            'data' => $interests
        ]);  
    }

    public function subInterests(Request $req){

        $data = $req->all();

        $sub_interests = SubInterest::where('default_sub_cat', '1')
                                    ->where('interest_id', $data['interest_id'])    
                                    ->get()
                                    ->toArray();
        
        if($sub_interests){
            return response()->json([
                'status' => 200,
                'message' => 'Successfully got sub interests.',
                'data' => $sub_interests
            ]);
        }else{
            return response()->json([
                'status' => 200,
                'message' => 'Please check interest id.',
                'data' => $sub_interests
            ]);
        }
    }

    public function store(Request $req){

        $data = $req->all();
        $validate_data = [
                            'title' => 'required',
                            'type' => 'required',// 0 => public, 1 => private
                            'interest_id' => 'required',
                            'sub_interest_id' => 'required',
                            'target_units' => 'required',
                            'from' => 'required',
                            'to' => 'required',
                            'description' => 'required',
                            'photo' => 'mimes:jpeg,jpg,png,gif',
                            'incentive' => 'required',
                            'invites' => 'array',
                        ];

        $validate = Validator::make($data, $validate_data);

        if($validate->fails()){
            return response()->json([
                'status' => "failed",
                'message' => $validate->errors()
            ]);
        }

        if($data['incentive'] == 1){
            $validate_data = array_merge($validate_data, ['incentive_prize' => 'required']);
        }

        $validate = Validator::make($data, $validate_data);

        if($validate->fails()){
            return response()->json([
                'status' => "failed",
                'message' => $validate->errors()
            ]);
        }

        $check_target_unique_id = random_int(0000000000000000, 9999999999999999);
        $target_unique_id = $this->check_unique_target_id($check_target_unique_id);

        $target = new Target();
        if($req->has('organization_id')){
            $target->organization_id = $data['organization_id'];
        }else{
            $user_id = auth()->user()->id;
            $target->user_id = $user_id;
        }
        $target->target_unique_id = $target_unique_id;
        $target->title = $data['title'];
        $target->type = $data['type'];
        $target->interest_id = $data['interest_id'];
        $target->sub_interest_id = $data['sub_interest_id'];
        $target->target_units = $data['target_units'];
        $target->from = $data['from'];
        $target->to = $data['to'];
        $target->description = $data['description'];
        if($req->hasFile('photo')){
            $original_name = $req->photo->getClientOriginalName();
            $upload_file_name = time().'_'.$original_name;
            $req->photo->move(public_path('/target_post_images'), $upload_file_name);
            $target->photo = $upload_file_name;
        }
        $target->incentive = $data['incentive'];

        if($data["incentive"] == 1 && array_key_exists("incentive_prize", $data)){
            $target->incentive_prize = $data['incentive_prize'];
        }   

        if($target->save()){

            $target_id = $target->id;
            if($req->has('invites')){
                $invites = $data['invites'];
                foreach($invites as $invite){
                    $target_invite = New TargetInvite();
                    $target_invite->target_id = $target_id;
                    $target_invite->invited_user_id = $invite;
                    $target_invite->accepted = '0';
                    $target_invite->save();
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Target added successfully.'
            ]);

        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Target failed to add.'
            ]);
        }
    }

    private function check_unique_target_id($id){
        $chk = Target::where('target_unique_id', $id)->count();
        if($chk != 0){
            $id = random_int(0000000000000000, 9999999999999999);
            $this->check_unique_target_id($id);
        }
        return $id;
        //     Target::get('target_unique_id')->pluck('target_unique_id')->toArray();
        //     if (in_array($id, $target_unique_id_array)){
        //         $target_unique_id = random_int(0000000000000000, 9999999999999999);
        //         $this->check_unique_target_id($target_unique_id);
        //     }
        //     return $id;
    }

    public function edit($id){
        $target = Target::with('targetInvites')->find($id);
        if($target){
            return response()->json([
                'status' => 200,
                'message' => 'Target found successfully.',
                'data' => $target
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Failed to find the target.',
            ]);  
        }
    }

    public function update(Request $req, $id){
        $data = $req->all();

        $validate_data = [
            'title' => 'required',
            'type' => 'required', // 0 => public, 1 => private
            'interest_id' => 'required',
            'sub_interest_id' => 'required',
            'target_units' => 'required',
            'from' => 'required',
            'to' => 'required',
            'description' => 'required',
            'photo' => 'mimes:jpeg,jpg,png,gif',
            'incentive' => 'required',
            'invites' => 'array',
        ];

        $validate = Validator::make($data, $validate_data);
        if($validate->fails()){
            return response()->json([
                'status' => "failed",
                'message' => $validate->errors()
            ]);
        }

        if($data['incentive'] == 1){
            $validate_data = array_merge($validate_data, ['incentive_prize' => 'required']);
        }
       
        $validate = Validator::make($data, $validate_data);

        if($validate->fails()){
            return response()->json([
                'status' => "failed",
                'message' => $validate->errors()
            ]);
        }

        $target = Target::find($id);

        if($req->hasFile('photo')){
            $delete_old_image = public_path('/target_post_images/');
            unlink($delete_old_image.$target->photo);

            $original_name = $req->photo->getClientOriginalName();
            $file_name_after = time().$original_name; 
            $req->photo->move(public_path('/target_post_images'), $file_name_after); 
            $target->photo = $file_name_after;
        }

        $target->title = $data['title'];
        $target->type = $data['type'];
        $target->interest_id = $data['interest_id'];
        $target->sub_interest_id = $data['sub_interest_id'];
        $target->target_units = $data['target_units'];
        $target->from = $data['from'];
        $target->to = $data['to'];
        $target->description = $data['description'];
        // $target->photo = $upload_file_name;
        $target->incentive = $data['incentive'];

        if($data["incentive"] == 1 && array_key_exists("incentive_prize", $data)){
            $target->incentive_prize = $data['incentive_prize'];
        }else{
            $target->incentive_prize = null;
        }

        if($target->save()){

            if($req->has('invites')){

                $target_invite = $data['invites'];
                $old_target_invites = TargetInvite::where('target_id', $id)->pluck('invited_user_id')->toArray();
                $new_invites = array_diff($target_invite, $old_target_invites);

                foreach($new_invites as $invite){
                    $target_invite = New TargetInvite();
                    $target_invite->target_id = $id;
                    $target_invite->invited_user_id = $invite;
                    $target_invite->accepted = '0';
                    $target_invite->save();
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Target Updated Successfully.',
            ]);

        }else{
            return response()->json([
                'status' => 200,
                'message' => 'Target Failed to Update.',
            ]);
        }

    }

    public function destroy($id){
        $target = Target::with(['event'])->find($id);

        if($target){

            if($target->event){
                foreach($target->event as $events){
                    if($events->cover_photo){
                        $delete_old_event_photo = public_path('/event_cover_images/');
                        unlink($delete_old_event_photo.$events->cover_photo);
                    }
                }
            }

            if($target->photo != null){
                $delete_old_photo = public_path('/target_post_images/');
                unlink($delete_old_photo.$target->photo);
            }

            if($target->delete()){
                return response()->json([
                    'status' => 200,
                    'message' => 'Target Deleted Successfully.',
                ]);
            }else{
                return response()->json([
                    'status' => 200,
                    'message' => 'Failed to delete target.',
                ]);
            }

        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Failed to found target.',
            ]);  
        }
    }

}
