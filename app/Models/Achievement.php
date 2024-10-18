<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achievement extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    public function user(){
        return $this->belongsToMany(User::class);
    }
}
