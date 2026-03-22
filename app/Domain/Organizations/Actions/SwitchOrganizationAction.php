<?php

namespace App\Domain\Organizations\Actions;

use App\Domain\Organizations\Data\SwitchOrganizationData;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SwitchOrganizationAction
{
    public function execute(User $user, SwitchOrganizationData $data): User
    {
        $organization = $user->organizations()
            ->where('organizations.id', $data->organizationId)
            ->first();

        if (! $organization) {
            throw new NotFoundHttpException('Organization not found.');
        }

        $user->update([
            'current_organization_id' => $organization->id,
        ]);

        return $user->fresh(['currentOrganization']);
    }
}