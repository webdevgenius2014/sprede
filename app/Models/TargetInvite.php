<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetInvite extends Model
{
    use HasFactory;
    protected $fillable = [
        'target_id',
        'invited_user_id ',
        'accepetd'
    ];

}
