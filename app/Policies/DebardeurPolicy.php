<?php

namespace App\Policies;

use App\Models\Debardeur;
use App\Models\User;

class DebardeurPolicy
{
    use HasProvinceAccess;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Debardeur $debardeur): bool
    {
        return $this->belongsToUserProvince($user, $debardeur);
    }

    public function create(User $user): bool
    {
        return ! is_null($user->province_id);
    }

    public function update(User $user, Debardeur $debardeur): bool
    {
        return $this->belongsToUserProvince($user, $debardeur);
    }

    public function delete(User $user, Debardeur $debardeur): bool
    {
        return $this->belongsToUserProvince($user, $debardeur);
    }
}
