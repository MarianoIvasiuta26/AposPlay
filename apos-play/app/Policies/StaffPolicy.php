<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class StaffPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::SUPERADMIN, UserRole::OWNER);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::SUPERADMIN, UserRole::OWNER);
    }

    public function delete(User $user, User $staffUser): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        if (!$user->isOwner()) {
            return false;
        }

        $ownerComplexIds = $user->complexesOwned()->pluck('id');

        return $staffUser->complexesStaff()
            ->whereIn('complexes.id', $ownerComplexIds)
            ->exists();
    }
}
