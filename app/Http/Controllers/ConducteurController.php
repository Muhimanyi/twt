<?php

namespace App\Http\Controllers;

use App\Models\Conducteur;
use App\Models\Engin;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ConducteurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ?string $sector = null): Response
    {
        $this->authorize('viewAny', Conducteur::class);

        $user = $request->user();
        $sector = $sector ?? $request->query('sector');
        $provinceId = $request->query('province_id');

        $query = Conducteur::with(['province', 'engin']);

        // Scope province : si l'utilisateur est lié à des provinces, on filtre
        $assignedIds = $user->assignedProvinceIds();
        if (! empty($assignedIds)) {
            $query->whereIn('province_id', $assignedIds);
        } elseif ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        if ($sector) {
            $query->where('sector', $sector);
        }

        $baseQuery = clone $query;

        return Inertia::render('conducteurs/Index', [
            'conducteurs' => $query->latest()->get(),
            'provinces' => Province::orderBy('name')->get(),
            'currentSector' => $sector,
            'currentProvinceId' => $provinceId,
            'lockedToProvince' => ! empty($assignedIds),
            'stats' => [
                'total' => $baseQuery->count(),
                'by_sector' => (clone $baseQuery)->groupBy('sector')->selectRaw('sector, count(*) as total')->pluck('total', 'sector'),
                'by_province' => (clone $baseQuery)->whereNotNull('province_id')->groupBy('province_id')->selectRaw('province_id, count(*) as total')->pluck('total', 'province_id'),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Conducteur::class);
        $validated = $request->validate([
            'sector' => 'required|string',
            'engin_id' => 'nullable|exists:engins,id',
            'province_id' => 'required|exists:provinces,id',
            'name' => 'required|string',
            'gender' => 'required|string',
            'birth_place' => 'required|string',
            'birth_date' => 'required|date',
            'father_name' => 'required|string',
            'mother_name' => 'required|string',
            'marital_status' => 'required|string',
            'nationality' => 'required|string',
            'profession' => 'required|string',
            'license_number' => 'nullable|string',
            'license_category' => 'nullable|string',
            'license_issued_at' => 'nullable|date',
            'license_expires_at' => 'nullable|date',
            'id_piece_type' => 'required|string',
            'id_piece_number' => 'required|string',
            'id_piece_issued_place' => 'required|string',
            'id_piece_issued_at' => 'required|date',
            'migratory_document_type' => 'nullable|string',
            'visa_type' => 'nullable|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'address' => 'required|string',
            'affiliation' => 'nullable|string',
            'transport_mode' => 'nullable|string',
            'expiration_date' => 'nullable|date',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('conducteurs/photos', 'public');
        }

        Conducteur::create($validated);

        return Redirect::route('conducteurs.index')
            ->with('success', 'Conducteur enregistré avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Conducteur $conducteur): Response
    {
        $this->authorize('view', $conducteur);

        return Inertia::render('conducteurs/Show', [
            'conducteur' => $conducteur->load(['province', 'engin']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Conducteur $conducteur): RedirectResponse
    {
        $this->authorize('update', $conducteur);
        $validated = $request->validate([
            'province_id' => 'required|exists:provinces,id',
            'engin_id' => 'nullable|exists:engins,id',
            'name' => 'required|string',
            'gender' => 'required|string',
            'phone' => 'required|string',
            'address' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            // Add other fields as needed for update
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('conducteurs/photos', 'public');
        }

        $conducteur->update($validated);

        return Redirect::route('conducteurs.index')
            ->with('success', 'Informations du conducteur mises à jour.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Conducteur $conducteur): RedirectResponse
    {
        $this->authorize('delete', $conducteur);

        $conducteur->delete();

        return Redirect::route('conducteurs.index')
            ->with('success', 'Conducteur supprimé.');
    }

    /**
     * Search for an engin by its identification number.
     */
    public function searchEngin(string $idNumber)
    {
        $engin = Engin::where('identification_number', $idNumber)
            ->orWhere('plate_number', $idNumber)
            ->first();

        if (! $engin) {
            return response()->json(['message' => 'Engin non trouvé'], 404);
        }

        return response()->json($engin);
    }
}
