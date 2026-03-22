<?php

namespace App\Domain\Auth\Actions;

use App\Domain\Auth\Data\LoginData;
use App\Domain\Auth\Exceptions\InvalidCredentialsException;
use App\Domain\Auth\Services\TokenService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginUserAction
{
    public function __construct(
        protected TokenService $tokenService
    ) {
    }

    /**
     * @throws InvalidCredentialsException
     */
    public function execute(LoginData $data): array
    {
        $user = User::query()
            ->where('email', $data->email)
            ->first();

        if (! $user || ! Hash::check($data->password, $user->password)) {
            Log::warning('Failed login attempt detected.', [
                'email' => $data->email,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            throw new InvalidCredentialsException();
        }

        $token = $this->tokenService->createAccessToken($user, 'default');

        Log::info('User logged in successfully.', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
        ]);

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}