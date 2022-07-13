<?php

namespace Tests\Feature\Api\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Tests\TestCase;


class DeviceLoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /** @test **/
    // public function can_login_with_valid_credentials()
    // {
    //     $user = User::factory()->create(['is_staff' => 1]);
    //     $response = $this->postJson(route('api.v1.login'),[
    //         'email' => $user->email,
    //         'password' => 'password'
    //     ]);

    //     $token = $response->getData()->data->attributes->access_token;

    //     $response->assertHeader(
    //         'Location',
    //         route('api.v1.home')
    //     );        

    //     $response->assertStatus(Response::HTTP_OK);
    //     $response->assertExactJson([
    //         "status" => "success", 
    //         "data" => [
    //             "type" => 'token',
    //             "attributes" => [
    //                 "access_token" => $token,
    //                 "token_type" => 'Bearer'
    //             ]
    //         ]
    //     ]);        
    // } 
}
