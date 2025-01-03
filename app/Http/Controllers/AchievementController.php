<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;

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
        $request->validate([
            "name" => "required",
            "field" => "required",
            "threshold" => "required|numeric|min:0",
            "description" => "required"
        ]);

        $achievement = new Achievement();
        $achievement->name = $request->name;
        $achievement->field = $request->field;
        $achievement->threshold = $request->threshold;
        $achievement->description = $request->description;
        $achievement->save();
        return response()->json([
            "message"=> "Achievement created successfully",
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
        $request->validate([
            "name" => "nullable",
            "field" => "nullable",
            "threshold" => "nullable|numeric|min:0",
            "description" => "nullable"
        ]);

        $achievement = Achievement::findOrFail($id);
        $achievement->update($request->all());

        return response()->json([
            "message"=> "Achievement updated successfully",
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

    public function show(string $id){
        $achievement = Achievement::findOrFail($id);

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

    public function delete($id){
        $achievement = Achievement::findOrFail($id);
        $achievement->delete();
        return response()->json([
            "message"=> "Achievement deleted successfully"
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

    public function restore($id){
        $achievement = Achievement::withTrashed()->findOrFail($id);
        $achievement->restore();
        return response()->json([
            "message"=> "Achievement restored successfully"
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
     * @apiSuccess {String} achievements.field <code>field</code> of achievement.
     * @apiSuccess {String} achievements.description <code>description</code> of achievement.
     * @apiSuccess {Number} achievements.threshold <code>threshold</code> of achievement.
     *    @apiSuccessExample {json} Success-Response:
     *       {
     *           "achievements": [
     *               {
     *                   "id": 2,
     *                   "name": "On which.",
     *                   "description": "Dormouse.",
     *                   "field": "deaths",
     *                   "threshold": 0
     *               },
     *               {
     *                   "id": 3,
     *                   "name": "Oh dear!.",
     *                   "description": "On which.",
     *                   "field": "boss3lvl",
     *                   "threshold": 8
     *               }
     *           ]
     *       }
     *    @apiVersion 0.1.0
     */

    public function getAllAchievements(){
        $achievements = Achievement::all();
        return response()->json([
            "achievements" => $achievements
        ]);
    }
}
