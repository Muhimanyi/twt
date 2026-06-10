<?php

use App\Models\Permission;
use App\Models\Province;
use App\Models\ProvinceAssignment;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    Cache::flush();
    Province::factory()->create(['name' => 'Test Province']);
    $this->seed(PermissionSeeder::class);
});

test('superadmin has all permissions', function () {
    $user = User::factory()->create(['role' => 'superadmin']);

    expect($user->isSuperadmin())->toBeTrue();
    expect($user->hasPermission('any.random.key'))->toBeTrue();
    expect($user->hasPermission('conducteurs.view'))->toBeTrue();
    expect($user->hasPermission('users.manage-permissions'))->toBeTrue();
    expect($user->allPermissionKeys())->toHaveCount(Permission::count());
});

test('user with direct global permission has access', function () {
    $user = User::factory()->create(['role' => 'user']);
    $perm = Permission::where('key', 'conducteurs.view')->first();
    DB::table('permission_user')->insert([
        'permission_id' => $perm->id,
        'user_id' => $user->id,
        'province_id' => null,
    ]);

    expect($user->hasPermission('conducteurs.view'))->toBeTrue();
    expect($user->hasPermission('engins.view'))->toBeFalse();
});

test('user with province-scoped permission has access', function () {
    $province = Province::first();
    $user = User::factory()->create(['role' => 'user']);
    ProvinceAssignment::create([
        'user_id' => $user->id,
        'province_id' => $province->id,
        'role' => 'user',
    ]);
    $perm = Permission::where('key', 'conducteurs.create')->first();
    DB::table('permission_user')->insert([
        'permission_id' => $perm->id,
        'user_id' => $user->id,
        'province_id' => $province->id,
    ]);

    expect($user->hasPermission('conducteurs.create', $province->id))->toBeTrue();
    expect($user->hasPermission('conducteurs.create'))->toBeTrue();
});

test('user without any permissions has no access', function () {
    $user = User::factory()->create(['role' => 'user']);

    expect($user->hasPermission('conducteurs.view'))->toBeFalse();
    expect($user->hasPermission('dashboard.view'))->toBeFalse();
    expect($user->hasPermission('anything'))->toBeFalse();
});

test('role-based permissions are not automatic without explicit permission_user entries', function () {
    $user = User::factory()->create(['role' => 'secretaire']);

    expect($user->hasPermission('dashboard.view'))->toBeFalse();
    expect($user->hasPermission('conducteurs.view'))->toBeFalse();
    expect($user->hasPermission('conducteurs.create'))->toBeFalse();
    expect($user->hasPermission('taxes.view'))->toBeFalse();
});

test('role-based permissions via province assignments are not automatic', function () {
    $province = Province::first();
    $user = User::factory()->create(['role' => 'user']);
    ProvinceAssignment::create([
        'user_id' => $user->id,
        'province_id' => $province->id,
        'role' => 'manager',
    ]);

    expect($user->hasPermission('dashboard.view'))->toBeFalse();
    expect($user->hasPermission('taxes.create'))->toBeFalse();
});

test('user role without province assignment gets no automatic permissions', function () {
    $user = User::factory()->create(['role' => 'manager']);

    expect($user->hasPermission('taxes.create'))->toBeFalse();
    expect($user->hasPermission('taxes.delete'))->toBeFalse();
});

test('assignedProvinceIds returns correct province ids', function () {
    $province1 = Province::first();
    $province2 = Province::factory()->create(['name' => 'Province 2']);
    $user = User::factory()->create(['role' => 'user']);

    ProvinceAssignment::create(['user_id' => $user->id, 'province_id' => $province1->id, 'role' => 'user']);
    ProvinceAssignment::create(['user_id' => $user->id, 'province_id' => $province2->id, 'role' => 'admin']);

    $ids = $user->assignedProvinceIds();
    expect($ids)->toContain($province1->id);
    expect($ids)->toContain($province2->id);
    expect($ids)->toHaveCount(2);
});

test('isSuperadmin detects superadmin role', function () {
    $super = User::factory()->create(['role' => 'superadmin']);
    $admin = User::factory()->create(['role' => 'admin']);
    $user = User::factory()->create(['role' => 'user']);
    $multiple = User::factory()->create(['role' => ['superadmin', 'user']]);

    expect($super->isSuperadmin())->toBeTrue();
    expect($admin->isSuperadmin())->toBeFalse();
    expect($user->isSuperadmin())->toBeFalse();
    expect($multiple->isSuperadmin())->toBeTrue();
});

test('roleForProvince returns correct role', function () {
    $province = Province::first();
    $user = User::factory()->create(['role' => 'user']);
    ProvinceAssignment::create(['user_id' => $user->id, 'province_id' => $province->id, 'role' => 'admin']);

    expect($user->roleForProvince($province->id))->toBe('admin');
    expect($user->roleForProvince(999))->toBeNull();
});

test('permission cache is flushed correctly', function () {
    $user = User::factory()->create(['role' => 'secretaire']);
    Cache::shouldReceive('forget')
        ->withSomeOfArgs("user.{$user->id}.permissions")
        ->andReturnTrue();
    Cache::shouldReceive('forget')
        ->withSomeOfArgs("user.{$user->id}.all_permissions")
        ->andReturnTrue();

    $user->flushPermissionCache();
});
