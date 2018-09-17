<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public $reg = [
        'first_name' => "Josiah",
        'last_name' => "Adegboye",
        'email' => "josadegboye@gmail.com",
        'password' => "secret",
        'password_confirmation' => "secret",
    ];

    public $login = [
        'email' => "josadegboye@gmail.com",
        'password' => "secret",
    ];

    public function testExample(){
        $this->assertTrue(true);
    }

    public function testRegister(){
        $response = $this->withHeader("Accept", "application/json")
            ->json("POST", 'api/register', $this->reg);
        $response->assertJson(["status" => "success"]);
    }
}
