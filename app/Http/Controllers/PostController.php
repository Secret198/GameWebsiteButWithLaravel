<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function create(Request $request){
        $request->validate([
            "post" => "required|min:3|max:65535",
            "img" => "", //validate the image somehow
            "user_id" => "required|numeric",
        ]);
        $post = new Post();
        $post->post = $request->post;
        $user_id = User::findOrFail($request->user_id);
        $post->user_id = $user_id;

        $latestPost = Post::orderBy("id", "desc")->first()->id;
        $latesPost++;
        //Process image
        $image = base64_decode($request->image);
        $imageName = "/storage/app/private/".$latesPost;
        $success = file_put_contents($imageName);
    }
}
