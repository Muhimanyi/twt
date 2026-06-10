<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class PermissionController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        if (! $user->hasPermission('users.manage-permissions')) {
            abort(403);
        }

        $users = User::with('province', 'provinceAssignments.province')
            ->orderBy('name')
            ->get()
            ->map(function ($u) {
                $globalKeys = DB::table('permission_user')
                    ->where('user_id', $u->id)
                    ->whereNull('province_id')
                    ->join('permissions', 'permission_user.permission_id', '=', 'permissions.id')
                    ->pluck('permissions.key');

                $byProvince = [];
                foreach ($u->provinceAssignments as $a) {
                    $byProvince[$a->province_id] = DB::table('permission_user')
                        ->where('user_id', $u->id)
                        ->where('province_id', $a->province_id)
                        ->join('permissions', 'permission_user.permission_id', '=', 'permissions.id')
                        ->pluck('permissions.key');
                }

                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $u->role,
                    'province' => $u->province?->name,
                    'province_assignments' => $u->provinceAssignments->map(fn ($a) => [
                        'province_id' => $a->province_id,
                        'province_name' => $a->province?->name,
                        'role' => $a->role,
                    ]),
                    'permission_keys' => $globalKeys,
                    'permissions_by_province' => $byProvince,
                ];
            });

        $permissions = Permission::orderBy('group')->orderBy('id')->get();
        $permissionsGrouped = $permissions->groupBy('group');
        // Super admin voit tout, sinon filtrer par provinces assignées
        if (! $user->isSuperadmin()) {
            $assignedIds = $user->assignedProvinceIds();
            $provinces = Province::whereIn('id', $assignedIds)->orderBy('name')->get();
            $users = $users->filter(function ($u) use ($assignedIds) {
                $uProvinceIds = collect($u['province_assignments'])->pluck('province_id')->toArray();

                return count(array_intersect($uProvinceIds, $assignedIds)) > 0;
            })->values();
        } else {
            $provinces = Province::orderBy('name')->get();
        }

        return Inertia::render('users/Permissions', [
            'users' => $users,
            'permissionsGrouped' => $permissionsGrouped,
            'provinces' => $provinces,
            'roleOptions' => [
                ['value' => 'superadmin', 'label' => 'Super Admin'],
                ['value' => 'admin', 'label' => 'Admin'],
                ['value' => 'admin-region', 'label' => 'Admin Région'],
                ['value' => 'manager', 'label' => 'Manager'],
                ['value' => 'secretaire', 'label' => 'Secrétaire'],
                ['value' => 'user', 'label' => 'Utilisateur'],
            ],
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $authUser = $request->user();

        if (! $authUser->hasPermission('users.manage-permissions')) {
            abort(403);
        }

        // Seul un superadmin peut modifier les permissions d'un autre superadmin
        if ($user->isSuperadmin() && ! $authUser->isSuperadmin()) {
            abort(403, 'Vous ne pouvez pas modifier les permissions d\'un Super Admin.');
        }

        // Un admin non-superadmin ne peut modifier que les users de ses provinces
        if (! $authUser->isSuperadmin()) {
            $userProvinceIds = $user->assignedProvinceIds();
            $authProvinceIds = $authUser->assignedProvinceIds();
            if (count(array_intersect($userProvinceIds, $authProvinceIds)) === 0) {
                abort(403, 'Vous ne pouvez pas modifier les permissions d\'un utilisateur en dehors de vos provinces.');
            }
        }

        $validated = $request->validate([
            'permission_keys' => ['array'],
            'permission_keys.*' => ['string', 'exists:permissions,key'],
            'province_permissions' => ['nullable', 'array'],
            'province_permissions.*.province_id' => ['required', 'exists:provinces,id'],
            'province_permissions.*.keys' => ['array'],
            'province_permissions.*.keys.*' => ['string', 'exists:permissions,key'],
        ]);

        DB::transaction(function () use ($user, $validated) {
            // Global permissions (province_id = null)
            $globalKeys = $validated['permission_keys'] ?? [];
            $globalIds = Permission::whereIn('key', $globalKeys)->pluck('id');

            DB::table('permission_user')
                ->where('user_id', $user->id)
                ->whereNull('province_id')
                ->delete();

            foreach ($globalIds as $permId) {
                DB::table('permission_user')->insert([
                    'permission_id' => $permId,
                    'user_id' => $user->id,
                    'province_id' => null,
                ]);
            }

            // Province-scoped permissions
            foreach ($validated['province_permissions'] ?? [] as $provPerm) {
                $provinceId = $provPerm['province_id'];
                $provIds = Permission::whereIn('key', $provPerm['keys'] ?? [])->pluck('id');

                DB::table('permission_user')
                    ->where('user_id', $user->id)
                    ->where('province_id', $provinceId)
                    ->delete();

                foreach ($provIds as $permId) {
                    DB::table('permission_user')->insert([
                        'permission_id' => $permId,
                        'user_id' => $user->id,
                        'province_id' => $provinceId,
                    ]);
                }
            }
        });

        $user->flushPermissionCache();

        return Redirect::route('permissions.index')
            ->with('success', "Permissions mises à jour pour {$user->name}.");
    }
}
