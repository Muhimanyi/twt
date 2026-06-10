<?php

namespace App\Http\Controllers;

use App\Http\Requests\Debardeur\StoreDebardeurRequest;
use App\Http\Requests\Debardeur\UpdateDebardeurRequest;
use App\Models\Debardeur;
use App\Models\Province;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class DebardeurController extends Controller
{
    public function index(Request $request, ?string $sector = null): Response
    {
        $this->authorize('viewAny', Debardeur::class);

        $user = $request->user();
        $sector = $sector ?? $request->query('sector');

        $query = Debardeur::with('province');

        // Scope province
        $assignedIds = $user->assignedProvinceIds();
        if (! empty($assignedIds)) {
            $query->whereIn('province_id', $assignedIds);
        }

        if ($sector) {
            $query->where('sector', ucfirst($sector));
        }

        $baseQuery = clone $query;

        return Inertia::render('debardeurs/Index', [
            'debardeurs' => $query->orderBy('last_name')->get(),
            'provinces' => Province::orderBy('name')->get(),
            'currentSector' => $sector,
            'lockedToProvince' => ! empty($assignedIds),
            'stats' => [
                'total' => $baseQuery->count(),
                'by_sector' => (clone $baseQuery)->select('sector', \DB::raw('count(*) as total'))->groupBy('sector')->pluck('total', 'sector'),
                'by_province' => (clone $baseQuery)->select('province_id', \DB::raw('count(*) as total'))->groupBy('province_id')->pluck('total', 'province_id'),
            ],
        ]);
    }

    public function store(StoreDebardeurRequest $request): RedirectResponse
    {
        $this->authorize('create', Debardeur::class);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('debardeurs/photos', 'public');
        }

        Debardeur::create($data);

        return Redirect::route('debardeurs.index')
            ->with('success', 'Débardeur enregistré avec succès.');
    }

    public function show(Debardeur $debardeur): Response
    {
        $this->authorize('view', $debardeur);

        $debardeur->load('province');

        return Inertia::render('debardeurs/Show', [
            'debardeur' => $debardeur,
        ]);
    }

    public function update(UpdateDebardeurRequest $request, Debardeur $debardeur): RedirectResponse
    {
        $this->authorize('update', $debardeur);

        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('debardeurs/photos', 'public');
        }

        $debardeur->update($data);

        return Redirect::route('debardeurs.index')
            ->with('success', 'Informations du débardeur mises à jour.');
    }

    public function destroy(Debardeur $debardeur): RedirectResponse
    {
        $this->authorize('delete', $debardeur);

        $debardeur->delete();

        return Redirect::route('debardeurs.index')
            ->with('success', 'Débardeur supprimé de la base.');
    }
}
