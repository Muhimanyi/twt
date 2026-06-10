<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CertificatController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Certificat::class);

        $user = $request->user();
        $query = Certificat::with(['engin.province']);

        // Scope province : filtrer les certificats via l'engin
        $assignedIds = $user->assignedProvinceIds();
        if (! empty($assignedIds)) {
            $query->whereHas('engin', function ($q) use ($assignedIds) {
                $q->whereIn('province_id', $assignedIds);
            });
        }

        if ($request->has('search')) {
            $query->where('certificate_number', 'like', '%'.$request->search.'%')
                ->orWhere('owner_name', 'like', '%'.$request->search.'%')
                ->orWhereHas('engin', function ($q) use ($request) {
                    $q->where('identification_number', 'like', '%'.$request->search.'%');
                });
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $baseQuery = clone $query;

        return Inertia::render('certificats/Index', [
            'certificats' => $query->latest()->get(),
            'types' => Certificat::select('type')->distinct()->pluck('type'),
            'lockedToProvince' => ! empty($assignedIds),
            'stats' => [
                'total' => $baseQuery->count(),
                'identification' => (clone $baseQuery)->where('type', 'Identification')->count(),
                'possession' => (clone $baseQuery)->where('type', 'Possession')->count(),
            ],
        ]);
    }

    public function show(Certificat $certificat): Response
    {
        $this->authorize('view', $certificat);

        return Inertia::render('certificats/Show', [
            'certificat' => $certificat->load(['engin.province']),
        ]);
    }
}
