<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\CourtBlock;
use App\Models\User;

class CourtBlockPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::SUPERADMIN, UserRole::OWNER);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::SUPERADMIN, UserRole::OWNER);
    }

    public function delete(User $user, CourtBlock $block): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        if ($user->isOwner()) {
            $court = $block->court;
            return $court->complex && $court->complex->owner_id === $user->id;
        }

        return false;
    }
}
