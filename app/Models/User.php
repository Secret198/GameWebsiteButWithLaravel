<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, SoftDeletes, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'deaths',
        'kills',
        'waves',
        'boss1lvl',
        'boss2lvl',
        'boss3lvl',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $baseAbilities = ["user-update", "user-view", "post-view", "post-create", "post-update", "post-delete", "post-get-all", "achievement-get-all", "user-get-all"];

    public function regenerateToken(){
        $this->tokens()->delete();
        
        switch($this->privilege){
            case 1:
                $this->token = $this->createToken("access", $this->baseAbilities)->plainTextToken;       
                break;
            case 10:
                $this->token = $this->createToken("access", ["*"])->plainTextToken;       
                break;
        }
    }

    public function LoginTokenHandle(){
        
        switch($this->privilege){
            case 1:
                $this->token = $this->createToken("access", $this->baseAbilities)->plainTextToken;       
                break;
            case 10:
                $this->token = $this->createToken("access", ["*"])->plainTextToken;       
                break;
        }
        
    }

    private function canhave($field, $threshold){
        switch($field){
            case "deaths":
                if($this->deaths >= $threshold){
                    return true;
                }
                else{
                    return false;
                }
            case "kills":
                if($this->kills >= $threshold){
                    return true;
                }
                else{
                    return false;
                }

            case "waves":
                if($this->waves >= $threshold){
                    return true;
                }
                else{
                    return false;
                }
            case "boss1lvl":
                if($this->boss1lvl >= $threshold){
                    return true;
                }
                else{
                    return false;
                }
            case "boss2lvl":
                if($this->boss2lvl >= $threshold){
                    return true;
                }
                else{
                    return false;
                }
            case "boss3lvl":
                if($this->boss3lvl >= $threshold){
                    return true;
                }
                else{
                    return false;
                }
        }
    }

    public function checkForAchievements(){
        $playerAchievements = $this->achievements()->get();
        $allAchievements = Achievement::all();
        foreach($allAchievements as $achievement){
            if($this->canHave($achievement->field, $achievement->threshold) && !$playerAchievements->contains("id", $achievement->id)){
                $this->achievements()->attach($achievement->id);
            }
        }
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function posts(){
        return $this->hasMany(Post::class);
    }

    public function achievements(){
        return $this->belongsToMany(Achievement::class);
    }

    public function likedPosts(){
        return $this->belongsToMany(Post::class);
    }
}
