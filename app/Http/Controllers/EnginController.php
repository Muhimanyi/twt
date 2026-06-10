<?php

namespace App\Http\Controllers;

use App\Models\Engin;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class EnginController extends Controller
{
    /**
     * Display a listing of the resource.
     * Scope automatique selon la province de l'utilisateur connecté.
     */
    public function index(Request $request, ?string $category = null): Response
    {
        $this->authorize('viewAny', Engin::class);

        $user = $request->user();
        $category = $category ?? $request->query('category');
        $provinceId = $request->query('province_id');

        $query = Engin::with('province');

        // Scope province : si l'utilisateur est lié à des provinces, on filtre
        $assignedIds = $user->assignedProvinceIds();
        if (! empty($assignedIds)) {
            $query->whereIn('province_id', $assignedIds);
        } elseif ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($category) {
            $query->where('category', $category);
        }

        $baseQuery = clone $query;

        return Inertia::render('engins/Index', [
            'engins' => $query->latest()->get(),
            'provinces' => Province::orderBy('name')->get(),
            'currentCategory' => $category,
            'currentProvinceId' => $provinceId,
            'lockedToProvince' => ! empty($assignedIds),
            'stats' => [
                'total' => $baseQuery->count(),
                'by_category' => (clone $baseQuery)->groupBy('category')->selectRaw('category, count(*) as total')->pluck('total', 'category'),
                'by_province' => (clone $baseQuery)->whereNotNull('province_id')->groupBy('province_id')->selectRaw('province_id, count(*) as total')->pluck('total', 'province_id'),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Engin::class);

        $validated = $request->validate([
            'category' => 'required|string',
            'sub_category' => 'required|string',
            'construction_type' => 'nullable|string',
            'province_id' => 'required|exists:provinces,id',
            'owner_name' => 'required|string',
            'owner_gender' => 'required|string',
            'owner_birth_place' => 'required|string',
            'owner_father_name' => 'required|string',
            'owner_mother_name' => 'required|string',
            'owner_marital_status' => 'required|string',
            'owner_nationality' => 'required|string',
            'owner_phone' => 'required|string',
            'owner_email' => 'nullable|email',
            'owner_address' => 'required|string',
            'owner_photo' => 'nullable|image|max:2048',
            'vehicle_genre' => 'nullable|string',
            'vehicle_brand' => 'nullable|string',
            'vehicle_type' => 'nullable|string',
            'vehicle_color' => 'nullable|string',
            'plate_number' => 'nullable|string',
            'engine_number' => 'nullable|string',
            'engine_brand' => 'nullable|string',
            'chassis_number' => 'nullable|string',
            'manufacture_year' => 'nullable|integer',
            'horsepower' => 'nullable|integer',
            'identification_place' => 'required|string',
            'identification_date' => 'required|date',
            // Maritime fields
            'vessel_name' => 'nullable|string',
            'registration_number' => 'nullable|string',
            'registration_place' => 'nullable|string',
            'registration_date' => 'nullable|date',
            'capacity' => 'nullable|string',
            'navigation_line' => 'nullable|string',
            'height' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'propulsion_type' => 'nullable|string',
            'usage' => 'nullable|array',
            'crew_count' => 'nullable|integer',
            'driver_count' => 'nullable|integer',
        ]);

        if ($request->hasFile('owner_photo')) {
            $validated['owner_photo_path'] = $request->file('owner_photo')->store('engins/owners', 'public');
        }

        Engin::create($validated);

        return Redirect::route('engins.index')
            ->with('success', 'Engin enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Engin $engin): Response
    {
        $this->authorize('view', $engin);

        return Inertia::render('engins/Show', [
            'engin' => $engin->load('province'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Engin $engin): RedirectResponse
    {
        $this->authorize('update', $engin);

        $validated = $request->validate([
            'category' => 'required|string',
            'sub_category' => 'required|string',
            'province_id' => 'required|exists:provinces,id',
            'construction_type' => 'nullable|string',
            'owner_name' => 'required|string',
            'owner_gender' => 'required|string',
            'owner_phone' => 'required|string',
            'owner_address' => 'required|string',
            'owner_photo' => 'nullable|image|max:2048',
            'vehicle_genre' => 'nullable|string',
            'vehicle_brand' => 'nullable|string',
            'vehicle_type' => 'nullable|string',
            'vehicle_color' => 'nullable|string',
            'plate_number' => 'nullable|string',
            'engine_number' => 'nullable|string',
            'engine_brand' => 'nullable|string',
            'chassis_number' => 'nullable|string',
            'manufacture_year' => 'nullable|integer',
            'horsepower' => 'nullable|integer',
            // Maritime fields
            'vessel_name' => 'nullable|string',
            'registration_number' => 'nullable|string',
            'registration_place' => 'nullable|string',
            'registration_date' => 'nullable|date',
            'capacity' => 'nullable|string',
            'navigation_line' => 'nullable|string',
            'height' => 'nullable|numeric',
            'length' => 'nullable|numeric',
            'width' => 'nullable|numeric',
            'propulsion_type' => 'nullable|string',
            'usage' => 'nullable|array',
            'crew_count' => 'nullable|integer',
            'driver_count' => 'nullable|integer',
        ]);

        if ($request->hasFile('owner_photo')) {
            $validated['owner_photo_path'] = $request->file('owner_photo')->store('engins/owners', 'public');
        }

        $engin->update($validated);

        return Redirect::route('engins.index')
            ->with('success', 'Informations de l\'engin mises à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Engin $engin): RedirectResponse
    {
        $this->authorize('delete', $engin);

        $engin->delete();

        return Redirect::route('engins.index')
            ->with('success', 'Engin supprimé.');
    }
}
