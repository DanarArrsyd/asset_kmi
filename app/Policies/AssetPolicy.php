<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Asset $asset): bool
    {
        return match ($user->role) {
            UserRole::SuperAdmin, UserRole::Admin, UserRole::Auditor => true,
            UserRole::Department, UserRole::User => $asset->department_id === $user->department_id,
        };
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }

    public function update(User $user, Asset $asset): bool
    {
        return match ($user->role) {
            UserRole::SuperAdmin, UserRole::Admin => true,
            UserRole::Department => $asset->department_id === $user->department_id,
            UserRole::Auditor, UserRole::User => false,
        };
    }

    public function delete(User $user, Asset $asset): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }

    public function restore(User $user, Asset $asset): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }

    public function forceDelete(User $user, Asset $asset): bool
    {
        return $user->role === UserRole::SuperAdmin;
    }
}
