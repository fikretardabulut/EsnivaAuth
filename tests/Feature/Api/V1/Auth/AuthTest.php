<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Fikret',
            'email' => 'fikret@example.com',
            'password' => 'Esniva123!',
            'password_confirmation' => 'Esniva123!',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'email_verified_at',
                        'created_at',
                        'updated_at',
                    ],
                    'auth' => [
                        'token',
                        'type',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'fikret@example.com',
        ]);
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'name' => 'Fikret',
            'email' => 'fikret@example.com',
            'password' => 'Esniva123!',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'fikret@example.com',
            'password' => 'Esniva123!',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login successful.')
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'auth' => [
                        'token',
                        'type',
                    ],
                ],
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'fikret@example.com',
            'password' => 'Esniva123!',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'fikret@example.com',
            'password' => 'WrongPassword123!',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'The provided credentials are incorrect.');
    }

    public function test_authenticated_user_can_fetch_profile(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/auth/me');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', $user->email);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/auth/logout');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Logged out successfully.');
    }

    public function test_guest_cannot_access_me_endpoint(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }
}