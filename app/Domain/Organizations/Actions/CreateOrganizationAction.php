<?php

namespace App\Domain\Organizations\Actions;

use App\Domain\Organizations\Data\CreateOrganizationData;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Str;

class CreateOrganizationAction
{
    public function execute(User $user, CreateOrganizationData $data): Organization
    {
        $baseSlug = Str::slug($data->name);
        $slug = $baseSlug;
        $counter = 1;

        while (Organization::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $organization = Organization::create([
            'name' => $data->name,
            'slug' => $slug,
            'owner_id' => $user->id,
        ]);

        $organization->users()->attach($user->id, [
            'role' => 'owner',
        ]);

        $user->update([
            'current_organization_id' => $organization->id,
        ]);

        return $organization->fresh();
    }
}