<?php

namespace App\Policies;

use App\Models\Conducteur;
use App\Models\User;

class ConducteurPolicy
{
    use HasProvinceAccess;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Conducteur $conducteur): bool
    {
        return $this->belongsToUserProvince($user, $conducteur);
    }

    public function create(User $user): bool
    {
        return ! is_null($user->province_id);
    }

    public function update(User $user, Conducteur $conducteur): bool
    {
        return $this->belongsToUserProvince($user, $conducteur);
    }

    public function delete(User $user, Conducteur $conducteur): bool
    {
        return $this->belongsToUserProvince($user, $conducteur);
    }
}
