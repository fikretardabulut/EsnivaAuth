<?php

namespace Tests\Feature\Api\V1\Tokens;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TokenManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_list_tokens(): void
    {
        $user = User::factory()->create();

        $user->createToken('web');
        $user->createToken('mobile');

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/tokens');

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'tokens',
                ],
            ]);
    }

    public function test_authenticated_user_can_create_token(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/tokens', [
            'name' => 'postman-dev',
            'abilities' => ['auth:read', 'tokens:read'],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Token created successfully.')
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'plain_text_token',
                    'type',
                ],
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'name' => 'postman-dev',
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);
    }

    public function test_authenticated_user_can_delete_own_token(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $token = $user->createToken('delete-me');

        $tokenModel = PersonalAccessToken::query()
            ->where('tokenable_id', $user->id)
            ->where('name', 'delete-me')
            ->first();

        $response = $this->deleteJson('/api/v1/tokens/' . $tokenModel->id);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Token deleted successfully.');

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenModel->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_token(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $otherUser->createToken('other-token');

        $otherToken = PersonalAccessToken::query()
            ->where('tokenable_id', $otherUser->id)
            ->where('name', 'other-token')
            ->first();

        $response = $this->deleteJson('/api/v1/tokens/' . $otherToken->id);

        $response->assertStatus(404);
    }

    public function test_guest_cannot_access_tokens_endpoint(): void
    {
        $response = $this->getJson('/api/v1/tokens');

        $response->assertStatus(401);
    }
}