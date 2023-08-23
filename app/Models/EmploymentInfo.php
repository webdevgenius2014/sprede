<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmploymentInfo extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'is_defence',
        'on_privacy',
        'organization',
        'designation',
        'from',
        'to',
        'current_work_here',
        'org_city',
        'org_country'
    ];
}
