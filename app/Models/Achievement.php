<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Achievement extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    protected $fillable = [
        "name",
        "field",
        "threshold",
        "description"
    ];


    public function users(){
        return $this->belongsToMany(User::class);
    }
}
