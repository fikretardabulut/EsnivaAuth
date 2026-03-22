<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Data\RegisterData;
use App\Domain\Auth\Services\TokenService;
use App\Models\User;

class RegisterUserAction
{
    public function __construct(
        protected TokenService $tokenService
    ) {
    }

    public function execute(RegisterData $data): array
    {
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);

        $token = $this->tokenService->createAccessToken($user);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}