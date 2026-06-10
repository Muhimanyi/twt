<?php

use App\Models\Permission;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\DB;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users with dashboard permission can visit the dashboard', function () {
    $this->seed(PermissionSeeder::class);
    $user = User::factory()->create(['role' => 'user']);
    $perm = Permission::where('key', 'dashboard.view')->first();
    DB::table('permission_user')->insert([
        'permission_id' => $perm->id,
        'user_id' => $user->id,
        'province_id' => null,
    ]);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('authenticated users without permission cannot visit the dashboard', function () {
    $this->seed(PermissionSeeder::class);
    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertForbidden();
});
