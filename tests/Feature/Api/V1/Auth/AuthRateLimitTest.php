<?php

namespace Tests\Feature\Api\V1\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_rate_limited_after_too_many_attempts(): void
    {
        User::factory()->create([
            'email' => 'fikret@example.com',
            'password' => 'Esniva123!',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'fikret@example.com',
                'password' => 'WrongPassword123!',
            ]);
        }

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'fikret@example.com',
            'password' => 'WrongPassword123!',
        ]);

        $response
            ->assertStatus(429)
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Too many login attempts. Please try again later.');
    }
}