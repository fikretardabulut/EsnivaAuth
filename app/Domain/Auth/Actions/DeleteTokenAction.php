<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Services\TokenService;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteTokenAction
{
    public function __construct(
        protected TokenService $tokenService
    ) {
    }

    public function execute(User $user, int $tokenId): void
    {
        $token = $this->tokenService->findUserToken($user, $tokenId);

        if (! $token) {
            throw new NotFoundHttpException('Token not found.');
        }

        $deleted = $this->tokenService->deleteUserToken($user, $tokenId);

        if (! $deleted) {
            throw new NotFoundHttpException('Token not found.');
        }

        Log::info('Personal access token deleted.', [
            'user_id' => $user->id,
            'token_id' => $tokenId,
            'token_name' => $token->name,
            'ip' => request()->ip(),
        ]);
    }
}