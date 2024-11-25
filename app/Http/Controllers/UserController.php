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

    /**
     * @api {post} /user/login User login
     * @apiGroup User
     * @apiHeaderExample {json} Request-headers:
     * {
     *  "Accept": "Application/json",
     *  "Content-type": "Application/json"
     * }
     *  @apiError InvalidEmailOrPassword <code>email</code> or <code>password</code> of user was not found.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Invalid email or password"
     *     }
     *    @apiPermission none
     * @apiSuccess {String} message Information about the login.
     * @apiSuccess {Object} user Data of the logged in user.
     * @apiSuccess {Number} user.id   Users id.
     * @apiSuccess {String} user.name User name.
     * @apiSuccess {Number} user.deaths User's deaths.
     * @apiSuccess {String} user.token User's access token.
     * @apiSuccess {Number} user.privilege User's privilege level.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *      "message": "Login successful",
     *       "user": 
     *          {
     *             "id": 2,
     *             "name": "Test User1",
     *             "deaths": 4,
     *             "token": "4|a7Dcj9AirLlhXHElDufzY1Wvo7epglPx7Qca9NZk3570b23a",
     *             "privilege": 10
     *          }
     *    }
     *    @apiVersion 0.1.0
     */

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

   /**
     * @api {post} /user/register User registration
     * @apiGroup User
     * @apiHeaderExample {json} Request-headers:
     * {
     *  "Accept": "Application/json",
     *  "Content-type": "Application/json"
     * }
     * @apiError ThePasswordFieldMustBeAtLeast8Characters <code>password</code> must be at least 8 characters.
     * @apiError ThereIsAlreadyAnAccountWithThisEmailAddress A user with this <code>email</code> already exists in the database.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *           "message": "The password field must be at least 8 characters.",
     *           "errors": {
     *               "password": [
     *                   "The password field must be at least 8 characters."
     *               ]
     *           }
     *      }
     * @apiPermission none
     * @apiSuccess {String} message Information about the registration.
     * @apiSuccess {Object} user Data of the newly registered user.
     * @apiSuccess {Number} user.id   Users id.
     * @apiSuccess {String} user.token User's access token.
     * @apiSuccess {Number} user.privilege User's privilege level.
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

    public function register(Request $request){
        $request->validate([
            "email" => "required|email",
            "name" => "required|min:3",
            "password" => "required|min:8",
        ]);

        $name = $request->input("name");
        $email = $request->input("email");
        $password = $request->input("password");

        $userAlreadyExists = User::where("email", $email)->first();
        if($userAlreadyExists){
            return response()->json([
                "message" => "There is already an account with this email address"
            ], 409);
        }
        
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

    /**
     * @api {patch} /user/update/privilege/:id Making other users admin
     * @apiParam {Number} id Id of user to be made admin
     * @apiGroup User
     * @apiHeaderExample {json} Request-headers:
     * {
     *  "Accept": "Application/json",
     *  "Content-type": "Application/json",
     *  "Authorization: "Bearer <bearer-token>"
     * }
     * @apiError Unauthenticated User making the request is not logged in
     * @apiError InvalidAbilityProvided Normal users are not authorized to make other users admin.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Unauthenticated."
     *     }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the admin creation.
     * @apiSuccess {Object} user Data of the user that was made admin.
     * @apiSuccess {Number} user.id Users id.
     * @apiSuccess {Number} user.privilege User's privilege level.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *     {
     *         "message": "Admin created successfully",
     *         "user": {
     *             "id": 9,
     *             "privilege": 10
     *         }
     *     }
     *    @apiVersion 0.1.0
     */

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

   
    /**
     * @api {put} /user/update/:id User update
     * @apiParam {Number} id Id of user to be updated
     * @apiGroup User
     * @apiHeaderExample {json} Request-headers:
     * {
     *  "Accept": "Application/json",
     *  "Content-type": "Application/json",
     *  "Authorization: "Bearer <bearer-token>"
     * }
     * @apiError TheNameFieldMustBeAtLeast3Characters. <code>name</code> field must be at least 3 characters.
     * @apiError TheEmailFieldMustBeAValidEmailAddress. <code>email</code> field must be a valid email address.
     * @apiError TheDeathsFieldMustBeANumber <code>deaths</code> field must be a number.
     * @apiError TheKillsFieldMustBeANumber <code>kills</code> field must be a number.
     * @apiError ThePointsFieldMustBeANumber <code>points</code> field must be a number.
     * @apiError TheBoss1lvlFieldMustBeANumber <code>boss1lvl</code> field must be a number.
     * @apiError TheBoss2lvlFieldMustBeANumber <code>boss2lvl</code> field must be a number.
     * @apiError TheBoss3lvlFieldMustBeANumber <code>boss3lvl</code> field must be a number.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *       {
     *           "message": "The name field must be at least 3 characters.",
     *           "errors": {
     *               "name": [
     *                   "The name field must be at least 3 characters."
     *               ]
     *           }
     *       }
     * @apiPermission normal user
     * @apiSuccess {String} message Information about the update.
     * @apiSuccess {Object} user Data of the updated user.
     * @apiSuccess {Number} user.id   Users id.
     * @apiSuccess {Number} user.name User name.
     * @apiSuccess {Number} user.email User email.
     * @apiSuccess {Number} user.deaths User deaths.
     * @apiSuccess {Number} user.kills User kills.
     * @apiSuccess {Number} user.points User points.
     * @apiSuccess {Number} user.boss1lvl User boss 1 level.
     * @apiSuccess {Number} user.boss2lvl User boss 2 level.
     * @apiSuccess {Number} user.boss3lvl User boss 3 level.
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *       "message": "User updated successfully",
     *          "user": {
     *              "id": 1,
     *              "name": "This is epic",
     *              "email": "test3@yourmom.com",
     *              "deaths": 69,
     *              "kills": 4,
     *              "points": 5,
     *              "boss1lvl": 4,
     *              "boss2lvl": 1,
     *              "boss3lvl": 7
     *          }
     *       }
     *    @apiVersion 0.1.0
     */

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

    /**
     * @api {delete} /user/:id Delete user
     * @apiParam {Number} id Id of user to be deleted
     * @apiGroup User
     * @apiHeaderExample {json} Request-headers:
     * {
     *  "Accept": "Application/json",
     *  "Content-type": "Application/json",
     *  "Authorization: "Bearer <bearer-token>"
     * }
     * @apiError Unauthenticated User making the request is not logged in
     * @apiError InvalidAbilityProvided Normal users are not authorized to delete users.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Unauthenticated."
     *     }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the user deletion.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *     {
     *         "message": "User deleted successfully",
     *     }
     *    @apiVersion 0.1.0
     */

    public function delete($id){
        $user = User::findOrFail($id);        
        $user->delete();
        return response()->json([
            "message" => "User deleted successfully"
        ]);
    }

    /**
     * @api {delete} /user/restore/:id Restore user
     * @apiParam {Number} id Id of user to be restored
     * @apiGroup User
     * @apiHeaderExample {json} Request-headers:
     * {
     *  "Accept": "Application/json",
     *  "Content-type": "Application/json",
     *  "Authorization: "Bearer <bearer-token>"
     * }
     * @apiError Unauthenticated User making the request is not logged in
     * @apiError InvalidAbilityProvided Normal users are not authorized to restore deleted users.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Unauthenticated."
     *     }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the user restoration.
     * @apiSuccess {Object} user Data of restored user
     * @apiSuccess {Number} user.id Id of restored user
     * @apiSuccess {Number} user.privilege Privilege of restored user
     *    @apiSuccessExample {json} Success-Response:
     *   {
     *       "message": "User restored successfully",
     *       "user": {
     *           "id": 6,
     *           "privilege": 1
     *       }
     *   }
     *    @apiVersion 0.1.0
     */

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

    public function searchUsers(Request $request, $sortByStr, $sortDirStr, $search){
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
            ])->where("name", "LIKE", "%".$search."%")->orderBy($sortBy, $sortDir)->paginate(30);
        }
        else{
            $users = User::select([
                "id",
                "name",
            ])->where("name", "LIKE", "%".$search."%")->orderBy($sortBy, $sortDir)->paginate(30);
        }

        return response()->json([
            "users" => $users
        ]);
           
    }

}
