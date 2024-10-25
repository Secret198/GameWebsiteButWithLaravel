<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;

class UserController extends Controller
{
    public function login(Request $request){
        $email = $request->input("email");
        $password = $request->input("password");

        $request->validate([
            "email" => "required|email",
            "password"=> "required",
        ]);

        $user = User::where("email", $email)->first();
        
        if(!$user || !Hash::check($password, $password ? $user->password : '')){
            return response()->json([
                'message' => "Invalid email or password"       
            ], 401);
        }

        $user->tokens()->delete();

        switch($user->privilege){
            case 1:
                $user->token = $user->createToken("access", ["user-update", "post-create", "post-update", "post-delete"])->plainTextToken;       
                break;
            case 10:
                $user->token = $user->createToken("access", ["*"])->plainTextToken;       
                break;
        }

        return response()->json([
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

        $user->token = $user->createToken("access", ["user-update", "post-create", "post-delete", "post-update"])->plainTextToken;

        return response()->json([
            "user" => [
                "id" => $user->id,
                "token" => $user->token,
                "privilege" => $user->privilege
            ]
        ]);
        
    }

    public function makeUserAdmin(Request $request, $id){
        $user = User::findOrFail($id);

        $user->tokens()->delete();
        $user->token = $user->createToken("access", ["*"])->plainTextToken;     //Ha akarjuk akkor egyesével beírogatni
        $user->update(["privilege" => 10]);

        return response()->json([
            "user"=> [
                "id"=> $user->id,
                "token"=> $user->token,
                "privilege" => $user->privilege
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
        $user = User::findOrFail($id);

        $user->update($request->all());

        return response()->json([
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "deaths" => $user->deaths,
            "kills" => $user->kills,
            "points" => $user->points,
            "boss1lvl" => $user->boss1lvl,
            "boss2lvl" => $user->boss2lvl,
            "boss3lvl" => $user->boss3lvl,
        ]);
    }

    public function delete(Request $request, $id){
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            "message" => "User deleted successfully"
        ]);
    }
}
