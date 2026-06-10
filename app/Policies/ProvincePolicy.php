<?php

namespace App\Policies;

use App\Models\Province;
use App\Models\User;

class ProvincePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperadmin()) {
            return true;
        }

        $assignedIds = $user->assignedProvinceIds();

        if (empty($assignedIds)) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Province $province): bool
    {
        $assignedIds = $user->assignedProvinceIds();

        if (empty($assignedIds)) {
            return true;
        }

        return in_array($province->id, $assignedIds);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Province $province): bool
    {
        $assignedIds = $user->assignedProvinceIds();

        if (empty($assignedIds)) {
            return true;
        }

        return in_array($province->id, $assignedIds);
    }

    public function delete(User $user, Province $province): bool
    {
        return false;
    }
}
