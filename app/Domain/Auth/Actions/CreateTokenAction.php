<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Data\CreateTokenData;
use App\Domain\Auth\Services\TokenService;
use App\Models\User;

class CreateTokenAction
{
    public function __construct(
        protected TokenService $tokenService
    ) {
    }

    public function execute(User $user, CreateTokenData $data): array
    {
        $plainTextToken = $this->tokenService->createAccessToken(
            user: $user,
            name: $data->name,
            abilities: $data->abilities
        );

        $tokenModel = $user->tokens()->latest()->first();

        return [
            'plain_text_token' => $plainTextToken,
            'token' => $tokenModel,
        ];
    }
}