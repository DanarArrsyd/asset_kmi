<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class StockOpnamePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin, UserRole::Auditor], true);
    }
}
