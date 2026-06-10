<?php

namespace App\Http\Controllers;

use App\Models\Permis;
use App\Models\Province;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PermisController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Permis::class);

        $user = $request->user();
        $query = Permis::with(['conducteur.province']);

        // Scope province : filtrer les permis via le conducteur
        $assignedIds = $user->assignedProvinceIds();
        if (! empty($assignedIds)) {
            $query->whereHas('conducteur', function ($q) use ($assignedIds) {
                $q->whereIn('province_id', $assignedIds);
            });
        }

        if ($request->has('search')) {
            $query->where('license_number', 'like', '%'.$request->search.'%')
                ->orWhereHas('conducteur', function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%');
                });
        }

        $baseQuery = clone $query;

        return Inertia::render('permis/Index', [
            'permis' => $query->latest()->get(),
            'lockedToProvince' => ! empty($assignedIds),
            'stats' => [
                'total' => $baseQuery->count(),
                'active' => (clone $baseQuery)->where('status', 'Valide')->count(),
                'expired' => (clone $baseQuery)->where('status', 'Expire')->count(),
            ],
        ]);
    }

    public function show(Permis $permis): Response
    {
        $this->authorize('view', $permis);

        return Inertia::render('permis/Show', [
            'permis' => $permis->load(['conducteur.province', 'conducteur.engin']),
        ]);
    }
}
