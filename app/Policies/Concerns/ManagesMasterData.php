<?php

namespace App\Policies\Concerns;

use App\Enums\UserRole;
use App\Models\User;

trait ManagesMasterData
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin, UserRole::Auditor], true);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
    }

    public function update(User $user): bool
    {
        return $this->create($user);
    }

    public function delete(User $user): bool
    {
        return $this->create($user);
    }
}
