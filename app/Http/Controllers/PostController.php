<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;

class PostController extends Controller
{
    public function create(Request $request){
        $request->validate([
            "post" => "required|min:10|max:65534",
            "img" => "nullable", //validate the image somehow
            "user_id" => "required|numeric",
        ]);
        $post = new Post();
        $post->post = $request->post;
        $user_id = User::findOrFail($request->user_id)->id;
        $post->user_id = $user_id;
        $post->likes = 0;

        $latestPost = Post::orderBy("id", "desc")->first()->id;
        $latestPost++;

        //Process image
        // $image = base64_decode($request->image);
        

        // $success = file_put_contents($imageName, $image);
        // if(!$success){
        //     return response()->json([
        //         "message" => "Failed to upload file"
        //     ]);
        // }

        $post->image = $post->processImage($request->image, $latestPost);
        $post->save();

        return response()->json([
            "post" => [
                "id" => $post->id,
                "message" => "Post created successfully"
            ]
        ]);
    }

    public function update(Request $request, $id){
        $request->validate([
            "post" => "nullable|min:10|max:65534",
            //"img" => "nullable", //same shit as before
            "likes" => "nullable|numeric"
        ]);

        $post = Post::findOrFail($id);
        if(isset($request->image)){
            Storage::disk("local")->delete($post->image);
            $imageName = $post->processImage($request->image, $post->id);
            $post->image = $imageName;
        }

        $post->update();        //somehow update it
        return response()->json([
            "post" => [
                "id"=> $post->id,
                "message"=> "Post updated successfully"
            ]
        ]);
    }

    public function delete($id){
        $post = Post::findOrFail($id);
        $post->delete();
        return response()->json([
            "message" => "Post deleted successfully"
        ]);
    }

    
}
