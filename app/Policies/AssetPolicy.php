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

    /**
     * Editing the asset record itself — name, department, dates, everything.
     *
     * Auditors are denied on purpose. They are not locked out of the asset
     * entirely: recordStockOpname() below is the narrow write they do get.
     */
    public function update(User $user, Asset $asset): bool
    {
        return match ($user->role) {
            UserRole::SuperAdmin, UserRole::Admin => true,
            UserRole::Department => $asset->department_id === $user->department_id,
            UserRole::Auditor, UserRole::User => false,
        };
    }

    /**
     * Recording a stock take, which writes condition and status back onto the
     * asset — an auditor marking something Missing during a count is the whole
     * point of the module.
     *
     * This is deliberately a separate ability from update(). Folding auditors
     * into update() would hand them the full edit form as well; leaving the
     * write implicit inside StockOpnameService, as it was until now, meant the
     * permission existed but nothing named or enforced it.
     */
    public function recordStockOpname(User $user, Asset $asset): bool
    {
        return match ($user->role) {
            UserRole::SuperAdmin, UserRole::Admin, UserRole::Auditor => true,
            UserRole::Department, UserRole::User => false,
        };
    }

    /**
     * Moving an asset between departments. Separate from update() because a
     * department user may edit their own assets but not push one out of their
     * own scope — CLAUDE.md has them *requesting* a transfer, and Asset
     * Transfer is its own planned module with its own approval flow.
     */
    public function reassignDepartment(User $user, Asset $asset): bool
    {
        return in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true);
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
