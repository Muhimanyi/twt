<?php

namespace App\Policies;

use App\Models\Permis;
use App\Models\User;

class PermisPolicy
{
    use HasProvinceAccess;

    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Permis n'a pas de province_id direct — il passe par le conducteur.
     */
    public function view(User $user, Permis $permis): bool
    {
        if (is_null($user->province_id)) {
            return false;
        }

        $conducteur = $permis->conducteur;

        return $conducteur && $conducteur->province_id === $user->province_id;
    }

    public function create(User $user): bool
    {
        return ! is_null($user->province_id);
    }

    public function update(User $user, Permis $permis): bool
    {
        return $this->view($user, $permis);
    }

    public function delete(User $user, Permis $permis): bool
    {
        return $this->view($user, $permis);
    }
}
