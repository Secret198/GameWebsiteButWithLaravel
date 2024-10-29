<?php

namespace App\Http\Controllers;

use Hash;
use App\Models\Post;
use App\Models\User;
use App\Models\Achievement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

class UserController extends Controller
{
    public function login(Request $request){

        $request->validate([
            "email" => "required|email",
            "password"=> "required",
        ]);

        $email = $request->input("email");
        $password = $request->input("password");

        $user = User::where("email", $email)->first();
        if(!$user || !Hash::check($password, $password ? $user->password : '')){//
            return response()->json([
                'message' => "Invalid email or password"       
            ], 401);
        }

        $user->regenerateToken();

        return response()->json([
            "message" => "Login successful",
            'user' => [
                "id" =>$user->id,
                "name" => $user->name,
                "deaths" => $user->deaths,
                "token" => $user->token,
                "privilege" => $user->privilege
            ],
        ]);
    }

    public function register(Request $request){
        $request->validate([
            "email" => "required|email",
            "name" => "required|min:3",
            "password" => "required",
        ]);

        $name = $request->input("name");
        $email = $request->input("email");
        $password = $request->input("password");

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->deaths = 0;
        $user->kills = 0;
        $user->points = 0;
        $user->boss1lvl = 0;
        $user->boss2lvl = 0;
        $user->boss3lvl = 0;
        $user->privilege = 1;
        $user->save();

        $user->token = $user->createToken("access", $user->baseAbilities)->plainTextToken;

        return response()->json([
            "message" => "User registered successfully",
            "user" => [
                "id" => $user->id,
                "token" => $user->token,
                "privilege" => $user->privilege
            ]
        ]);
        
    }

    public function makeUserAdmin($id){
        $user = User::findOrFail($id);

        $user->tokens()->delete();
        $user->createToken("access", ["*"])->plainTextToken;     //Ha akarjuk akkor egyesével beírogatni
        $user->privilege = 10;
        $user->save();

        return response()->json([
            "message" => "Admin created successfully",
            "user"=> [
                "id"=> $user->id,
                "privilege" => $user->privilege,
            ]
            ]);
    }

    // public function updateEveryone(Request $request, $id){
    //     $request->validate([
    //         "name" => "nullable|min:3",
    //         "email" => "nullable|email"
    //     ]);
    //     $user = User::findOrFail($id);
        
    //     if($request->has("name")){
    //         $user->update(["name" => $request->input("name")]);
    //     }
    //     if($request->has("email")){
    //         $user->update(["email"=> $request->input("email")]);
    //     }

    //     return response()->json([
    //         "id" => $user->id,
    //         "name" => $user->name,
    //         "email" => $user->email
    //     ]);
    // }

    public function update(Request $request, $id){
        $request->validate([
            "name" => "nullable|min:3",
            "email" => "nullable|email",
            "deaths" => "nullable|numeric",
            "kills" => "nullable|numeric",
            "points" => "nullable|numeric",
            "boss1lvl" => "nullable|numeric",
            "boss2lvl" => "nullable|numeric",
            "boss3lvl" => "nullable|numeric",
        ]);

        $accessTokenUser = PersonalAccessToken::findToken($request->bearerToken())->tokenable;
        $user = User::findOrFail($id);
        if($accessTokenUser->id != $user->id && $accessTokenUser->privilege != 10){ 
            return response()->json([
                "message" => "Action not allowed"
            ], 403);
        }

        $user->update($request->all());
        $user->checkForAchievements();

        return response()->json([
            "message" => "User updated successfully",
            "user" => [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "deaths" => $user->deaths,
                "kills" => $user->kills,
                "points" => $user->points,
                "boss1lvl" => $user->boss1lvl,
                "boss2lvl" => $user->boss2lvl,
                "boss3lvl" => $user->boss3lvl,
            ],
                        
        ]);
    }

    public function delete($id){
        $user = User::findOrFail($id);        
        $user->delete();
        return response()->json([
            "message" => "User deleted successfully"
        ]);
    }

    public function restore($id){
        $user = User::withTrashed()->findOrFail($id);
        if(!$user){
            return response()->json([
                "message" => "Unable to find user"
            ]);
        }
        $user->restore();
        $user->regenerateToken();
        return response()->json([
            "message" => "User restored successfully",
            "user" => [
                "id" => $user->id,
                "privilege" => $user->privilege
            ]
        ]);
    }

    public function getUserData(Request $request, $id){
        $accessToken = PersonalAccessToken::findToken($request->bearerToken())->abilities;
        if(in_array("view-all", $accessToken) || in_array("*", $accessToken)){
            $user = User::withTrashed()->findOrFail($id);
            $data = [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "deaths" => $user->deaths,
                "kills" => $user->kills,
                "points" => $user->points,
                "boss1lvl" => $user->boss1lvl,
                "boss2lvl" => $user->boss2lvl,
                "boss3lvl" => $user->boss3lvl,
                "deleted_at" => $user->deleted_at,
                "created_at" => $user->created_at,
                "modified_at" => $user->updated_at
            ];
        }
        else{
            $user = User::findOrFail($id);
            $data = [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "deaths" => $user->deaths,
                "kills" => $user->kills,
                "points" => $user->points,
                "boss1lvl" => $user->boss1lvl,
                "boss2lvl" => $user->boss2lvl,
                "boss3lvl" => $user->boss3lvl,
                "created_at" => $user->created_at
            ];
        }

        $achievements = $user->achievements;

        return response()->json([
            "user" => $data,
            "achievements" => $achievements
        ]);
    }

    public function getAllUsers(Request $request, $sortByStr, $sortDirStr){
        $sortBy = request()->query("sort_by", $sortByStr);
        $sortDir = request()->query("sort_dir", $sortDirStr);
        $accessToken = PersonalAccessToken::findToken($request->bearerToken())->abilities;
        if(in_array("view-all", $accessToken) || in_array("*", $accessToken)){
            $users = User::withTrashed()->select([
                "id",
                "name",
                "created_at",
                "updated_at",
                "deleted_at"
            ])->orderBy($sortBy, $sortDir)->paginate(30);
        }
        else{
            $users = User::select([
                "id",
                "name",
            ])->orderBy($sortBy, $sortDir)->paginate(30);
        }

        return response()->json([
            "users" => $users
        ]);
           
    }

    public function getOwnPosts(Request $request, $sortByStr, $sortDirStr){
        $token = PersonalAccessToken::findToken($request->bearerToken());

        $userId = $token->tokenable->id;
        
        $sortBy = request()->query("sort_by", $sortByStr);
        $sortDir = request()->query("sort_dir", $sortDirStr);
        $tokenAbilities = $token->abilities;
        if(in_array("view-all", $tokenAbilities) || in_array("*", $tokenAbilities)){
            $posts = Post::withTrashed()->select([
                "id",
                "post",
                "created_at",
                "updated_at",
                "deleted_at"
            ])->where("user_id", $userId)->orderBy($sortBy, $sortDir)->paginate(30);
        }
        else{
            $posts = Post::select([
                "id",
                "post",
                "created_at",
                "updated_at",
            ])->where("user_id", $userId)->orderBy($sortBy, $sortDir)->paginate(30);
        }

        return response()->json([
            "posts" => $posts
        ]);
           
    }

}
