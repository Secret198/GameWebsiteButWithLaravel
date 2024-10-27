<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'points',
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

    public $baseAbilities = ["user-update", "post-create", "post-update", "post-delete", "post-get-all", "achievement-get-all", "user-get-all"];

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

    public function checkForAchievements(){
        $this->achievements()->getAll();
        return [];
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
}
