<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Complex;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleService
{
    public function createOwner(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => UserRole::OWNER,
            ]);

            if (!empty($data['complex_name'])) {
                Complex::create([
                    'name' => $data['complex_name'],
                    'owner_id' => $user->id,
                    'address' => $data['complex_address'] ?? null,
                ]);
            }

            return $user;
        });
    }

    public function deactivateOwner(User $owner): void
    {
        $owner->update(['is_active' => false]);
    }

    public function reactivateOwner(User $owner): void
    {
        $owner->update(['is_active' => true]);
    }

    public function createStaff(array $data, array $complexIds = []): User
    {
        return DB::transaction(function () use ($data, $complexIds) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => UserRole::STAFF,
            ]);

            if (!empty($complexIds)) {
                $user->complexesStaff()->attach($complexIds);
            }

            return $user;
        });
    }

    public function assignStaffToComplex(User $staff, Complex $complex): void
    {
        $staff->complexesStaff()->syncWithoutDetaching([$complex->id]);
    }

    public function removeStaffFromComplex(User $staff, Complex $complex): void
    {
        $staff->complexesStaff()->detach($complex->id);
    }

    public function getComplexesForUser(User $user): Collection
    {
        if ($user->isSuperadmin()) {
            return Complex::with('owner')->get();
        }

        if ($user->isOwner()) {
            return $user->complexesOwned()->get();
        }

        if ($user->isStaff()) {
            return $user->complexesStaff()->get();
        }

        return new Collection();
    }
}
