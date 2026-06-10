<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Logique d'accès par province :
 *   - superadmin → accès total
 *   - Aucune assignation province → accès multi-régional
 *   - Sinon → limité aux provinces assignées
 */
trait HasProvinceAccess
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

    protected function belongsToUserProvince(User $user, Model $model): bool
    {
        $assignedIds = $user->assignedProvinceIds();

        if (empty($assignedIds)) {
            return true;
        }

        return in_array($model->province_id, $assignedIds);
    }
}
