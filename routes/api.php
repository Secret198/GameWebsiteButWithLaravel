<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/register', [UserController::class,'register']);
// Route::patch("user/update/admin/{id}", [UserController::class,"updateAdmin"])->middleware(["auth:sanctum", "abilities:user-update-admin"]);
// Route::patch("user/update/everyone/{id}", [UserController::class,"updateEveryone"])->middleware(["auth:sanctum", "abilities:user-update-everyone"]);
Route::patch("user/update/{id}", [UserController::class,"update"])->middleware(["auth:sanctum", "abilities:user-update"]);
Route::patch("user/update/privilege/{id}", [UserController::class,"makeUserAdmin"])->middleware(["auth:sanctum", "abilities:user-update-admin"]);
Route::delete("/user/{id}", [UserController::class, "delete"])->middleware(["auth:sanctum", "abilities:user-delete"]);
Route::delete("/user/restore/{id}", [UserController::class, "restore"])->middleware(["auth:sanctum", "abilities:user-delete"]);
Route::post("/post", [PostController::class,"create"])->middleware(["auth:sanctum","abilities:post-create"]);
Route::patch("/post/{id}", [PostController::class,"update"])->middleware(["auth:sanctum","abilities:post-update"]);
Route::delete("/post/{id}", [PostController::class,"delete"])->middleware(["auth:sanctum","abilities:post-delete"]);