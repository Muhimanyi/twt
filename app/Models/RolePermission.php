<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class RolePermission extends Model
{
    public $timestamps = false;

    protected $fillable = ['role', 'permission_id'];

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }

    public static function roleHasPermission(string $role, string $key): bool
    {
        return Cache::remember("role.{$role}.permission.{$key}", 3600, function () use ($role, $key) {
            return static::where('role', $role)
                ->whereHas('permission', fn ($q) => $q->where('key', $key))
                ->exists();
        });
    }

    public static function flushRoleCache(?string $role = null): void
    {
        if ($role) {
            Cache::forget("role.{$role}.*");
        }
    }

    public function getTable()
    {
        return 'role_permission';
    }
}
