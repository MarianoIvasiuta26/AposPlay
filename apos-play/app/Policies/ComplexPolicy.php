<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Complex;
use App\Models\User;

class ComplexPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(UserRole::SUPERADMIN, UserRole::OWNER);
    }

    public function view(User $user, Complex $complex): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        if ($user->isOwner()) {
            return $complex->owner_id === $user->id;
        }

        if ($user->isStaff()) {
            return $user->complexesStaff()->where('complexes.id', $complex->id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::SUPERADMIN, UserRole::OWNER);
    }

    public function update(User $user, Complex $complex): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        return $user->isOwner() && $complex->owner_id === $user->id;
    }

    public function delete(User $user, Complex $complex): bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        return $user->isOwner() && $complex->owner_id === $user->id;
    }
}
