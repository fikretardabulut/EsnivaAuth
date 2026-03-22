<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Organizations\Actions\CreateOrganizationAction;
use App\Domain\Organizations\Actions\ListOrganizationsAction;
use App\Domain\Organizations\Actions\ShowOrganizationAction;
use App\Domain\Organizations\Actions\SwitchOrganizationAction;
use App\Domain\Organizations\Data\CreateOrganizationData;
use App\Domain\Organizations\Data\SwitchOrganizationData;
use App\Domain\Organizations\Resources\OrganizationResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Organizations\CreateOrganizationRequest;
use App\Http\Requests\Api\V1\Organizations\SwitchOrganizationRequest;
use App\Shared\Concerns\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    use ApiResponder;

    public function __construct(
        protected CreateOrganizationAction $createOrganizationAction,
        protected ListOrganizationsAction $listOrganizationsAction,
        protected ShowOrganizationAction $showOrganizationAction,
        protected SwitchOrganizationAction $switchOrganizationAction,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $organizations = $this->listOrganizationsAction->execute($request->user());

        return $this->success([
            'organizations' => OrganizationResource::collection($organizations),
            'current_organization_id' => $request->user()->current_organization_id,
        ], 'Organizations fetched successfully.');
    }

    public function store(CreateOrganizationRequest $request): JsonResponse
    {
        $organization = $this->createOrganizationAction->execute(
            $request->user(),
            CreateOrganizationData::fromArray($request->validated())
        );

        return $this->success([
            'organization' => new OrganizationResource($organization),
        ], 'Organization created successfully.', 201);
    }

    public function show(Request $request, int $organization): JsonResponse
    {
        $organizationModel = $this->showOrganizationAction->execute(
            $request->user(),
            $organization
        );

        return $this->success([
            'organization' => new OrganizationResource($organizationModel),
        ], 'Organization fetched successfully.');
    }

    public function switch(SwitchOrganizationRequest $request): JsonResponse
    {
        $user = $this->switchOrganizationAction->execute(
            $request->user(),
            SwitchOrganizationData::fromArray($request->validated())
        );

        return $this->success([
            'current_organization_id' => $user->current_organization_id,
        ], 'Organization switched successfully.');
    }
}