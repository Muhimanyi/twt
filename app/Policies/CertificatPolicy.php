<?php

namespace App\Policies;

use App\Models\Certificat;
use App\Models\User;

class CertificatPolicy
{
    use HasProvinceAccess;

    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Certificat n'a pas de province_id direct — il passe par l'engin.
     */
    public function view(User $user, Certificat $certificat): bool
    {
        if (is_null($user->province_id)) {
            return false;
        }

        $engin = $certificat->engin;

        return $engin && $engin->province_id === $user->province_id;
    }

    public function create(User $user): bool
    {
        return ! is_null($user->province_id);
    }

    public function update(User $user, Certificat $certificat): bool
    {
        return $this->view($user, $certificat);
    }

    public function delete(User $user, Certificat $certificat): bool
    {
        return $this->view($user, $certificat);
    }
}
