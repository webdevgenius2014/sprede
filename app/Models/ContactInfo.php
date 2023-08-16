<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'permanent_add_city',
        'permanent_add_country',
        'same_as_permanent_add',
        'current_add_city',
        'current_add_country'
    ];
}
