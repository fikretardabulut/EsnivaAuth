<?php

namespace App\Domain\Auth\Actions;

use App\Models\User;

class GetCurrentUserAction
{
    public function execute(User $user): User
    {
        return $user;
    }
}