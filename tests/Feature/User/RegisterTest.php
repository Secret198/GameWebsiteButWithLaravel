<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    public function test_with_normal_data(): void
    {
        $response = $this->postJson("/api/user/register", [
            "email" => "lajos69@lajos.com",
            "name" => "Lajos2",
            "password" => "Password59?"
        ]);

        $response->assertStatus(200);
    }

    public function test_with_no_data(): void
    {
        $response = $this->postJson("/api/user/register", [
        ]);

        $response->assertStatus(422);
    }

    public function test_with_wrong_data(): void
    {
        $response = $this->postJson("/api/user/register", [
            "email" => "test3example.com",
            "name" => "Tes",
            "password" => "fdf"
        ]);

        $response->assertStatus(422);
    }
}
