<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Auth\Actions\GetCurrentUserAction;
use App\Domain\Auth\Actions\LoginUserAction;
use App\Domain\Auth\Actions\LogoutUserAction;
use App\Domain\Auth\Actions\RegisterUserAction;
use App\Domain\Auth\Data\LoginData;
use App\Domain\Auth\Data\RegisterData;
use App\Domain\Auth\Resources\AuthTokenResource;
use App\Domain\Auth\Resources\AuthUserResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Shared\Concerns\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ApiResponder;

    public function __construct(
        protected RegisterUserAction $registerUserAction,
        protected LoginUserAction $loginUserAction,
        protected GetCurrentUserAction $getCurrentUserAction,
        protected LogoutUserAction $logoutUserAction,
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->registerUserAction->execute(
            RegisterData::fromArray($request->validated())
        );

        return $this->success([
            'user' => new AuthUserResource($result['user']),
            'auth' => new AuthTokenResource($result['token']),
        ], 'User registered successfully.', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->loginUserAction->execute(
            LoginData::fromArray($request->validated())
        );

        return $this->success([
            'user' => new AuthUserResource($result['user']),
            'auth' => new AuthTokenResource($result['token']),
        ], 'Login successful.');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->getCurrentUserAction->execute($request->user());

        return $this->success([
            'user' => new AuthUserResource($user),
        ], 'Authenticated user fetched successfully.');
    }

    public function logout(Request $request): JsonResponse
    {
        $this->logoutUserAction->execute($request->user());

        return $this->success(null, 'Logged out successfully.');
    }
}