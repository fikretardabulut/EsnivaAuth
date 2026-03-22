<?php

namespace App\Domain\Organizations\Actions;

use App\Models\User;
use Illuminate\Support\Collection;

class ListOrganizationsAction
{
    public function execute(User $user): Collection
    {
        return $user->organizations()->latest()->get();
    }
}