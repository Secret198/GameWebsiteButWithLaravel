<?php

namespace App\Http\Controllers;

use Laravel\Sanctum\PersonalAccessToken;
use Storage;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class PostController extends Controller
{

    /**
     * @api {post} /post Post registration
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiBody {String{min:10 - max:65534}} post Text of the new post
     * @apiBody {String{max: 500KB}} image Base64 encoded image for the new post
     * @apiError ThePostFieldMustBeAtLeast10Characters <code>post</code> must be at least 10 characters.
     * @apiError ThePostFieldMustNotBeGreaterThan65534Characters. <code>post</code> must be below 65534 characters.
     * @apiError TheImageMustBeOfTypeJpeg,jpg,png <code>image</code> must be of type jpeg, jpg, png
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Content
     *       {
     *           "message": "The post field must be at least 10 characters.",
     *           "errors": {
     *               "post": [
     *                   "The post field must be at least 10 characters."
     *               ]
     *           }
     *       }
     * @apiPermission none
     * @apiSuccess {String} message Information about the registration.
     * @apiSuccess {Object} user Data of the newly registered user.
     * @apiSuccess {Number} user.id   Users <code>id</code>.
     * @apiSuccess {String} user.token User's access <code>token</code>.
     * @apiSuccess {Number} user.privilege User's <code>privilege</code> level.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *           "message": "User registered successfully",
     *           "user": {
     *               "id": 11,
     *               "token": "5|wt46dJE69ABNtf7luWYGaIk8WE5P2JYoCILBzJcqadf29d0d",
     *               "privilege": 1
     *           }
     *    }
     *    @apiVersion 0.1.0
     */

    public function create(Request $request){
        $request->validate([
            "post" => "required|min:10|max:65534",
            "image" => "nullable|is_image:jpeg,jpg,png|base64_image_size:500", 
            // "user_id" => "required|numeric",
        ]);

        $accessTokenUser = PersonalAccessToken::findToken($request->bearerToken())->tokenable;

        $post = new Post();
        $post->post = $request->post;
        // $user_id = User::findOrFail($request->user_id)->id;
        $post->user_id = $accessTokenUser->id;
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
            "message" => "Post created successfully",
            "post" => [
                "id" => $post->id,
            ]
        ]);
    }

    public function update(Request $request, $id){
        $request->validate([
            "post" => "nullable|min:10|max:65534",
            "image" => "nullable|is_image:jpeg,jpg,png|base64_image_size:500", 
            "likes" => "nullable|numeric"
        ]);

        $accessTokenUser = PersonalAccessToken::findToken($request->bearerToken())->tokenable;
        $post = Post::findOrFail($id);
        if($accessTokenUser->id != $post->user_id && $accessTokenUser->privilege != 10){
            return response()->json([
                "message" => "Action not allowed"
            ], 401);
        }

        if(isset($request->image)){
            Storage::disk("local")->delete($post->image);
            $imageName = $post->processImage($request->image, $post->id);
            $post->image = $imageName;
        }
        $post->post = isset($request->post) ? $request->post : $post->post;
        $post->likes = isset($request->likes) ? $request->likes : $post->likes;

        $post->save();
        return response()->json([
            "message"=> "Post updated successfully",
            "post" => [
                "id"=> $post->id,
            ]
        ]);
    }

    public function delete(Request $request, $id)
    {
        $accessTokenUser = PersonalAccessToken::findToken($request->bearerToken())->tokenable;
        $post = Post::findOrFail($id);
        if($accessTokenUser->id != $post->user_id && $accessTokenUser->privilege != 10){ 
            return response()->json([
                "message" => "Action not allowed"
            ], 401);
        }
        $post->delete();
        return response()->json([
            "message" => "Post deleted successfully"
        ]);
    }

    public function restore($id){
        $post = Post::withTrashed()->findOrFail($id);
        $post->restore();
        return response()->json([
            "message" => "Post restored successfully",
            "post" => [
                "id" => $post->id
            ]
        ]);
    }

    public function getPostData(Request $request, $id){
        $accessToken = PersonalAccessToken::findToken($request->bearerToken())->abilities;
        if(in_array("view-all", $accessToken) || in_array("*", $accessToken)){
            $post = Post::withTrashed()->findOrFail($id);
            $image = $post->getImage();
            
            $data = [
                "id" => $post->id,
                "post" => $post->post,
                "image" => $image,
                "likes" => $post->likes,
                "deleted_at" => $post->deleted_at,
                "created_at" => $post->created_at,
                "modified_at" => $post->updated_at
            ];
        }
        else{
            $post = Post::findOrFail($id);
            $image = $post->getImage();
            $data = [
                "id" => $post->id,
                "post" => $post->post,
                "image" => $image,
                "likes" => $post->likes,
                "created_at" => $post->created_at,
                "modified_at" => $post->updated_at
            ];
        }


        return response()->json([
            "post" => $data,
        ]);
    }

    public function getAllPosts(Request $request, $sortByStr, $sortDirStr){
        $sortBy = request()->query("sort_by", $sortByStr);
        $sortDir = request()->query("sort_dir", $sortDirStr);
        $accessToken = PersonalAccessToken::findToken($request->bearerToken())->abilities;
        if(in_array("view-all", $accessToken) || in_array("*", $accessToken)){
            $posts = Post::withTrashed()->select([
                "id",
                "post",
                "created_at",
                "updated_at",
                "deleted_at"
            ])->orderBy($sortBy, $sortDir)->paginate(30);
        }
        else{
            $posts = Post::select([
                "id",
                "post",
                "created_at",
                "updated_at",
            ])->orderBy($sortBy, $sortDir)->paginate(30);
        }

        return response()->json([
            "posts" => $posts
        ]);
           
    }

    public function searchPosts(Request $request, $sortByStr, $sortDirStr, $search){
        $sortBy = request()->query("sort_by", $sortByStr);
        $sortDir = request()->query("sort_dir", $sortDirStr);
        $accessToken = PersonalAccessToken::findToken($request->bearerToken())->abilities;
        if(in_array("view-all", $accessToken) || in_array("*", $accessToken)){
            $posts = Post::withTrashed()->select([
                "id",
                "post",
                "created_at",
                "updated_at",
                "deleted_at"
            ])->where("post", "LIKE", "%".$search."%")->orderBy($sortBy, $sortDir)->paginate(30);
        }
        else{
            $posts = Post::select([
                "id",
                "post",
                "created_at",
                "updated_at"
            ])->where("post", "LIKE", "%".$search."%")->orderBy($sortBy, $sortDir)->paginate(30);
        }

        return response()->json([
            "posts" => $posts
        ]);
           
    }
}
