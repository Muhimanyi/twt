<?php

use App\Models\Permission;
use App\Models\Province;
use App\Models\ProvinceAssignment;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    Province::factory()->create(['name' => 'Test Province']);
    // Ensure all tests use verified users
    $this->emailVerified = true;
});

function actingAsWithPermission(string $permissionKey): User
{
    $province = Province::first();
    $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);
    $perm = Permission::where('key', $permissionKey)->first();
    DB::table('permission_user')->insert([
        'permission_id' => $perm->id,
        'user_id' => $user->id,
        'province_id' => null,
    ]);
    test()->actingAs($user);

    return $user;
}

function actingAsSuperAdmin(): User
{
    $user = User::factory()->create(['role' => 'superadmin', 'email_verified_at' => now()]);
    test()->actingAs($user);

    return $user;
}

// --- Middleware: CheckPermission ---

test('guest is redirected to login for protected routes', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
    $this->get(route('conducteurs.index'))->assertRedirect(route('login'));
    $this->get(route('engins.index'))->assertRedirect(route('login'));
});

test('user without permission receives 403 on dashboard', function () {
    User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);
    $this->actingAs(User::first());
    $this->get(route('dashboard'))->assertForbidden();
});

test('user with permission can access dashboard', function () {
    actingAsWithPermission('dashboard.view');
    $this->get(route('dashboard'))->assertOk();
});

test('superadmin can access all protected routes', function () {
    actingAsSuperAdmin();
    $this->get(route('dashboard'))->assertOk();
    $this->get(route('conducteurs.index'))->assertOk();
    $this->get(route('engins.index'))->assertOk();
    $this->get(route('debardeurs.index'))->assertOk();
    $this->get(route('provinces.index'))->assertOk();
    $this->get(route('taxes.index'))->assertOk();
    $this->get(route('paiements.index'))->assertOk();
    $this->get(route('users.index'))->assertOk();
    $this->get(route('permis.index'))->assertOk();
    $this->get(route('certificats.index'))->assertOk();
});

// --- Conducteurs ---

test('conducteurs index requires conducteurs.view', function () {
    actingAsWithPermission('dashboard.view');
    $this->get(route('conducteurs.index'))->assertForbidden();
});

test('conducteurs index is accessible with conducteurs.view', function () {
    actingAsWithPermission('conducteurs.view');
    $this->get(route('conducteurs.index'))->assertOk();
});

test('conducteurs store requires conducteurs.create', function () {
    actingAsWithPermission('conducteurs.view');
    $this->post(route('conducteurs.store'))->assertForbidden();
});

// --- Engins ---

test('engins index requires engins.view', function () {
    actingAsWithPermission('dashboard.view');
    $this->get(route('engins.index'))->assertForbidden();
});

test('engins index is accessible with engins.view', function () {
    actingAsWithPermission('engins.view');
    $this->get(route('engins.index'))->assertOk();
});

test('engins store requires engins.create', function () {
    actingAsWithPermission('engins.view');
    $this->post(route('engins.store'))->assertForbidden();
});

// --- Debardeurs ---

test('debardeurs index requires debardeurs.view', function () {
    actingAsWithPermission('dashboard.view');
    $this->get(route('debardeurs.index'))->assertForbidden();
});

test('debardeurs index is accessible with debardeurs.view', function () {
    actingAsWithPermission('debardeurs.view');
    $this->get(route('debardeurs.index'))->assertOk();
});

// --- Provinces ---

test('provinces index requires provinces.view', function () {
    actingAsWithPermission('dashboard.view');
    $this->get(route('provinces.index'))->assertForbidden();
});

test('provinces index is accessible with provinces.view', function () {
    actingAsWithPermission('provinces.view');
    $this->get(route('provinces.index'))->assertOk();
});

// --- Taxes ---

test('taxes index requires taxes.view', function () {
    actingAsWithPermission('dashboard.view');
    $this->get(route('taxes.index'))->assertForbidden();
});

test('taxes index is accessible with taxes.view', function () {
    actingAsWithPermission('taxes.view');
    $this->get(route('taxes.index'))->assertOk();
});

// --- Paiements ---

test('paiements index requires paiements.view', function () {
    actingAsWithPermission('dashboard.view');
    $this->get(route('paiements.index'))->assertForbidden();
});

