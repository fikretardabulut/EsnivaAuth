<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Support\TokenAbility;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

class TokenService
{
    public function createAccessToken(
        User $user,
        string $name = 'default',
        array $abilities = []
    ): string {
        $resolvedAbilities = empty($abilities)
            ? TokenAbility::default()
            : $abilities;

        return $user->createToken($name, $resolvedAbilities)->plainTextToken;
    }

    public function revokeCurrentToken(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function listUserTokens(User $user)
    {
        return $user->tokens()->latest()->get();
    }

    public function deleteUserToken(User $user, int $tokenId): bool
    {
        return (bool) $user->tokens()->where('id', $tokenId)->delete();
    }

    public function findUserToken(User $user, int $tokenId): ?PersonalAccessToken
    {
        return $user->tokens()->where('id', $tokenId)->first();
    }
}