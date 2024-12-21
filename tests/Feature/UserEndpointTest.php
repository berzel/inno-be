<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserEndpointTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user data retrieval (happy path).
     */
    public function test_authenticated_user_can_access_user_endpoint(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/user');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'name',
                'email',
            ]);

        $response->assertJson([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * Test unauthorized access to the user endpoint.
     */
    public function test_unauthenticated_user_cannot_access_user_endpoint(): void
    {
        $response = $this->getJson('/api/v1/user');

        // Assert the response status and error message
        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
}
