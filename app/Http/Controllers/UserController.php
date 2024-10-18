<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        
        if(!$user || Hash::check($password, $password ? $user->password : '')){
            return response()->json([
                
            ])
        }
    }
}
