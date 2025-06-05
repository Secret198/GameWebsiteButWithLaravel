<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    protected $seed = true;
    public function test_login_with_normal_data(): void
    {

        $response = $this->postJson( "api/user/login", [
            "email" => "test4@example.com",
            "password" => "password1"
        ]);

        
        $response->assertStatus(200);
    }

    public function test_login_with_no_data(): void
    {

        $response = $this->postJson( "api/user/login", [
            "email" => "",
            "password" => ""
        ]);

        
        $response->assertStatus(422);
    }

    public function test_login_with_wrong_data(): void
    {

        $response = $this->postJson( "api/user/login", [
            "email" => "testyescvasd",
            "password" => "asdasdf"
        ]);

        
        $response->assertStatus(422);
    }

    
}
