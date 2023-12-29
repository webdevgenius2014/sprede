<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'org_email',
        'org_contact',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
