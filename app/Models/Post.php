<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    public function user(){
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        "post",
        "image",
        "likes"
    ];

    public function processImage($image, $id){
        preg_match("/\/(.*?);/", $image, $extension);
        $img = file_get_contents($image);
        $imageName = "/posts/".$id.".".$extension[1];
        Storage::disk("local")->put($imageName, $img);
        return $imageName;
    }
}
