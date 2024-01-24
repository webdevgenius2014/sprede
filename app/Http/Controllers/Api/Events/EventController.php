<?php

namespace App\Http\Controllers\Api\Events;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\EventInvite;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(Request $req){
        $target_id = $req->target_id;

        $events = Event::where('target_id', $target_id)
                        // ->select('id', 'target_id', 'name', 'location', 'event_date', 'start_time', 'meridiem', 'cover_photo') 
                        ->with('target:id,target_unique_id')
                        ->get(['id', 'target_id', 'name', 'location', 'event_date', 'start_time', 'meridiem', 'cover_photo']);
        // dd(count($events->toArray()));
        if(count($events->toArray())){
            $events_array = $events->toArray();
            // foreach($events_array as $key => $e_a){
            //     $events_array[$key]['event_activity'] = unserialize($e_a['event_activity']);
            // }
            return response()->json([
                'status' => 200,
                'message' => 'Successfully got data.',
                'data' => $events_array
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'No data found for the provided target id.'
            ]);
        }
    }

    public function view(Request $req, $id){
        $event = Event::with(['target:id,target_unique_id'])->find($id);
        if($event){
            return response()->json([
                'status' => 200,
                'message' => 'Successfully got data.',
                'data' => $event
            ]);
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'No Event found.'
            ]);
        }
    }

    public function store(Request $req){

        $input = $req->all();

        $rules = [
            // 'user_id',
            // 'organization_id',
            'event_name' => 'required',
            'event_category' => 'required',
            'event_type' => 'required', // 0 => public, 1 => private
            'event_location' => 'required',
            'event_date' => 'required',
            'event_start_time' => 'required',
            'event_meridiem' => 'required',
            'event_description' => 'required',
            'event_cover_photo' => 'mimes:jpeg,jpg,png,gif',
            'target_id' => 'required',
            'event_validator_charging_mode' => 'required',
            'event_validator_id' => 'required',
            'event_vendor_id' => 'required',
            'event_frequency' => 'required', // 0 => One Time, 1 => Recurring, 2 => Ad-Hoc
            'event_mode_of_participation' => 'required',
            'event_payment' => 'required', // 0 => Free
            'event_incentive_type' => 'required', // 1 => Incentivized, 0 => Unincentivized
            // 'event_incentive_price' => 'required',
            'event_activity' => 'required'
        ];
        
        $validate = Validator::make($input, $rules);
        if($validate->fails()){
            return response()->json([
                'status' => "failed",
                'message' => $validate->errors()
            ]);
        }

        if($input['event_frequency'] == 0){ // One Time
            $rules = array_merge($rules, [
                                            'event_frequency_start_date' => 'required',
                                            'event_frequency_end_date' => 'required'
                                        ]
                                );
        }

        if($input['event_frequency'] == 1){ // Recurring condtion
            $rules = array_merge($rules, [
                                            'event_frequency_start_date' => 'required',
                                            'event_frequency_end_date' => 'required',
                                            'event_recurring_time' => 'required',
                                            'event_continuous_time' => 'required'
                                        ]
                                );
        }

        if($input['event_frequency'] == 2){ // Ad-Hoc condtion
            $rules = array_merge($rules, [
                                            'event_frequency_end_date' => 'required',
                                            'event_continuous_time' => 'required'
                                        ]
                                );
        }

        if($input['event_incentive_type'] == 1){
            $rules = array_merge($rules, [
                                        'event_incentive_price' => 'required',
                                    ]
                            );
        }

        $validate = Validator::make($input, $rules);
        
        if($validate->fails()){
            return response()->json([
                'status' => "failed",
                'message' => $validate->errors()
            ]);
        }

        $upload_file_name = '';
        if($req->has('event_cover_photo')){
            $original_name = $req->event_cover_photo->getClientOriginalName();
            $upload_file_name = time()."_".$original_name;
            $req->event_cover_photo->move(public_path('/event_cover_images'), $upload_file_name);
        }
        
        $event = new Event;
        if($req->has('organization_id')){
            $event->organization_id = $input['organization_id'];
        }else{
            $user_id = auth()->user()->id;
            $event->user_id = $user_id;
        }
        $event->name = $input['event_name'];
        $event->category = $input['event_category'];
        $event->type = $input['event_type'];
        $event->location = $input['event_location'];
        $event->event_date = $input['event_date'];
        $event->start_time = $input['event_start_time'];
        $event->meridiem = $input['event_meridiem'];
        $event->description = $input['event_description'];
        $event->cover_photo = $upload_file_name;
        $event->target_id = $input['target_id'];
        $event->validator_charging_mode = $input['event_validator_charging_mode'];
        $event->validator_id = $input['event_validator_id'];

        if($req->has('event_validator_optional_id')){ // Optional
            $event->validator_optional_id = $input['event_validator_optional_id'];
        }
        $event->vendor_id = $input['event_vendor_id'];
        $event->frequency = $input['event_frequency'];
        $event->frequency_start_date = $input['event_frequency_start_date'];
        $event->frequency_end_date = $input['event_frequency_end_date'];

        if($input['event_frequency'] == 0){ // One Time
            $event->frequency_start_date = $input['event_frequency_start_date'];
            $event->frequency_end_date = $input['event_frequency_end_date'];
        }

        if($input['event_frequency'] == 1){ // Recurring condtion
            $event->frequency_start_date = $input['event_frequency_start_date'];
            $event->frequency_end_date = $input['event_frequency_end_date'];
            $event->recurring_time = $input['event_recurring_time'];
            $event->continuous_time = $input['event_continuous_time'];
        }

        if($input['event_frequency'] == 2){ // Ad-Hoc condtion
            $event->frequency_end_date = $input['event_frequency_end_date'];
            $event->continuous_time = $input['event_continuous_time'];
        }

        $event->mode_of_participation = $input['event_mode_of_participation'];
        $event->payment = $input['event_payment'];
        $event->incentive_type = $input['event_incentive_type'];
        if($input['event_incentive_type'] == 1){
            $event->incentive_price = $input['event_incentive_price'];
        }        
        $event->event_activity = serialize($input['event_activity']);

        if($event->save()){

            $event_id = $event->id;
            if($req->has('event_invites')){   
                $invites = $input['event_invites'];
                foreach($invites as $invite){
                    $event_invite = New EventInvite();
                    $event_invite->event_id = $event_id;
                    $event_invite->invited_user_id = $invite;
                    $event_invite->accepted = '0';
                    $event_invite->save();
                }
            }

            return response()->json([
                'status' => 200,
                'message' => 'Event added successfully.'
            ]);

        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Event failed to add.'
            ]);
        }
    }

    public function edit($id){
        $event = Event::with(['target'])->find($id);
        if($event){
            if($event->event_activity){
                $event->event_activity = unserialize($event->event_activity);
            }
            if(count($event->toArray())){
                return response()->json([
                    'status' => 200,
                    'message' => 'Successfully got data.',
                    'data' => $event
                ]);
            }
        }else{
            return response()->json([
                'status' => 400,
                'message' => 'No data found for this target_id.',
            ]);
        }
    }

    public function update(Request $req, $id){

        $event = Event::with(['target'])->find($id);
        $input = $req->all();

        $rules = [
            'event_name' => 'required',
            'event_category' => 'required',
            'event_type' => 'required', // 0 => public, 1 => private
            'event_location' => 'required',
            'event_date' => 'required',
            'event_start_time' => 'required',
            'event_meridiem' => 'required',
            'event_description' => 'required',
            'event_cover_photo' => 'mimes:jpeg,jpg,png,gif',
            // 'target_id' => 'required',
            'event_validator_charging_mode' => 'required',
            'event_validator_id' => 'required',
            'event_vendor_id' => 'required',
            'event_frequency' => 'required', // 0 => One Time, 1 => Recurring, 2 => Ad-Hoc
            'event_mode_of_participation' => 'required',
            'event_payment' => 'required', // 0 => Free
            'event_incentive_type' => 'required', // 1 => Incentivized, 0 => Unincentivized
            // 'event_activity' => 'required'
        ];

        $validate = Validator::make($input, $rules);
        if($validate->fails()){
            return response()->json([
                'status' => "failed",
                'message' => $validate->errors()
            ]);
        }

        //Event Frequency conditions
        if($input['event_frequency'] == 0){ // One Time
            $rules = array_merge($rules, [
                                            'event_frequency_start_date' => 'required',
                                            'event_frequency_end_date' => 'required'
                                        ]
                                );
        }

        if($input['event_frequency'] == 1){ // Recurring condtion
            $rules = array_merge($rules, [
                                            'event_frequency_start_date' => 'required',
                                            'event_frequency_end_date' => 'required',
                                            'event_recurring_time' => 'required',
                                            'event_continuous_time' => 'required'
                                        ]
                                );
        }

        if($input['event_frequency'] == 2){ // Ad-Hoc condtion
            $rules = array_merge($rules, [
                                            'event_frequency_end_date' => 'required',
                                            'event_continuous_time' => 'required'
                                        ]
                                );
        }

        //Event incentive price
        if($input['event_incentive_type'] == 1){
            $rules = array_merge($rules, [
                                        'event_incentive_price' => 'required',
                                    ]
                            );
        }

        $validate = Validator::make($input, $rules);
        if($validate->fails()){
            return response()->json([
                'status' => "failed",
                'message' => $validate->errors()
            ]);
        }

        $upload_file_name = '';
        // dd(public_path("event_cover_images\\").$event->cover_photo);
        if($req->has('event_cover_photo')){

            if($event->cover_photo){
                //remove old one
                unlink(public_path('event_cover_images\\').$event->cover_photo);
            }
            $original_name = $req->event_cover_photo->getClientOriginalName();
            $upload_file_name = time()."_".$original_name;
            $req->event_cover_photo->move(public_path('/event_cover_images'), $upload_file_name);
        }

        $event->name = $input['event_name'];
        $event->category = $input['event_category'];
        $event->type = $input['event_type'];
        $event->location = $input['event_location'];
        $event->event_date = $input['event_date'];
        $event->start_time = $input['event_start_time'];
        $event->meridiem = $input['event_meridiem'];
        $event->description = $input['event_description'];
        $event->cover_photo = $upload_file_name;
        $event->validator_charging_mode = $input['event_validator_charging_mode'];
        $event->validator_id = $input['event_validator_id'];
        if($req->has('event_validator_optional_id')){ // Optional
            $event->validator_optional_id = $input['event_validator_optional_id'];
        }
        $event->vendor_id =  $input['event_vendor_id'];
        $event->frequency =  $input['event_frequency'];

        if($input['event_frequency'] == 0){ // One Time
            $event->frequency_start_date = $input['event_frequency_start_date'];
            $event->frequency_end_date = $input['event_frequency_end_date'];

            $event->recurring_time = null;
            $event->continuous_time = null;
        }

        if($input['event_frequency'] == 1){ // Recurring condtion
                $event->frequency_start_date = $input['event_frequency_start_date'];
                $event->frequency_end_date = $input['event_frequency_end_date'];
                $event->recurring_time = $input['event_recurring_time'];
                $event->continuous_time = $input['event_continuous_time'];
        }

        if($input['event_frequency'] == 2){ // Ad-Hoc condtion
            $event->frequency_end_date = $input['event_frequency_end_date'];
            $event->continuous_time = $input['event_continuous_time']; 

            $event->frequency_start_date = null;
            $event->recurring_time = null;
        }

        //Event incentive price
        if($input['event_incentive_type'] == 1){
            $event->incentive_price = $input['event_incentive_price']; 
        }
 
        $event->event_activity = serialize($input['event_activity']);

        if($event->save()){
            if($req->has('event_invites')){
                $event_id = $event->id;

                $event_invite = $input['event_invites'];
                $old_event_invite = EventInvite::where('event_id', $id)->pluck('invited_user_id')->toArray();
                $new_invites = array_diff($event_invite, $old_event_invite);

                foreach($new_invites as $new_invite){
                    $event_invite = New EventInvite;
                    $event_invite->event_id = $event_id;
                    $event_invite->invited_user_id = $new_invite;
                    $event_invite->save();
                }
            }
            return response()->json([
                'status' => 200,
                'message' => 'Event updated successfully.'
            ]);

        }else{
            return response()->json([
                'status' => 400,
                'message' => 'Failed to update event.'
            ]);
        }
    }

    public function destroy($id){

        $event = Event::find($id);

        if($event){
            if($event->cover_photo){
                $delete_old_photo = public_path('/event_cover_images/');
                unlink($delete_old_photo.$event->cover_photo);
            }
            if($event->delete()){
                return response()->json([
                    'status' => 200,
                    'message' => 'Event Deleted Successfully.',
                ]);
            }else{
                return response()->json([
                    'status' => 400,
                    'message' => 'Failed to delete event.',
                ]);
            }
        }else{
            return response()->json([
                'status' => 200,
                'message' => 'Please check event id.',
            ]);
        }
    }
}
