<?php

namespace App\Domain\Organizations\Actions;

use App\Models\Organization;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShowOrganizationAction
{
    public function execute(User $user, int $organizationId): Organization
    {
        $organization = $user->organizations()
            ->where('organizations.id', $organizationId)
            ->first();

        if (! $organization) {
            throw new NotFoundHttpException('Organization not found.');
        }

        return $organization;
    }
}