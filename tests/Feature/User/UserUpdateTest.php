<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    protected $seed = true;

    public function test_normal_user_logged_in_update_self(): void
    {
        $user = User::factory()->create([
            'name' => 'Test',
            'email' => 'test@example.com',
            'password' => 'password',
            'deaths' => 0,
            'kills' => 0,
            'waves' => 0,
            'boss1lvl' => 0,
            'boss2lvl' => 0,
            'boss3lvl' => 0,
            'privilege' => 1,
        ]);

        $token = $user->createToken("access", $user->baseAbilities)->plainTextToken;
        $user->save();
        $response = $this->withHeaders([
            "Authorization" => "Bearer ".$token,
        ])->putJson("/api/update/{$user->id}", [
            "name" => "newName",
            "email" => "newemail@email.com"
        ]);

        $response->assertStatus(200);
    }
}
