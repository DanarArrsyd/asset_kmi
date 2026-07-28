<?php

namespace App\Policies;

use App\Models\User;

class StockOpnamePolicy
{
    /**
     * The list itself is open to everyone; StockOpnameController::index scopes
     * the rows to the caller's department for the two scoped roles.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /*
     * Creating a stock take lives on AssetPolicy::recordStockOpname instead of
     * here. It has to be answered against a specific asset — this policy only
     * ever saw the role, so nothing checked whether the actor could reach the
     * asset being counted. That was harmless only because every role allowed to
     * count could already view every asset.
     */
}
