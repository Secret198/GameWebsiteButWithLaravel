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
     * @apiDefine HeadersWithToken
     * @apiHeaderExample {json} Request-headers:
     * {
     *  "Accept": "Application/json",
     *  "Content-type": "Application/json",
     *  "Authorization": "Bearer <bearer-token>"
     * } 
     */

    /**
     * @api {post} /user/login User login
     * @apiGroup User
     * @apiHeaderExample {json} Request-headers:
     * {
     *  "Accept": "Application/json",
     *  "Content-type": "Application/json"
     * }
     * @apiBody {Email} email User's email address
     * @apiBody {String} password User's password
     * @apiError ThePasswordFieldIsRequired The <code>password</code> field is required
     * @apiError TheEmailFieldIsRequired The <code>email</code> field is required
     *  @apiError InvalidEmailOrPassword <code>email</code> or <code>password</code> of user was not found.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 404 Not Found
     *     {
     *       "message": "Invalid email or password"
     *     }
     *    @apiPermission none
     * @apiSuccess {String} message Information about the login.
     * @apiSuccess {Object} user Data of the logged in user.
     * @apiSuccess {Number} user.id   User's <code>id</code>.
     * @apiSuccess {String} user.name User's <code>name</code>.
     * @apiSuccess {Number} user.deaths User's <code>deaths</code>.
     * @apiSuccess {String} user.token User's access <code>token</code>.
     * @apiSuccess {Number} user.privilege User's <code>privilege</code> level.
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
     * @apiBody {Email} email New user's email address
     * @apiBody {String{min: 3}} name New user's user <code>name</code>
     * @apiBody {String{min: 8}} password New user's <code>password</code>
     * @apiError ThePasswordFieldIsRequired The <code>password</code> field is required
     * @apiError TheEmailFieldIsRequired The <code>email</code> field is required
     * @apiError TheNameFieldIsRequired The <code>name</code> field is required
     * @apiError ThePasswordFieldMustBeAtLeast8Characters <code>password</code> must be at least 8 characters.
     * @apiError ThereIsAlreadyAnAccountWithThisEmailAddress A user with this <code>email</code> already exists in the database.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Content
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
        $user->waves = 0;
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
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError InvalidAbilityProvided The user is not authorized to create admins
     * @apiError NoQueryResultsForModel:id User with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *     {
     *       "message": "Unauthenticated."
     *     }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the admin creation.
     * @apiSuccess {Object} user Data of the user that was made admin.
     * @apiSuccess {Number} user.id Users <code>id</code>.
     * @apiSuccess {Number} user.privilege User's new <code>privilege</code> level.
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
            "user"=> $user
            ]);
    }

   
    /**
     * @api {put} /user/update/:id User update
     * @apiParam {Number} id Id of user to be updated
     * @apiGroup User
     * @apiDescription Updating the user data, normal users are only allowed to update their own data, while admin users can update everyone's
     * @apiUse HeadersWithToken
     * @apiBody {String{min:3}} [name] New name of user
     * @apiBody {Email} [email] New email of user
     * @apiBody {Number} [deaths] New number of <code>deaths</code> of the user
     * @apiBody {Number} [kills] New number of <code>kills</code> of the user
     * @apiBody {Number} [waves] New number of <code>waves</code> of user
     * @apiBody {Number} [boss1lvl] New <code>boss1lvl</code> of user
     * @apiBody {Number} [boss2lvl] New <code>boss2lvl</code> of user
     * @apiBody {Number} [boss3lvl] New <code>boss3lvl</code> of user
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError TheNameFieldMustBeAtLeast3Characters. <code>name</code> field must be at least 3 characters.
     * @apiError TheEmailFieldMustBeAValidEmailAddress. <code>email</code> field must be a valid email address.
     * @apiError TheDeathsFieldMustBeANumber <code>deaths</code> field must be a number.
     * @apiError TheKillsFieldMustBeANumber <code>kills</code> field must be a number.
     * @apiError ThewavesFieldMustBeANumber <code>waves</code> field must be a number.
     * @apiError TheBoss1lvlFieldMustBeANumber <code>boss1lvl</code> field must be a number.
     * @apiError TheBoss2lvlFieldMustBeANumber <code>boss2lvl</code> field must be a number.
     * @apiError TheBoss3lvlFieldMustBeANumber <code>boss3lvl</code> field must be a number.
     * @apiError NoQueryResultsForModel:id User with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Content
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
     * @apiSuccess {Number} user.id   User's <code>id</code>.
     * @apiSuccess {Number} user.name User's <code>name</code>.
     * @apiSuccess {Number} user.email User's <code>email</code>.
     * @apiSuccess {Number} user.deaths User's <code>deaths</code>.
     * @apiSuccess {Number} user.kills User's <code>kills</code>.
     * @apiSuccess {Number} user.waves User's <code>waves</code>.
     * @apiSuccess {Number} user.boss1lvl User's <code>boss1lvl</code>.
     * @apiSuccess {Number} user.boss2lvl User's <code>boss2lvl</code>.
     * @apiSuccess {Number} user.boss3lvl User's <code>boss3lv</code>l.
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *       "message": "User updated successfully",
     *          "user": {
     *              "id": 1,
     *              "name": "New name",
     *              "email": "test3@newemail.com",
     *              "deaths": 619,
     *              "kills": 4,
     *              "waves": 5,
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
            "waves" => "nullable|numeric",
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
                "waves" => $user->waves,
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
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError InvalidAbilityProvided The user is not authorized to delete users.
     * @apiError NoQueryResultsForModel:id User with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *     {
     *       "message": "Unauthenticated."
     *     }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the user deletion.
     * @apiSuccess {Object} user Data of the deleted user
     * @apiSuccess {Number} user.id   User's <code>id</code>.
     * @apiSuccess {Number} user.name User's <code>name</code>.
     * @apiSuccess {Number} user.email User's <code>email</code>.
     * @apiSuccess {Number} user.deaths User's <code>deaths</code>.
     * @apiSuccess {Number} user.kills User's <code>kills</code>.
     * @apiSuccess {Number} user.waves User's <code>waves</code>.
     * @apiSuccess {Number} user.boss1lvl User's <code>boss1lvl</code>.
     * @apiSuccess {Number} user.boss2lvl User's <code>boss2lvl</code>.
     * @apiSuccess {Number} user.boss3lvl User's <code>boss3lvl</code>.
     * @apiSuccess {Date} user.created_at When the user was created.
     * @apiSuccess {Date} user.deleted_at When the user was deleted.
     * @apiSuccess {Number} user.privilege <code>privilege</code> level of user.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *     {
     *         "message": "User deleted successfully",
     *         "user": {
     *              "id": 3,
     *              "name": "Test User2",
     *              "email": "test5@example.com",
     *              "deaths": 8,
     *              "kills": 9,
     *              "waves": 8,
     *              "boss1lvl": 3,
     *              "boss2lvl": 2,
     *              "boss3lvl": 5,
     *              "privilege": 10,
     *              "deleted_at": "2025-01-07T07:57:23.000000Z",
     *              "created_at": "2024-11-05T11:49:09.000000Z",
     *              "updated_at": "2024-12-09T12:48:25.000000Z"
     *          }
     *     }
     *    @apiVersion 0.3.0
     */

    public function delete($id){ 

        $user = User::findOrFail($id);
        
        $user->timestamps = false;
        $user->deleteQuietly();
        $user->timestamps = true;

        return response()->json([
            "message" => "User deleted successfully",
            "user" => $user
        ]);
    }

    /**
     * @api {delete} /user/restore/:id Restore user
     * @apiParam {Number} id Id of user to be restored
     * @apiGroup User
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError InvalidAbilityProvided The user is not authorized to restore deleted users.
     * @apiError NoQueryResultsForModel:id User with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *     {
     *       "message": "Unauthenticated."
     *     }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the user restoration.
     * @apiSuccess {Object} user Data of the deleted user
     * @apiSuccess {Number} user.id   User's <code>id</code>.
     * @apiSuccess {Number} user.name User's <code>name</code>.
     * @apiSuccess {Number} user.email User's <code>email</code>.
     * @apiSuccess {Number} user.deaths User's <code>deaths</code>.
     * @apiSuccess {Number} user.kills User's <code>kills</code>.
     * @apiSuccess {Number} user.waves User's <code>waves</code>.
     * @apiSuccess {Number} user.boss1lvl User's <code>boss1lvl</code>.
     * @apiSuccess {Number} user.boss2lvl User's <code>boss2lvl</code>.
     * @apiSuccess {Number} user.boss3lvl User's <code>boss3lvl</code>.
     * @apiSuccess {Date} user.created_at When the user was created.
     * @apiSuccess {Date} user.deleted_at When the user was deleted.
     * @apiSuccess {Number} user.privilege <code>privilege</code> level of user.
     *    @apiSuccessExample {json} Success-Response:
     *   {
     *       "message": "User restored successfully",
     *       "user": {
     *           "id": 5,
     *           "name": "Test User4",
     *           "email": "test7@example.com",
     *           "deaths": 5,
     *           "kills": 6,
     *           "waves": 5,
     *           "boss1lvl": 9,
     *           "boss2lvl": 7,
     *           "boss3lvl": 10,
     *           "privilege": 10,
     *           "deleted_at": null,
     *           "created_at": "2024-11-05T11:49:11.000000Z",
     *           "updated_at": "2024-11-05T11:49:11.000000Z"
     *       }
     *   }
     *    @apiVersion 0.3.0
     */

    public function restore($id){
        $user = User::withTrashed()->findOrFail($id);
        if(!$user){
            return response()->json([
                "message" => "Unable to find user"
            ]);
        }
        $user->timestamps = false;
        $user->restoreQuietly();
        $user->timestamps = true;

        $user->regenerateToken();
        unset($user->token);
        return response()->json([
            "message" => "User restored successfully",
            "user" => $user
        ]);
    }

    /**
     * @api {get} /user/:id Get user data
     * @apiDescription Getting user data, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {Number} id Id of user to be queried
     * @apiGroup User
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiError NoQueryResultsForModel:id User with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} user Data of the requested user.
     * @apiSuccess (Success-Normal user) {Number} user.id   User's <code>id</code>.
     * @apiSuccess (Success-Normal user) {Number} user.name User's <code>name</code>.
     * @apiSuccess (Success-Normal user) {Number} user.email User's <code>email</code>.
     * @apiSuccess (Success-Normal user) {Number} user.deaths User's <code>deaths</code>.
     * @apiSuccess (Success-Normal user) {Number} user.kills User's <code>kills</code>.
     * @apiSuccess (Success-Normal user) {Number} user.waves User's <code>waves</code>.
     * @apiSuccess (Success-Normal user) {Number} user.boss1lvl User's <code>boss1lvl</code>.
     * @apiSuccess (Success-Normal user) {Number} user.boss2lvl User's <code>boss2lvl</code>.
     * @apiSuccess (Success-Normal user) {Number} user.boss3lvl User's <code>boss3lvl</code>.
     * @apiSuccess (Success-Normal user) {Date} user.created_at When the user was created.
     * @apiSuccess (Success-Normal user) {Number} user.privilege <code>privilege</code> level of user.
     * @apiSuccess (Success-Normal user) {Object} achievements Achievements of the selected user.
     * @apiSuccess (Success-Normal user) {Number} achievements.id Achievement <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} achievements.name Achievement <code>name</code>.
     * @apiSuccess (Success-Normal user) {String} achievements.description Achievement <code>description</code>.
     * @apiSuccess (Success-Normal user) {String} achievements.field Column name in the user table.
     * @apiSuccess (Success-Normal user) {Number} achievements.threshold Threshold of the achievement.
     * @apiSuccess (Success-Normal user) {Object} achievements.pivot Data from the pivot table.
     * @apiSuccess (Success-Normal user) {Number} achievements.pivot.user_id User id from the pivot table.
     * @apiSuccess (Success-Normal user) {Number} achievements.pivot.achievement_id Achievement id from the pivot table.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} user Data of the requested user
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.deleted_at When the user was deleted
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.modified_at When the user was last modified
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "user": {
     *               "id": 5,
     *               "name": "Test User4",
     *               "email": "test7@example.com",
     *               "deaths": 5,
     *               "kills": 6,
     *               "waves": 5,
     *               "boss1lvl": 9,
     *               "boss2lvl": 7,
     *               "boss3lvl": 10,
     *               "created_at": "2024-11-05T11:49:11.000000Z",
     *               "privilege": 1
     *           },
     *           "achievements": [
     *               {
     *                   "id": 3,
     *                   "name": "But, now.",
     *                   "description": "And will.",
     *                   "field": "boss3lvl",
     *                   "threshold": 5,
     *                   "pivot": {
     *                       "user_id": 5,
     *                       "achievement_id": 3
     *                   }
     *               }
     *           ]
     *       }
     *    @apiVersion 0.2.0
     */

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
                "waves" => $user->waves,
                "boss1lvl" => $user->boss1lvl,
                "boss2lvl" => $user->boss2lvl,
                "boss3lvl" => $user->boss3lvl,
                "deleted_at" => $user->deleted_at,
                "created_at" => $user->created_at,
                "updated_at" => $user->updated_at,
                "privilege" => $user->privilege
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
                "waves" => $user->waves,
                "boss1lvl" => $user->boss1lvl,
                "boss2lvl" => $user->boss2lvl,
                "boss3lvl" => $user->boss3lvl,
                "created_at" => $user->created_at,
                "privilege" => $user->privilege
            ];
        }

        $achievements = $user->achievements;

        return response()->json([
            "user" => $data,
            "achievements" => $achievements
        ]);
    }

    /**
     * @api {get} /user/all/:sort_by/:sort_dir Get all users
     * @apiDescription Getting user data, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {String} sort_by Field to be used to order the posts by
     * @apiParam {String="asc","desc"} sort_dir Order direction for the posts
     * @apiGroup User
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} users Data of all the users.
     * @apiSuccess (Success-Normal user) {Number} users.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} users.data Array of the returnded users.
     * @apiSuccess (Success-Normal user) {Number} users.data.id User <code>id</code>.
     * @apiSuccess (Success-Normal user) {Number} users.data.privilege User <code>privilege</code> level.
     * @apiSuccess (Success-Normal user) {String} users.data.name User <code>name</code>.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} user.data Data of the requested user
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.data.deleted_at When the user was deleted
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.data.modified_at When the user was last modified
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.data.created_at When the user was created
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *          "users": {
     *              "current_page": 1,
     *              "data": [
     *                  {
     *                      "id": 11,
     *                      "privilege": 1,
     *                      "name": "lakatos"
     *                  },
     *                  {
     *                      "id": 1,
     *                      "privilege": 10,
     *                      "name": "Test User0jkkjkj"
     *                  },
     *                  ...
     *                  {
     *                      "id": 10,
     *                      "privilege": 10,
     *                      "name": "Test User9"
     *                  }
     *              ],
     *              "first_page_url": "http://localhost:8000/api/user/all/name/asc?page=1",
     *              "from": 1,
     *              "last_page": 1,
     *              "last_page_url": "http://localhost:8000/api/user/all/name/asc?page=1",
     *              "links": [
     *                  {
     *                      "url": null,
     *                      "label": "&laquo; Previous",
     *                      "active": false
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/user/all/name/asc?page=1",
     *                      "label": "1",
     *                      "active": true
     *                  },
     *                  {
     *                      "url": null,
     *                      "label": "Next &raquo;",
     *                      "active": false
     *                  }
     *              ],
     *              "next_page_url": null,
     *              "path": "http://localhost:8000/api/user/all/name/asc",
     *              "per_page": 30,
     *              "prev_page_url": null,
     *              "to": 11,
     *              "total": 11
     *          }
     *      }
     *    @apiVersion 0.2.0
     */

    public function getAllUsers(Request $request, $sortByStr, $sortDirStr){
        $sortBy = request()->query("sort_by", $sortByStr);
        $sortDir = request()->query("sort_dir", $sortDirStr);
        $accessToken = PersonalAccessToken::findToken($request->bearerToken())->abilities;
        if(in_array("view-all", $accessToken) || in_array("*", $accessToken)){
            $users = User::withTrashed()->select([
                "id",
                "name",
                "privilege",
                "created_at",
                "updated_at",
                "deleted_at"
            ])->orderBy($sortBy, $sortDir)->paginate(30);
        }
        else{
            $users = User::select([
                "id",
                "privilege",
                "name",
            ])->orderBy($sortBy, $sortDir)->paginate(30);
        }

        return response()->json([
            "users" => $users
        ]);
           
    }

    /**
     * @api {get} /user/:sort_by/:sort_dir Get user's own posts
     * @apiDescription Getting the user's own posts, admin users can view their deleted posts as well
     * @apiParam {String} sort_by Field to be used to order the posts by
     * @apiParam {String="asc","desc"} sort_dir Order direction for the posts
     * @apiGroup User
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} posts Data of all the posts of the logged in user.
     * @apiSuccess (Success-Normal user) {Number} posts.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} posts.data Data of the returnded posts.
     * @apiSuccess (Success-Normal user) {Number} posts.data.id Post <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} posts.data.post Text of the post.
     * @apiSuccess (Success-Normal user) {Number} posts.data.likes Number of likes on the post.
     * @apiSuccess (Success-Normal user) {Date} posts.data.created_at When the post was created.
     * @apiSuccess (Success-Normal user) {Date} posts.data.updated_at When the post was last updated.
     * @apiSuccess (Success-Normal user) {Array} likedPosts Ids of the user's liked posts.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} data Data of all the posts of the logged in user.
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} posts.data.deleted_at When the post was deleted
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "posts": {
     *               "current_page": 1,
     *               "data": [
     *                   {
     *                       "id": 1,
     *                       "post": "Alice desperately: 'he's perfectly idiotic!' And.",
     *                       "likes": 1796,
     *                       "created_at": "2024-11-05T11:49:15.000000Z",
     *                       "updated_at": "2024-11-05T11:49:15.000000Z",
     *                   },
     *                   {
     *                       "id": 2,
     *                       "post": "These were the verses the White Rabbit, who said.",
     *                       "likes": 1796,
     *                       "created_at": "2024-11-05T11:49:15.000000Z",
     *                       "updated_at": "2024-11-05T11:49:15.000000Z",
     *                   },
     *                   ...
     *                   {
     *                       "id": 10,
     *                       "post": "Alice had no pictures or conversations in it.",
     *                       "likes": 1796,
     *                       "created_at": "2024-11-05T11:49:15.000000Z",
     *                       "updated_at": "2024-11-05T11:49:15.000000Z",
     *                   }
     *               ],
     *               "first_page_url": "http://localhost:8000/api/user/id/asc?page=1",
     *               "from": 1,
     *               "last_page": 1,
     *               "last_page_url": "http://localhost:8000/api/user/id/asc?page=1",
     *               "links": [
     *                   {
     *                       "url": null,
     *                       "label": "&laquo; Previous",
     *                       "active": false
     *                   },
     *                   {
     *                       "url": "http://localhost:8000/api/user/id/asc?page=1",
     *                       "label": "1",
     *                       "active": true
     *                   },
     *                   {
     *                       "url": null,
     *                       "label": "Next &raquo;",
     *                       "active": false
     *                   }
     *               ],
     *               "next_page_url": null,
     *               "path": "http://localhost:8000/api/user/id/asc",
     *               "per_page": 30,
     *               "prev_page_url": null,
     *               "to": 4,
     *               "total": 4
     *           }
     *           "likedPosts": [
     *               11,
     *               10 
     *           ]
     *       }
     *    @apiVersion 0.3.0
     */

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
                "likes",
                "created_at",
                "updated_at",
                "deleted_at"
            ])->where("user_id", $userId)->orderBy($sortBy, $sortDir)->paginate(30);
        }
        else{
            $posts = Post::select([
                "id",
                "post",
                "likes",
                "created_at",
                "updated_at",
            ])->where("user_id", $userId)->orderBy($sortBy, $sortDir)->paginate(30);
        }

        $likedPosts = $token->tokenable->likedPosts;
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
     * @api {get} /user/post/search/:sort_by/:sort_dir/:search_for Search for own posts
     * @apiDescription Search from the user's own posts, admin users can view their deleted posts as well
     * @apiParam {String} sort_by Field to be used to order the posts by
     * @apiParam {String="asc","desc"} sort_dir Order direction for the posts
     * @apiParam {String} search_for Keyword to search for in user's posts
     * @apiGroup User
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} posts Data of all the posts where the search word was found.
     * @apiSuccess (Success-Normal user) {Number} posts.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} posts.data Data of the returnded posts.
     * @apiSuccess (Success-Normal user) {Number} posts.data.id Post <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} posts.data.post Text of the post.
     * @apiSuccess (Success-Normal user) {Number} posts.data.likes Number of likes on the post.
     * @apiSuccess (Success-Normal user) {Date} posts.data.created_at When the post was created.
     * @apiSuccess (Success-Normal user) {Date} posts.data.updated_at When the post was last updated.
     * @apiSuccess (Success-Normal user) {Array} likedPosts Ids of the user's liked posts.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} data Data of all the posts where the search word was found..
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} posts.data.deleted_at When the post was deleted
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "posts": {
     *               "current_page": 1,
     *               "data": [
     *                   {
     *                       "id": 1,
     *                       "post": "Alice desperately: 'he's perfectly idiotic!' And.",
     *                       "likes": 1796,
     *                       "created_at": "2024-11-05T11:49:15.000000Z",
     *                       "updated_at": "2024-11-05T11:49:15.000000Z",
     *                   },
     *                   {
     *                       "id": 2,
     *                       "post": "These were the verses the White Rabbit, who said.",
     *                       "likes": 1796,
     *                       "created_at": "2024-11-05T11:49:15.000000Z",
     *                       "updated_at": "2024-11-05T11:49:15.000000Z",
     *                   },
     *                   ...
     *                   {
     *                       "id": 10,
     *                       "post": "Alice had no pictures or conversations in it.",
     *                       "likes": 1796,
     *                       "created_at": "2024-11-05T11:49:15.000000Z",
     *                       "updated_at": "2024-11-05T11:49:15.000000Z",
     *                   }
     *               ],
     *               "first_page_url": "http://localhost:8000/api/user/id/asc?page=1",
     *               "from": 1,
     *               "last_page": 1,
     *               "last_page_url": "http://localhost:8000/api/user/id/asc?page=1",
     *               "links": [
     *                   {
     *                       "url": null,
     *                       "label": "&laquo; Previous",
     *                       "active": false
     *                   },
     *                   {
     *                       "url": "http://localhost:8000/api/user/id/asc?page=1",
     *                       "label": "1",
     *                       "active": true
     *                   },
     *                   {
     *                       "url": null,
     *                       "label": "Next &raquo;",
     *                       "active": false
     *                   }
     *               ],
     *               "next_page_url": null,
     *               "path": "http://localhost:8000/api/user/id/asc",
     *               "per_page": 30,
     *               "prev_page_url": null,
     *               "to": 4,
     *               "total": 4
     *           }
     *           "likedPosts": [
     *               11,
     *               10 
     *           ]
     *       }
     *    @apiVersion 0.3.0
     */

    public function searchOwnPosts(Request $request, $sortByStr, $sortDirStr, $search){
        $sortBy = request()->query("sort_by", $sortByStr);
        $sortDir = request()->query("sort_dir", $sortDirStr);
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());
        if(in_array("view-all", $accessToken->abilities) || in_array("*", $accessToken->abilities)){
            $posts = Post::withTrashed()->select([
                "id",
                "post",
                "likes",
                "created_at",
                "updated_at",
                "deleted_at",
            ])->where("user_id", $accessToken->tokenable->id)
            ->where("post", "LIKE", "%".$search."%")
            ->orderBy($sortBy, $sortDir)->paginate(30);
        }
        else{
            $posts = Post::select([
                "id",
                "post",
                "likes",
                "created_at",
                "updated_at",
            ])->where("user_id", $accessToken->tokenable->id)
            ->where("post", "LIKE", "%".$search."%")
            ->orderBy($sortBy, $sortDir)->paginate(30);
        }

        $likedPosts = $accessToken->tokenable->likedPosts;
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
     * @api {get} /user/search/:sort_by/:sort_dir/:search_for Search for users
     * @apiDescription Getting user data, admin users get additional fields returned in the response, compared to normal users
     * @apiParam {String} sort_by Field to be used to order the posts by
     * @apiParam {String="asc","desc"} sort_dir Order direction for the posts
     * @apiParam {String} search_for Keyword to search for in user names
     * @apiGroup User
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated. User making the request is not logged in or has outdated access token.
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated,"
     *       }
     * @apiPermission normal user
     * @apiSuccess (Success-Normal user) {Object} users Data of all the users.
     * @apiSuccess (Success-Normal user) {Number} users.current_page Current page of the pagination.
     * @apiSuccess (Success-Normal user) {Object} users.data Data of the returnded users.
     * @apiSuccess (Success-Normal user) {Number} users.data.id User <code>id</code>.
     * @apiSuccess (Success-Normal user) {String} users.data.name User <code>name</code>.
     * @apiSuccess (Success-Normal user) {Number} users.data.privilege User <code>privilege</code> level.
     * 
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Object} user.data Data of the requested user
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.data.deleted_at When the user was deleted
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.data.modified_at When the user was last modified
     * @apiSuccess (Success-Admin user (fields returned in addition to the normal user fields)) {Date} user.data.created_at When the user was created
     * @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *      {
     *          "users": {
     *              "current_page": 1,
     *              "data": [
     *                  {
     *                      "id": 9,
     *                      "name": "Test User8",
     *                      "privilege": 10
     *                  },
     *                  {
     *                      "id": 10,
     *                      "name": "Test User9",
     *                      "privilege": 10
     *                  },
     *                  ...
     *                  {
     *                      "id": 1,
     *                      "name": "Test User0jkkjkj",
     *                      "privilege": 10
     *                  }
     *              ],
     *              "first_page_url": "http://localhost:8000/api/user/search/created_at/desc/test?page=1",
     *              "from": 1,
     *              "last_page": 1,
     *              "last_page_url": "http://localhost:8000/api/user/search/created_at/desc/test?page=1",
     *              "links": [
     *                  {
     *                      "url": null,
     *                      "label": "&laquo; Previous",
     *                      "active": false
     *                  },
     *                  {
     *                      "url": "http://localhost:8000/api/user/search/created_at/desc/test?page=1",
     *                      "label": "1",
     *                      "active": true
     *                  },
     *                  {
     *                      "url": null,
     *                      "label": "Next &raquo;",
     *                      "active": false
     *                  }
     *              ],
     *              "next_page_url": null,
     *              "path": "http://localhost:8000/api/user/search/created_at/desc/test",
     *              "per_page": 30,
     *              "prev_page_url": null,
     *              "to": 10,
     *              "total": 10
     *          }
     *      }
     *    @apiVersion 0.2.0
     */

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
                "deleted_at",
                "privilege"
            ])->where("name", "LIKE", "%".$search."%")->orderBy($sortBy, $sortDir)->paginate(30);
        }
        else{
            $users = User::select([
                "id",
                "name",
                "privilege"
            ])->where("name", "LIKE", "%".$search."%")->orderBy($sortBy, $sortDir)->paginate(30);
        }

        return response()->json([
            "users" => $users
        ]);
           
    }

}
