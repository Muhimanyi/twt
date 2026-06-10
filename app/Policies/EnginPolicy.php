<?php

namespace App\Policies;

use App\Models\Engin;
use App\Models\User;

class EnginPolicy
{
    use HasProvinceAccess;

    public function viewAny(User $user): bool
    {
        return true; // Filtré dans le controller selon la province
    }

    public function view(User $user, Engin $engin): bool
    {
        return $this->belongsToUserProvince($user, $engin);
    }

    public function create(User $user): bool
    {
        return ! is_null($user->province_id);
    }

    public function update(User $user, Engin $engin): bool
    {
        return $this->belongsToUserProvince($user, $engin);
    }

    public function delete(User $user, Engin $engin): bool
    {
        return $this->belongsToUserProvince($user, $engin);
    }
}
