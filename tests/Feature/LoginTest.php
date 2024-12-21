<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test a successful login (happy day scenario).
     */
    public function test_user_can_login_successfully(): void
    {
        $user = User::factory()->create([
            'password' => 'Password123#'
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'Password123#',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'token_type',
            ]);
    }

    /**
     * Test validation errors for login.
     */
    public function test_login_validation_errors(): void
    {
        $data = [
            'email' => '',
            'password' => '',
        ];

        $response = $this->postJson('/api/v1/login', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
                'password'
            ]);
    }

    /**
     * Test login with incorrect credentials.
     */
    public function test_login_with_incorrect_credentials(): void
    {
        $user = User::factory()->create([
            'password' => "Password123#",
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The provided credentials do not match our records.',
            ]);
    }
}
