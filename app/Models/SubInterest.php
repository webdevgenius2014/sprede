<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\Interest;

class SubInterest extends Model
{
    use HasFactory;

    protected $fillable = [
        'interest_id',
        'user_id',
        'default_sub_cat',
        'name'
    ];

    public function interest(){
        return $this->belongsTo(Interest::class, 'interest_id');
    }

}
