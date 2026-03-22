<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Services\TokenService;
use App\Models\User;
use Illuminate\Support\Collection;

class ListTokensAction
{
    public function __construct(
        protected TokenService $tokenService
    ) {
    }

    public function execute(User $user): Collection
    {
        return $this->tokenService->listUserTokens($user);
    }
}