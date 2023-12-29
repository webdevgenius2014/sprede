<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'organization_id',
        'name',
        'category',
        'type',
        'location',
        'event_date',
        'start_time',
        'meridiem',
        'description',
        'cover_photo',
        'target_id',
        'validator_charging_mode',
        'validator_id',
        'validator_optional_id',
        'vendor_id',
        'frequency',
        'frequency_start_date',
        'frequency_end_date',
        'recurring_time',
        'continuous_time',
        'mode_of_participation',
        'payment',
        'incentive_type',
        'incentive_price',
        'event_activity'
    ];

    public function target(){
        return $this->belongsTo(Target::class);
    }
}
