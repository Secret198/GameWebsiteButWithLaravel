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

    protected $seed = true;

    public function test_with_normal_data(): void
    {
        $response = $this->postJson("/api/user/register", [
            "email" => "test3@example.com",
            "name" => "Test User0",
            "password" => "password0"
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
