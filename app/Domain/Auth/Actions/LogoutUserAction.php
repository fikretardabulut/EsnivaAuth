<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Services\TokenService;
use App\Models\User;

class LogoutUserAction
{
    public function __construct(
        protected TokenService $tokenService
    ) {
    }

    public function execute(User $user): void
    {
        $this->tokenService->revokeCurrentToken($user);
    }
}