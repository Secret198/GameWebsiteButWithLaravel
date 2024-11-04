<?php

use App\Http\Controllers\AchievementController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//User routes
Route::post('/user/login', [UserController::class, 'login']);
Route::post('/user/register', [UserController::class,'register']);
// Route::patch("user/update/admin/{id}", [UserController::class,"updateAdmin"])->middleware(["auth:sanctum", "abilities:user-update-admin"]);
// Route::patch("user/update/everyone/{id}", [UserController::class,"updateEveryone"])->middleware(["auth:sanctum", "abilities:user-update-everyone"]);
Route::put("user/update/{id}", [UserController::class,"update"])->middleware(["auth:sanctum", "abilities:user-update"]);
Route::patch("user/update/privilege/{id}", [UserController::class,"makeUserAdmin"])->middleware(["auth:sanctum", "abilities:user-update-admin"]);
Route::delete("/user/{id}", [UserController::class, "delete"])->middleware(["auth:sanctum", "abilities:user-delete"]);
Route::delete("/user/restore/{id}", [UserController::class, "restore"])->middleware(["auth:sanctum", "abilities:user-delete"]);
Route::get("/user/{id}", [UserController::class, "getUserData"])->middleware(["auth:sanctum", "abilities:user-view"]);
Route::get("/user/{sortByStr}/{sortDirStr}", [UserController::class, "getAllUsers"])->middleware(["auth:sanctum", "abilities:user-view"]);
Route::get("/user/{sortByStr}/{sortDirStr}", [UserController::class, "getOwnPosts"])->middleware(["auth:sanctum", "abilities:user-view"]);
Route::get("/user/search/{sortByStr}/{sortDirStr}/{search}", [UserController::class, "searchUsers"])->middleware(["auth:sanctum", "abilities:user-view"]);

//Post routes
Route::post("/post", [PostController::class,"create"])->middleware(["auth:sanctum","abilities:post-create"]);
Route::patch("/post/{id}", [PostController::class,"update"])->middleware(["auth:sanctum","abilities:post-update"]);
Route::delete("/post/{id}", [PostController::class,"delete"])->middleware(["auth:sanctum","abilities:post-delete"]);
Route::get("/post/{id}", [PostController::class,"getPostData"])->middleware(["auth:sanctum","abilities:post-view"]);
Route::get("/post/{sortByStr}/{sortDirStr}", [PostController::class,"getAllPosts"])->middleware(["auth:sanctum","abilities:post-view"]);
Route::get("/post/search/{sortByStr}/{sortDirStr}/{search}", [PostController::class,"searchPosts"])->middleware(["auth:sanctum","abilities:post-view"]);
Route::delete("/post/restore/{id}", [PostController::class,"restore"])->middleware(["auth:sanctum","abilities:post-restore"]);

//achievement routes
Route::post("/achievement", [AchievementController::class, "create"])->middleware(["auth:sanctum","abilities:achievement-create"]);
Route::get("/achievement", [AchievementController::class, "getAllAchievements"]);
Route::patch("/achievement/{id}", [AchievementController::class, "update"])->middleware(["auth:sanctum","abilities:achievement-update"]);
Route::delete("/achievement/{id}", [AchievementController::class, "delete"])->middleware(["auth:sanctum","abilities:achievement-delete"]);
Route::delete("/achievement/restore/{id}", [AchievementController::class, "restore"])->middleware(["auth:sanctum","abilities:achievement-delete"]);