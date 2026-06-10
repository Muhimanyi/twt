<?php

namespace App\Policies;

use App\Policies\HasProvinceAccess;
use App\Models\Annonce;
use App\Models\User;

class AnnoncePolicy
{
    use HasProvinceAccess;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Annonce $annonce): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('annonces.create');
    }

    public function update(User $user, Annonce $annonce): bool
    {
        if (!$user->hasPermission('annonces.edit')) {
            return false;
        }

        if ($annonce->province_id && !$user->isSuperadmin()) {
            return in_array($annonce->province_id, $user->assignedProvinceIds());
        }

        return true;
    }

    public function delete(User $user, Annonce $annonce): bool
    {
        if (!$user->hasPermission('annonces.delete')) {
            return false;
        }

        if ($annonce->province_id && !$user->isSuperadmin()) {
            return in_array($annonce->province_id, $user->assignedProvinceIds());
        }

        return true;
    }
}
