<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Laravel\Sanctum\PersonalAccessToken;

class AchievementController extends Controller
{

    /**
     * @api {post} /achievement Achievement creation
     * @apiGroup Achievement
     * @apiUse HeadersWithToken
     * @apiBody {String} name Name of the achievement
     * @apiBody {String} field The column name from the user table when handing out the achievement
     * @apiBody {Number{min: 0}} threshold The amount that has to reached to be awarded the achievement
     * @apiBody {String} description The description of the achievement
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError TheNameFieldIsRequired The <code>name</code> field is required
     * @apiError TheFieldFieldIsRequired The <code>field</code> field is required
     * @apiError TheThresholdFieldIsRequired The <code>threshold</code> field is required
     * @apiError TheThresholdFieldIsMustBeANumber The <code>threshold</code> field must be a number
     * @apiError TheDescriptionFieldIsRequired The <code>description</code> field is required
     * @apiError InvalidAbilityProvided The user is not authorized to create achievements
     * @apiError TheThresholdFieldMustBeAtLeast0. The <code>threshold</code> must be larger than 0
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Content
     *       {
     *           "message": "The field field is required. (and 2 more errors)",
     *           "errors": {
     *               "field": [
     *                   "The field field is required."
     *               ],
     *               "threshold": [
     *                   "The threshold field is required."
     *               ],
     *               "description": [
     *                   "The description field is required."
     *               ]
     *           }
     *       }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the achievement creation.
     * @apiSuccess {Object} achievement Data of the newly created achievement.
     * @apiSuccess {Number} achievement.id <code>id</code> of the new achievement.
     * @apiSuccess {String} achievement.name <code>name</code> of the new achievement.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Achievement created successfully",
     *           "post": {
     *               "id": 11
     *           }
     *       }
     *    @apiVersion 0.3.0
     */

    public function create(Request $request){

        $languages = $request->getLanguages();
        if($languages){
            App::setLocale($languages[1]);
        }
        
        $request->validate([
            "name" => "required",
            "field" => "required",
            "threshold" => "required|numeric|min:0",
            "description" => "required"
        ]);

        if(($request->field == "boss1lvl" || $request->field == "boss2lvl" || $request->field == "boss3lvl") && $request->threshold > 1 ){
            return response()->json([
                "message" => __("messages.achievementBossThresholdError")
            ], 400);
        }

        $achievement = new Achievement();
        $achievement->name = $request->name;
        $achievement->field = $request->field;
        $achievement->threshold = $request->threshold;
        $achievement->description = $request->description;
        $achievement->save();
        return response()->json([
            "message"=> __("messages.achievementCreateSuccess"),
            "achievement" => [
                "id"=> $achievement->id,
                "name"=> $achievement->name,
            ]
        ]);
    }

    /**
     * @api {patch} /achievement/:id Achievement update
     * @apiParam {Number} id Id of achievement to be updated
     * @apiGroup Achievement
     * @apiUse HeadersWithToken
     * @apiBody {String} [name] Name of the achievement
     * @apiBody {String} [field] The column name from the user table when handing out the achievement
     * @apiBody {Number{min: 0}} [threshold] The amount that has to reached to be awarded the achievement
     * @apiBody {String} [description] The description of the achievement
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError TheThresholdFieldIsMustBeANumber The <code>threshold</code> field must be a number
     * @apiError InvalidAbilityProvided The user is not authorized to update achievements
     * @apiError NoQueryResultsForModel:id Achievement with <code>id</code> could not be found
     * @apiError TheThresholdFieldMustBeAtLeast0. The <code>threshold</code> must be larger than 0
     *  @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 422 Unprocessable Content
     *       {
     *           "message": "The threshold field must be a number.",
     *           "errors": {
     *               "threshold": [
     *                   "The threshold field must be a number."
     *               ]
     *           }
     *       }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the achievement update.
     * @apiSuccess {Object} achievement Data of the updated achievement.
     * @apiSuccess {Number} achievement.id <code>id</code> of the updated achievement.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Achievement updated successfully",
     *           "achievement": {
     *               "id": 10
     *           }
     *       }
     *    @apiVersion 0.3.0
     */

    public function update(Request $request, $id){

        $languages = $request->getLanguages();
        if($languages){
            App::setLocale($languages[1]);
        }

        $request->validate([
            "name" => "nullable",
            "field" => "nullable",
            "threshold" => "nullable|numeric|min:0",
            "description" => "nullable"
        ]);

        if(($request->field == "boss1lvl" || $request->field == "boss2lvl" || $request->field == "boss3lvl") && $request->threshold > 1 ){
            return response()->json([
                "message" => __("messages.achievementBossThresholdError")
            ], 400);
        }

        $achievement = Achievement::withTrashed()->findOrFail($id);
        $achievement->update($request->all());

        return response()->json([
            "message"=> __("messages.achievementUpdateSuccess"),
            "achievement" => [
                "id" => $achievement->id
            ]
        ]);
    }

