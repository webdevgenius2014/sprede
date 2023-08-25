<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    public function subInterest(){
        return $this->hasMany(SubInterest::class);
    }

    public function default_sub_cat() {
        return $this->subInterest()->where('default_sub_cat', '=', 1);
    }

    public function default_subInterest() {
        return $this->hasMany(SubInterest::class)->where('default_sub_cat', '=', 1);
    }

}
