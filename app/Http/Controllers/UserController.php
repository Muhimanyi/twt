<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::with('province', 'provinceAssignments.province')->orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();

        return Inertia::render('users/Index', [
            'users' => $users,
            'provinces' => $provinces,
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make('123456Place');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        $assignments = $data['province_assignments'] ?? [];
        unset($data['province_assignments']);

        if (empty($assignments) && ! empty($data['province_id'])) {
            $roles = $data['role'] ?? [];
            $role = is_array($roles) ? ($roles[0] ?? 'user') : $roles;
            $assignments[] = [
                'province_id' => $data['province_id'],
                'role' => $role,
                'is_primary' => true,
            ];
        }

        $user = User::create($data);

        if (! empty($assignments)) {
            foreach ($assignments as $i => $a) {
                $user->provinceAssignments()->create([
                    'province_id' => $a['province_id'],
                    'role' => $a['role'],
                    'is_primary' => $a['is_primary'] ?? ($i === 0),
                ]);
            }
        }

        return Redirect::route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $assignments = $data['province_assignments'] ?? [];
        unset($data['province_assignments']);

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        } else {
            unset($data['photo']);
        }

        $user->update($data);

        if (! empty($assignments)) {
            $user->provinceAssignments()->delete();
            foreach ($assignments as $i => $a) {
                $user->provinceAssignments()->create([
                    'province_id' => $a['province_id'],
                    'role' => $a['role'],
                    'is_primary' => $a['is_primary'] ?? ($i === 0),
                ]);
            }
        } elseif (isset($data['province_id'])) {
            $roles = $data['role'] ?? $user->role ?? [];
            $role = is_array($roles) ? ($roles[0] ?? 'user') : $roles;
            $user->provinceAssignments()->delete();
            $user->provinceAssignments()->create([
                'province_id' => $data['province_id'],
                'role' => $role,
                'is_primary' => true,
            ]);
        }

        $user->flushPermissionCache();

        return Redirect::route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return Redirect::route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
