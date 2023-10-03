<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'title',
        'type',
        'interest_id',
        'sub_interest_id',
        'target_units',
        'from',
        'to',
        'description',
        'photo',
        'incentive',
        'incentive_prize'
    ];
    
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function interest(){
        return $this->belongsTo(Interest::class);
    }
    public function sub_interest(){
        return $this->belongsTo(SubInterest::class);
    }
    
    public function targetInvites(){
        return $this->hasMany(TargetInvite::class);
    }
}
