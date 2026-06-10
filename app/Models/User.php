<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'role', 'province_id', 'phone', 'photo'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'role' => 'array',
        ];
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function provinceAssignments(): HasMany
    {
        return $this->hasMany(ProvinceAssignment::class);
    }

    public function assignedProvinces(): BelongsToMany
    {
        return $this->belongsToMany(Province::class, 'province_assignments')
            ->withPivot('role', 'is_primary')
            ->withTimestamps();
    }

    public function primaryProvinceAssignment(): ?ProvinceAssignment
    {
        return $this->provinceAssignments()->where('is_primary', true)->first();
    }

    public function assignedProvinceIds(): array
    {
        $ids = $this->provinceAssignments()->pluck('province_id')->toArray();

        if (empty($ids) && $this->province_id) {
            return [$this->province_id];
        }

        return $ids;
    }

    public function isSuperadmin(): bool
    {
        $roles = $this->role;
        if (is_null($roles)) {
            return true;
        }
        if (is_string($roles)) {
            $roles = [$roles];
        }

        return in_array('superadmin', $roles);
    }

    public function roleForProvince(?int $provinceId): ?string
    {
        if (is_null($provinceId)) {
            return null;
        }

        $assignment = $this->provinceAssignments()
            ->where('province_id', $provinceId)
            ->first();

        return $assignment?->role;
    }

    public function canAccessProvince($provinceId): bool
    {
        if ($this->isSuperadmin()) {
            return true;
        }

        $assignedIds = $this->assignedProvinceIds();

        if (empty($assignedIds)) {
            return true;
        }

        return in_array($provinceId, $assignedIds);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)
            ->withPivot('province_id')
            ->withTimestamps();
    }

    public function permissionsForProvince(?int $provinceId): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)
            ->withPivot('province_id')
            ->wherePivot('province_id', $provinceId)
            ->withTimestamps();
    }

    public function hasPermission(string $key, ?int $provinceId = null): bool
    {
        if ($this->isSuperadmin()) {
            return true;
        }

        $cacheKey = "user.{$this->id}.permissions";
        $userPermissionKeys = Cache::remember($cacheKey, 3600, function () {
            return $this->permissions()->wherePivotNull('province_id')->pluck('key')->toArray();
        });

        if (in_array($key, $userPermissionKeys, true)) {
            return true;
        }

        // Check all province-scoped permissions when no specific province is given
        $provinceIds = $provinceId ? [$provinceId] : $this->assignedProvinceIds();
        foreach ($provinceIds as $pid) {
            $cacheKeyProvince = "user.{$this->id}.permissions.province.{$pid}";
            $provincePermissionKeys = Cache::remember($cacheKeyProvince, 3600, function () use ($pid) {
                return $this->permissionsForProvince($pid)->pluck('key')->toArray();
            });

            if (in_array($key, $provincePermissionKeys, true)) {
                return true;
            }
        }

        return false;
    }

    public function flushPermissionCache(): void
    {
        Cache::forget("user.{$this->id}.permissions");
        Cache::forget("user.{$this->id}.all_permissions");

        $assignedIds = $this->assignedProvinceIds();
        foreach ($assignedIds as $provinceId) {
            Cache::forget("user.{$this->id}.permissions.province.{$provinceId}");
        }
    }

    public function allPermissionKeys(): array
    {
        if ($this->isSuperadmin()) {
            return Permission::pluck('key')->toArray();
        }

        $keys = $this->permissions()->wherePivotNull('province_id')->pluck('key')->toArray();

        $assignedIds = $this->assignedProvinceIds();
        foreach ($assignedIds as $provinceId) {
            $provinceKeys = $this->permissionsForProvince($provinceId)->pluck('key')->toArray();
            $keys = array_merge($keys, $provinceKeys);
        }

        return array_values(array_unique($keys));
    }
}
