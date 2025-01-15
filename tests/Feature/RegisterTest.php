<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful registration.
     */
    public function test_user_can_register(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'Password123*',
            'password_confirmation' => 'Password123*',
        ];

        $response = $this->postJson('/api/v1/register', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'token',
                'token_type',
                'user' => [
                    'name',
                    'email',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        $user = User::where('email', $data['email'])->first();
        $this->assertTrue(Hash::check($data['password'], $user->password));
    }

    /**
     * Test validation errors for registration.
     */
    public function test_registration_validation_errors(): void
    {
        $data = [
            'name' => '', // Missing name
            'email' => 'invalid-email', // Invalid email
            'password' => 'short', // Password too short
            'password_confirmation' => 'mismatch', // Password confirmation mismatch
        ];

        $response = $this->postJson('/api/v1/register', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'name',
            'email',
            'password',
        ]);
    }
}