    /**
     * @api {get} /achievement/:id Get achievement
     * @apiParam {Number} id Id of achievement to be queried
     * @apiGroup Achievement
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError NoQueryResultsForModel:id Achievement with <code>id</code> could not be found
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated.",
     *       }
     * @apiPermission none
     * @apiSuccess {Object} achievement Data of the updated achievement.
     * @apiSuccess {Number} achievement.id <code>id</code> of the achievement.
     * @apiSuccess {String} achievement.name <code>name</code> of the achievement.
     * @apiSuccess {String} achievement.description <code>description</code> of the achievement.
     * @apiSuccess {String} achievement.field <code>field</code> of the achievement.
     * @apiSuccess {Number} achievement.threshold <code>threshold</code> of the achievement.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "achievement": {
     *               "id": 1,
     *               "name": "You shall not pass",
     *               "description": "eeeeeeeeeeeeeeee",
     *               "field": "boss1lvl",
     *               "threshold": 2
     *           }
     *       }
     *    @apiVersion 0.1.0
     */

    public function show(Request $request, string $id){
        $languages = $request->getLanguages();
        if($languages){
            App::setLocale($languages[1]);
        }

        $achievement = Achievement::withTrashed()->findOrFail($id);

        return response()->json([
            "achievement" => $achievement
        ]);
    }

    /**
     * @api {delete} /achievement/:id Delete achievement
     * @apiParam {Number} id Id of achievement to be deleted
     * @apiGroup Achievement
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError NoQueryResultsForModel:id Achievement with <code>id</code> could not be found
     * @apiError InvalidAbilityProvided The user is not authorized to delete achievements
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated.",
     *       }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the achievement deletion.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Achievement deleted successfully"
     *       }
     *    @apiVersion 0.1.0
     */

    public function delete(Request $request, $id){

        $languages = $request->getLanguages();
        if($languages){
            App::setLocale($languages[1]);
        }

        $achievement = Achievement::findOrFail($id);
        $achievement->delete();
        return response()->json([
            "message"=> __("messages.achievementDeleteSuccess"),
            "achievement" => $achievement
        ]);
    }

    /**
     * @api {delete} /achievement/restore/:id Restore achievement
     * @apiParam {Number} id Id of achievement to be restore
     * @apiGroup Achievement
     * @apiUse HeadersWithToken
     * @apiError Unauthenticated User making the request is not logged in or has outdated access token.
     * @apiError NoQueryResultsForModel:id Achievement with <code>id</code> could not be found
     * @apiError InvalidAbilityProvided The user is not authorized to restore achievements
     * @apiErrorExample {json} Error-Response:
     *     HTTP/1.1 401 Unathorized
     *       {
     *           "message": "Unauthenticated.",
     *       }
     * @apiPermission admin
     * @apiSuccess {String} message Information about the achievement deletion.
     *    @apiSuccessExample {json} Success-Response:
     *    HTTP/1.1 200 OK
     *       {
     *           "message": "Achievement restored successfully"
     *       }
     *    @apiVersion 0.1.0
     */

    public function restore(Request $request, $id){
        $languages = $request->getLanguages();
        if($languages){
            App::setLocale($languages[1]);
        }

        $achievement = Achievement::withTrashed()->findOrFail($id);
        $achievement->restore();
        return response()->json([
            "message"=> __("messages.achievementRestoreSuccess"),
            "achievement" => $achievement
        ]);
    }

    /**
     * @api {get} /achievement Get all achievements
     * @apiGroup Achievement
     * @apiUse HeadersWithToken
     * @apiPermission none
     * @apiSuccess {Object} achievements Array of all the achievements.
     * @apiSuccess {Number} achievements.id <code>id</code> of achievement.
     * @apiSuccess {String} achievements.name <code>name</code> of achievement.
     * @apiSuccess {String} achievements.description <code>description</code> of achievement.
     * 
     * @apiSuccess (Admin (Fields returned in addition to the normal ones)) achievements.deleted_at The date when the achievement was deleted)
     *    @apiSuccessExample {json} Success-Response:
     *       {
     *           "achievements": [
     *             {
     *                  "id": 1,
     *                  "name": "You shall not pass",
     *                  "description": "eeeeeeeeeeeeeeee",
     *              },
     *              {
     *                  "id": 2,
     *                  "name": "On which.",
     *                  "description": "Dormouse.",
     *              },
     *           ]
     *       }
     *    @apiVersion 0.3.0
     */

    public function getAllAchievements(Request $request){
        $languages = $request->getLanguages();
        if($languages){
            App::setLocale($languages[1]);
        }
        
        $accessToken = PersonalAccessToken::findToken($request->bearerToken());
        if($accessToken && (in_array("achievement-delete", $accessToken->abilities) || in_array("*", $accessToken->abilities))){
            $achievements = Achievement::withTrashed()->select([
                    "id",
                    "name",
                    "description",
                    "deleted_at"
                ]
            )->get();
        }
        else{
            $achievements = Achievement::select([
                    "id",
                    "name",
                    "description"
                ]
            )->get();
        }
        return response()->json([
            "achievements" => $achievements
        ]);
    }
}
