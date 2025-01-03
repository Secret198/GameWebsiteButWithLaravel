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
     * @api {post} /post Post creation
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiBody {String{min:10 - max:65534}} post Text of the new post
     * @apiBody {String{max: 500KB}} [image] Base64 encoded image for the new post
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError ThePostFieldMustBeAtLeast10Characters <code>post</code> must be at least 10 characters.
     * @apiError ThePostFieldMustNotBeGreaterThan65534Characters. <code>post</code> must be below 65534 characters.
     * @apiError ThePostFieldIsRequired The <code>post</code> field is required
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
     * @apiPermission normal user
     * @apiSuccess {String} message Information about the post creation.
     * @apiSuccess {Object} post Data of the newly created post.
     * @apiSuccess {Number} post.id <code>id</code> of the new post.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Post created successfully",
     *           "post": {
     *               "id": 11
     *           }
     *       }
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

        if($request->image){
            $post->image = $post->processImage($request->image, $latestPost);
        }
        $post->save();

        return response()->json([
            "message" => "Post created successfully",
            "post" => [
                "id" => $post->id,
            ]
        ]);
    }

    /**
     * @api {patch} /post/:id Post update
     * @apiDescription Updating posts, normal users can only update their own posts, while admins can update everyone's
     * @apiParam {Number} id Id of the post to be updated
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiBody {String{min:10 - max:65534}} [post] Text of the new post
     * @apiBody {String{max: 500KB}} [image] Base64 encoded image for the new post
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError ThePostFieldMustBeAtLeast10Characters <code>post</code> must be at least 10 characters.
     * @apiError ThePostFieldMustNotBeGreaterThan65534Characters. <code>post</code> must be below 65534 characters.
     * @apiError TheImageMustBeOfTypeJpeg,jpg,png <code>image</code> must be of type jpeg, jpg, png
     * @apiError NoQueryResultsForModel:id Post with <code>id</code> could not be found
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
     * @apiPermission normal user
     * @apiSuccess {String} message Information about the post update.
     * @apiSuccess {Object} post Data of the updated post.
     * @apiSuccess {Number} post.id <code>id</code> of the updated post.
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Post updated successfully",
     *           "post": {
     *               "id": 3
     *           }
     *       }
     *    @apiVersion 0.3.0
     */

    public function update(Request $request, $id){
        $request->validate([
            "post" => "nullable|min:10|max:65534",
            "image" => "nullable|is_image:jpeg,jpg,png|base64_image_size:500", 
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

        $post->save();

        return response()->json([
            "message"=> "Post updated successfully",
            "post" => [
                "id"=> $post->id,
            ]
        ]);
    }

    /**
     * @api {patch} /post/like/:id Like or unlike post
     * @apiDescription Liking a post
     * @apiParam {Number} id Id of the post to be liked
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiBody {Boolean="true", "false"} likes Wether to like or unlike a post
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError TheLikesFieldMustBeTrueOrFalse <code>likes</code> field must be a boolean value
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Content
     *      {
     *          "message": "Unauthenticated."
     *      }
     * @apiPermission normal user
     * @apiSuccess {String} message Information about the liking procedure.
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *      {
     *          "message": "Post liked successfully"
     *      }
     *    @apiVersion 0.3.0
     */

    public function likePost(Request $request, $id){
        $request->validate([
            "likes" => "required|boolean"
        ]);

        $accessTokenUser = PersonalAccessToken::findToken($request->bearerToken())->tokenable;
        $post = Post::findOrFail($id);

        $likedPosts = $accessTokenUser->likedPosts->toArray();
        $likedPostsIds = [];

        foreach($likedPosts as $onePost){
            array_push($likedPostsIds, $onePost["id"]);
        }

        $responseMessage = "";

        if($request->likes && !in_array($post->id, $likedPostsIds)){
            $post->likes += 1;
            $accessTokenUser->likedPosts()->attach($post->id);
            
            $responseMessage = "Post liked successfully";
        }
        else if($request->likes == false && in_array($post->id, $likedPostsIds)){
            $post->likes -= 1;
            $accessTokenUser->likedPosts()->detach($post->id);

            $responseMessage = "Unliked successfully";
        }
        else{
            $responseMessage = "Post is already liked or already unliked";
        }
        $post->save();

        return response()->json([
            "message" => $responseMessage
        ]);
    }

    /**
     * @api {delete} /post/:id Delete post
     * @apiDescription Deleting posts, normal users can delete their own posts, while admis can delete everyone's.
     * @apiParam {Number} id Id of the post to be deleted
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError NoQueryEesultsForModel:id Post with <code>id</code> could not be found.
     * @apiError ActionNotAllowed Normal users are not allowed to delete others user's posts
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *       {
     *           "message": "Action not allowed",
     *       }
     * @apiPermission normal user
     * @apiSuccess {String} message Information about the post deletion.
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Post deleted successfully",
     *       }
     *    @apiVersion 0.1.0
     */

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

    /**
     * @api {delete} /post/restore/:id Restore post
     * @apiParam {Number} id Id of the post to be restored
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError NoQueryEesultsForModel:id Post with <code>id</code> could not be found.
     * @apiError ActionNotAllowed Normal users are not allowed to restore posts
     * @apiError InvalidAbilityProvided The user is not authorized to restore posts
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unauthorized
     *       {
     *           "message": "Action not allowed",
     *       }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the post restoration.
     * @apiSuccess {Object} post Data of the restored post.
     * @apiSuccess {Number} post.id <code>id</code> of the restored post.
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Post restored successfully",
     *           "post": {
     *               "id": 3
     *           }
     *       }
     *    @apiVersion 0.1.0
     */

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

    /**
     * @api {get} /post/:id Get post data
     * @apiDescription Getting post data, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {Number} id Id of post to be queried
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiError NoQueryResultsForModel:id Post with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} post Data of the requested post.
     * @apiSuccess (Success-Normal user) {Number} post.id   Post's <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} post.post Post's <code>text</code>.
     * @apiSuccess (Success-Normal user) {String} post.image Post's <code>image</code> encoded with base64 encoding.
     * @apiSuccess (Success-Normal user) {Number} post.likes Post's number of <code>likes</code>.
     * @apiSuccess (Success-Normal user) {Date} post.created_at When the <code>post</code> was created.
     * @apiSuccess (Success-Normal user) {Date} post.modified_at When the <code>post</code> was last modified.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} post Data of the requested post
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} post.deleted_at When the post was deleted
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "post": {
     *               "id": 11,
     *               "post": "Yeah body, light weight",
     *               "image": "data:image/jpg;base64;<base64-encoded-image>",
     *               "likes": 0,
     *               "created_at": "2024-11-26T17:12:34.000000Z",
     *               "modified_at": "2024-11-26T17:12:34.000000Z"
     *           }
     *       }
     *    @apiVersion 0.1.0
     */

    public function getPostData(Request $request, $id){
        $accessToken = PersonalAccessToken::findToken($request->bearerToken())->abilities;
        if(in_array("view-all", $accessToken) || in_array("*", $accessToken)){
            $post = Post::withTrashed()->findOrFail($id);
            if($post->image){
                $image = $post->getImage();
            }
            else{
                $image = "";
            }
            
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

    /**
     * @api {get} /post/:sort_by/:sort_dir Get all posts
     * @apiDescription Getting all posts, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {String} sort_by Field the result is sorted by
     * @apiParam {String="asc","desc"} sort_dir Sort direction
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} posts Data of the posts.
     * @apiSuccess (Success-Normal user) {Number} post.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} post.data Array of all the post data.
     * @apiSuccess (Success-Normal user) {id} post.data.id Post's <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} post.data.post Post's text.
     * @apiSuccess (Success-Normal user) {Number} post.data.likes Post's number of <code>likes</code>.
     * @apiSuccess (Success-Normal user) {Date} post.data.created_at When the <code>post</code> was created.
     * @apiSuccess (Success-Normal user) {Date} post.data.modified_at When the <code>post</code> was last modified.
     * @apiSuccess (Success-Normal user) {Array} likedPosts Ids of the user's liked posts.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} post Data of the requested post
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} post.deleted_at When the post was deleted
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *      {
     *          "posts": {
     *              "current_page": 1,
     *              "data": [
     *                  {
     *                      "id": 6,
     *                      "post": "Gryphon. '--you advance twice--' 'Each with a.",
     *                      "likes": 884,
     *                      "created_at": "2024-11-23T13:19:26.000000Z",
     *                      "updated_at": "2024-11-23T13:19:26.000000Z"
     *                  },
     *                  {
     *                      "id": 7,
     *                      "post": "I should be like then?' And she went round the.",
     *                      "likes": 345,
     *                      "created_at": "2024-11-23T13:19:26.000000Z",
     *                      "updated_at": "2024-11-23T13:19:26.000000Z"
     *                  },
     *                  ...
     *                  {
     *                      "id": 35,
     *                      "post": "And I declare it's too bad, that it was indeed.",
     *                      "likes": 1260,
     *                      "created_at": "2024-12-05T18:01:27.000000Z",
     *                      "updated_at": "2024-12-05T18:01:27.000000Z"
     *                  }
     *              ],
     *              "first_page_url": "http://localhost:8000/api/post/id/asc?page=1",
     *              "from": 1,
     *              "last_page": 4,
     *              "last_page_url": "http://localhost:8000/api/post/id/asc?page=4",
     *              "links": [
     *                  {
     *                      "url": null,
     *                      "label": "&laquo; Previous",
     *                      "active": false
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/post/id/asc?page=1",
     *                      "label": "1",
     *                      "active": true
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/post/id/asc?page=2",
     *                      "label": "2",
     *                      "active": false
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/post/id/asc?page=3",
     *                      "label": "3",
     *                      "active": false
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/post/id/asc?page=4",
     *                      "label": "4",
     *                      "active": false
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/post/id/asc?page=2",
     *                      "label": "Next &raquo;",
     *                      "active": false
     *                  }
     *              ],
     *              "next_page_url": "http://localhost:8000/api/post/id/asc?page=2",
     *              "path": "http://localhost:8000/api/post/id/asc",
     *              "per_page": 30,
     *              "prev_page_url": null,
     *              "to": 30,
     *              "total": 108
     *          },
     *          "likedPosts": [
     *              100,
     *              98
     *          ]
     *      }
     *    @apiVersion 0.3.0
     */

    public function getAllPosts(Request $request, $sortByStr, $sortDirStr){
        $sortBy = request()->query("sort_by", $sortByStr);
        $sortDir = request()->query("sort_dir", $sortDirStr);
        $accessToken = PersonalAccessToken::findToken($request->bearerToken())->abilities;
        $accessTokenUser = PersonalAccessToken::findToken($request->bearerToken())->tokenable;
        if(in_array("view-all", $accessToken) || in_array("*", $accessToken)){
            $posts = Post::withTrashed()->select([
                "id",
                "post",
                "likes",
                "created_at",
                "updated_at",
                "deleted_at"
            ])->orderBy($sortBy, $sortDir)->paginate(30);
        }
        else{
            $posts = Post::select([
                "id",
                "post",
                "likes",
                "created_at",
                "updated_at",
            ])->orderBy($sortBy, $sortDir)->paginate(30);
        }

        $likedPosts = $accessTokenUser->likedPosts;
        $likedPostIds = [];
        foreach ($likedPosts as $likedPost){
            array_push($likedPostIds,$likedPost->id);
        }

        return response()->json([
            "posts" => $posts,
            "likedPosts" => $likedPostIds
        ]);
           
    }

    /**
     * @api {get} /post/search/:sort_by/:sort_dir/:search_for Search for posts
     * @apiDescription Search for posts, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {String} sort_by Field the result is sorted by
     * @apiParam {String="asc","desc"} sort_dir Sort direction
     * @apiParam {String} search_for Keyword to search for
     * @apiGroup Post
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} posts Data of the posts.
     * @apiSuccess (Success-Normal user) {Number} post.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} post.data Array of all the post data.
     * @apiSuccess (Success-Normal user) {id} post.data.id Post's <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} post.data.post Post's text.
     * @apiSuccess (Success-Normal user) {Number} post.data.likes Number of <code>likes</code> on the post.
     * @apiSuccess (Success-Normal user) {Date} post.data.created_at When the <code>post</code> was created.
     * @apiSuccess (Success-Normal user) {Date} post.data.modified_at When the <code>post</code> was last modified.
     * @apiSuccess (Success-Normal user) {Array} likedPosts Ids of the user's liked posts.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} post Data of the requested post
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} post.deleted_at When the post was deleted
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *      {
     *          "posts": {
     *              "current_page": 1,
     *              "data": [
     *                  {
     *                      "id": 90,
     *                      "post": "SOMEBODY ought to tell him. 'A nice muddle their.",
     *                      "likes": 425,
     *                      "created_at": "2024-12-05T18:01:29.000000Z",
     *                      "updated_at": "2024-12-05T18:01:29.000000Z"
     *                  },
     *                  {
     *                      "id": 91,
     *                      "post": "What happened to me! I'LL soon make you grow.",
     *                      "likes": 772,
     *                      "created_at": "2024-12-05T18:01:29.000000Z",
     *                      "updated_at": "2024-12-05T18:01:29.000000Z"
     *                  },
     *                  ...
     *                  {
     *                      "id": 48,
     *                      "post": "Alice, always ready to agree to everything that.",
     *                      "likes": 2918,
     *                      "created_at": "2024-12-05T18:01:27.000000Z",
     *                      "updated_at": "2024-12-05T18:01:27.000000Z"
     *                  }
     *              ],
     *              "first_page_url": "http://localhost:8000/api/post/search/created_at/desc/to?page=1",
     *              "from": 1,
     *              "last_page": 2,
     *              "last_page_url": "http://localhost:8000/api/post/search/created_at/desc/to?page=2",
     *              "links": [
     *                  {
     *                      "url": null,
     *                      "label": "&laquo; Previous",
     *                      "active": false
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/post/search/created_at/desc/to?page=1",
     *                      "label": "1",
     *                      "active": true
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/post/search/created_at/desc/to?page=2",
     *                      "label": "2",
     *                      "active": false
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/post/search/created_at/desc/to?page=2",
     *                      "label": "Next &raquo;",
     *                      "active": false
     *                  }
     *              ],
     *              "next_page_url": "http://localhost:8000/api/post/search/created_at/desc/to?page=2",
     *              "path": "http://localhost:8000/api/post/search/created_at/desc/to",
     *              "per_page": 30,
     *              "prev_page_url": null,
     *              "to": 30,
     *              "total": 35
     *          },
     *          "likedPosts": [
     *              100,
     *              98
     *          ]
     *      }
     *    @apiVersion 0.3.0
     */

    public function searchPosts(Request $request, $sortByStr, $sortDirStr, $search){
        $sortBy = request()->query("sort_by", $sortByStr);
        $sortDir = request()->query("sort_dir", $sortDirStr);
        $accessToken = PersonalAccessToken::findToken($request->bearerToken())->abilities;
        $accessTokenUser = PersonalAccessToken::findToken($request->bearerToken())->tokenable;
        if(in_array("view-all", $accessToken) || in_array("*", $accessToken)){
            $posts = Post::withTrashed()->select([
                "id",
                "post",
                "likes",
                "created_at",
                "updated_at",
                "deleted_at"
            ])->where("post", "LIKE", "%".$search."%")->orderBy($sortBy, $sortDir)->paginate(30);
        }
        else{
            $posts = Post::select([
                "id",
                "post",
                "likes",
                "created_at",
                "updated_at"
            ])->where("post", "LIKE", "%".$search."%")->orderBy($sortBy, $sortDir)->paginate(30);
        }

        $likedPosts = $accessTokenUser->likedPosts;
        $likedPostIds = [];
        foreach ($likedPosts as $likedPost){
            array_push($likedPostIds,$likedPost->id);
        }

        return response()->json([
            "posts" => $posts,
            "likedPosts" => $likedPostIds
        ]);
           
    }
}
