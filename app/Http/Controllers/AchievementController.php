<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function create(Request $request){
        $request->validate([
            "name" => "required",
            "field" => "required",
            "threshold" => "required|numeric"
        ]);

        $achievement = new Achievement();
        $achievement->name = $request->name;
        $achievement->field = $request->field;
        $achievement->threshold = $request->threshold;
        $achievement->save();
        return response()->json([
            "message"=> "Achievement created successfully",
            "achievement" => [
                "id"=> $achievement->id,
                "name"=> $achievement->name,
            ]
        ]);
    }

    public function update(Request $request, $id){
        $request->validate([
            "name" => "nullable",
            "field" => "nullable",
            "threshold" => "nullable"
        ]);

        $achievement = Achievement::findOrFail($id);
        $achievement->update($request->all());

        return response()->json([
            "message"=> "Achievement updated successfully",
        ]);
    }

    public function delete($id){
        $achievement = Achievement::findOrFail($id);
        $achievement->delete();
        return response()->json([
            "message"=> "Achievement deleted successfully"
        ]);
    }

    public function restore($id){
        $achievement = Achievement::withTrashed()->findOrFail($id);
        $achievement->restore();
        return response()->json([
            "message"=> "Achievement restored successfully"
        ]);
    }

    public function getAllAchievements(){
        $achievements = Achievement::all();
        return response()->json([
            "achievements" => $achievements
        ]);
    }
}
