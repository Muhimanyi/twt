<?php

namespace App\Http\Controllers;

use App\Models\Certificat;
use App\Models\Conducteur;
use App\Models\Debardeur;
use App\Models\Engin;
use App\Models\Paiement;
use App\Models\Permis;
use App\Models\Province;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $condQuery = Conducteur::query();
        $enginQuery = Engin::query();
        $debQuery = Debardeur::query();
        $permisQuery = Permis::query();
        $certQuery = Certificat::query();
        $paiementQuery = Paiement::query();

        $assignedIds = $user->assignedProvinceIds();
        if (! empty($assignedIds)) {
            $condQuery->whereIn('province_id', $assignedIds);
            $enginQuery->whereIn('province_id', $assignedIds);
            $debQuery->whereIn('province_id', $assignedIds);
            $paiementQuery->whereIn('province_id', $assignedIds);
        }

        $stats = [
            'total_conducteurs' => (clone $condQuery)->count(),
            'total_engins' => (clone $enginQuery)->count(),
            'total_debardeurs' => (clone $debQuery)->count(),
            'total_permis' => (clone $permisQuery)->count(),
            'permis_valides' => (clone $permisQuery)->where('status', 'Valide')->count(),
            'total_certificats' => (clone $certQuery)->count(),
            'total_paiements' => (clone $paiementQuery)->count(),
            'revenus_total' => (clone $paiementQuery)->sum('amount'),
            'conducteurs_par_secteur' => (clone $condQuery)
                ->selectRaw('sector, count(*) as total')
                ->groupBy('sector')
                ->pluck('total', 'sector'),
            'engins_par_categorie' => (clone $enginQuery)
                ->selectRaw('category, count(*) as total')
                ->groupBy('category')
                ->pluck('total', 'category'),
            'paiements_recents' => (clone $paiementQuery)
                ->with('payable')
                ->latest()
                ->take(5)
                ->get(),
            'province' => ! empty($assignedIds) ? Province::whereIn('id', $assignedIds)->pluck('name')->join(', ') : 'Toutes les provinces',
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}
