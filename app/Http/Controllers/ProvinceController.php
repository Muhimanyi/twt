<?php

namespace App\Http\Controllers;

use App\Http\Requests\Province\StoreProvinceRequest;
use App\Http\Requests\Province\UpdateProvinceRequest;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProvinceController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Province::class);

        $user = $request->user();

        // Scope : les utilisateurs affectés à des provinces voient uniquement les leurs
        $assignedIds = $user->assignedProvinceIds();
        if (! empty($assignedIds)) {
            $provinces = Province::whereIn('id', $assignedIds)->orderBy('name')->get();
        } else {
            $provinces = Province::orderBy('name')->get();
        }

        return Inertia::render('provinces/Index', [
            'provinces' => $provinces,
        ]);
    }

    public function store(StoreProvinceRequest $request): RedirectResponse
    {
        $this->authorize('create', Province::class);

        $data = $request->validated();

        // Ensure booleans are correctly captured
        $data['language_lingala'] = $request->boolean('language_lingala');
        $data['language_kikongo'] = $request->boolean('language_kikongo');
        $data['language_kiluba'] = $request->boolean('language_kiluba');
        $data['language_kiswahili'] = $request->boolean('language_kiswahili');

        Province::create($data);

        return Redirect::route('provinces.index')
            ->with('success', 'Province créée avec succès.');
    }

    public function show(Province $province): Response
    {
        $this->authorize('view', $province);

        return Inertia::render('provinces/Show', [
            'province' => $province,
        ]);
    }

    public function update(UpdateProvinceRequest $request, Province $province): RedirectResponse
    {
        $this->authorize('update', $province);

        $data = $request->validated();
        // Ensure booleans are correctly captured
        $data['language_lingala'] = $request->boolean('language_lingala');
        $data['language_kikongo'] = $request->boolean('language_kikongo');
        $data['language_kiluba'] = $request->boolean('language_kiluba');
        $data['language_kiswahili'] = $request->boolean('language_kiswahili');

        $province->update($data);

        return Redirect::route('provinces.index')
            ->with('success', 'Province mise à jour avec succès.');
    }

    public function destroy(Province $province): RedirectResponse
    {
        $this->authorize('delete', $province);

        $province->delete();

        return Redirect::route('provinces.index')
            ->with('success', 'Province supprimée avec succès.');
    }
}