test('paiements index is accessible with paiements.view', function () {
    actingAsWithPermission('paiements.view');
    $this->get(route('paiements.index'))->assertOk();
});

// --- Users ---

test('users index requires users.view', function () {
    actingAsWithPermission('dashboard.view');
    $this->get(route('users.index'))->assertForbidden();
});

test('users index is accessible with users.view', function () {
    actingAsWithPermission('users.view');
    $this->get(route('users.index'))->assertOk();
});

// --- Permis ---

test('permis index requires permis.view', function () {
    actingAsWithPermission('dashboard.view');
    $this->get(route('permis.index'))->assertForbidden();
});

test('permis index is accessible with permis.view', function () {
    actingAsWithPermission('permis.view');
    $this->get(route('permis.index'))->assertOk();
});

// --- Certificats ---

test('certificats index requires certificats.view', function () {
    actingAsWithPermission('dashboard.view');
    $this->get(route('certificats.index'))->assertForbidden();
});

test('certificats index is accessible with certificats.view', function () {
    actingAsWithPermission('certificats.view');
    $this->get(route('certificats.index'))->assertOk();
});

// --- explicit permission assignment tests ---

test('user with explicit view permissions can access view routes', function () {
    $province = Province::first();
    $user = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);
    foreach (['dashboard.view', 'conducteurs.view', 'engins.view', 'debardeurs.view', 'permis.view', 'certificats.view'] as $key) {
        $perm = Permission::where('key', $key)->first();
        DB::table('permission_user')->insert([
            'permission_id' => $perm->id,
            'user_id' => $user->id,
            'province_id' => null,
        ]);
    }
    test()->actingAs($user);
    $this->get(route('dashboard'))->assertOk();
    $this->get(route('conducteurs.index'))->assertOk();
    $this->get(route('engins.index'))->assertOk();
    $this->get(route('debardeurs.index'))->assertOk();
    $this->get(route('permis.index'))->assertOk();
    $this->get(route('certificats.index'))->assertOk();
    $this->get(route('provinces.index'))->assertForbidden();
    $this->get(route('taxes.index'))->assertForbidden();
});

// --- Permission controller ---

test('permissions index requires users.manage-permissions', function () {
    actingAsWithPermission('dashboard.view');
    $this->get(route('permissions.index'))->assertForbidden();
});

test('permissions index is accessible with users.manage-permissions', function () {
    actingAsWithPermission('users.manage-permissions');
    $this->get(route('permissions.index'))->assertOk();
});

test('non-superadmin cannot update superadmin permissions', function () {
    $province = Province::first();
    $superadmin = User::factory()->create(['role' => 'superadmin', 'email_verified_at' => now()]);
    $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    ProvinceAssignment::create(['user_id' => $admin->id, 'province_id' => $province->id, 'role' => 'admin']);

    // Give admin the manage-permissions permission globally
    $perm = Permission::where('key', 'users.manage-permissions')->first();
    DB::table('permission_user')->insert([
        'permission_id' => $perm->id,
        'user_id' => $admin->id,
        'province_id' => null,
    ]);

    $this->actingAs($admin);
    $this->put(route('permissions.update', $superadmin->id), [
        'permission_keys' => ['dashboard.view'],
    ])->assertForbidden();
});

test('non-superadmin cannot update user outside their provinces', function () {
    $provinceA = Province::first();
    $provinceB = Province::factory()->create(['name' => 'Province B']);
    $otherUser = User::factory()->create(['role' => 'user', 'email_verified_at' => now()]);
    ProvinceAssignment::create(['user_id' => $otherUser->id, 'province_id' => $provinceB->id, 'role' => 'user']);

    $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    ProvinceAssignment::create(['user_id' => $admin->id, 'province_id' => $provinceA->id, 'role' => 'admin']);

    $perm = Permission::where('key', 'users.manage-permissions')->first();
    DB::table('permission_user')->insert([
        'permission_id' => $perm->id,
        'user_id' => $admin->id,
        'province_id' => null,
    ]);

    $this->actingAs($admin);
    $this->put(route('permissions.update', $otherUser->id), [
        'permission_keys' => ['dashboard.view'],
    ])->assertForbidden();
});
