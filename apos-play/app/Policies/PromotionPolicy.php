<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Promotion;
use App\Models\User;

class PromotionPolicy
{
    private function isAdmin(User $user): bool
    {
        return $user->hasRole(UserRole::SUPERADMIN, UserRole::OWNER, UserRole::STAFF)
            || $user->courtsXAdmin()->exists();
    }

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, Promotion $promotion): bool
    {
        return $this->isAdmin($user);
    }

    public function delete(User $user, Promotion $promotion): bool
    {
        return $this->isAdmin($user);
    }
}
