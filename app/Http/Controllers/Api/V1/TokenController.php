<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Auth\Actions\CreateTokenAction;
use App\Domain\Auth\Actions\DeleteTokenAction;
use App\Domain\Auth\Actions\ListTokensAction;
use App\Domain\Auth\Data\CreateTokenData;
use App\Domain\Auth\Resources\PersonalAccessTokenResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\CreateTokenRequest;
use App\Shared\Concerns\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    use ApiResponder;

    public function __construct(
        protected ListTokensAction $listTokensAction,
        protected CreateTokenAction $createTokenAction,
        protected DeleteTokenAction $deleteTokenAction,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $tokens = $this->listTokensAction->execute($request->user());

        return $this->success([
            'tokens' => PersonalAccessTokenResource::collection($tokens),
        ], 'Tokens fetched successfully.');
    }

    public function store(CreateTokenRequest $request): JsonResponse
    {
        $result = $this->createTokenAction->execute(
            $request->user(),
            CreateTokenData::fromArray($request->validated())
        );

        return $this->success([
            'token' => new PersonalAccessTokenResource($result['token']),
            'plain_text_token' => $result['plain_text_token'],
            'type' => 'Bearer',
        ], 'Token created successfully.', 201);
    }

    public function destroy(Request $request, int $tokenId): JsonResponse
    {
        $this->deleteTokenAction->execute($request->user(), $tokenId);

        return $this->success(null, 'Token deleted successfully.');
    }
}