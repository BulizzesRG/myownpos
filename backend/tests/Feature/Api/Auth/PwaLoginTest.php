<?php

namespace Tests\Feature\Api\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PwaLoginTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test **/
    public function can_login_with_valid_credentials()
    {
        $user = User::factory()->create(['is_staff' => 1]);
        $response =  $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);       

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test **/
    public function users_cannot_authenticate_with_invalid_credentials()
    {
        $user = User::factory()->create(['is_staff' => 1]);
        $response =  $this->postJson('/login', [
            'email' => 'bad@email.com',
            'password' => 'password'
        ]);     
        
        $response->assertJsonValidationErrors(['email']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    }    
}
