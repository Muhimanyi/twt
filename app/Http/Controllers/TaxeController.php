<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\Taxe;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class TaxeController extends Controller
{
    /**
     * Règles de validation communes
     */
    private function validationRules(): array
    {
        return [
            'label' => 'required|string|max:255',
            'beneficiary' => 'required|string|max:255',
            'target' => [
                'required',
                'string',
                'in:Conducteur,Engin,Transporteur,Passager,Chargeur',
            ],
            'amount' => 'required|numeric|min:0|max:999999999999.99',
            'currency' => [
                'required',
                'string',
                'size:3',
                'in:USD,EUR,CDF,ZAR',
            ],
            'frequency' => [
                'required',
                'string',
                'in:Mensuelle,Trimestrielle,Semestrielle,Annuelle,Unique',
            ],
            'sector' => [
                'nullable',
                'string',
                'in:routier,lacustre,aerien,ferroviaire',
            ],
            'province_id' => 'nullable|exists:provinces,id',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    private function validationMessages(): array
    {
        return [
            'label.required' => 'L\'intitulé de la taxe est obligatoire.',
            'label.max' => 'L\'intitulé ne peut pas dépasser 255 caractères.',
            'beneficiary.required' => 'L\'entité bénéficiaire est obligatoire.',
            'target.required' => 'L\'assujetti est obligatoire.',
            'target.in' => 'L\'assujetti sélectionné est invalide.',
            'amount.required' => 'Le montant est obligatoire.',
            'amount.numeric' => 'Le montant doit être un nombre valide.',
            'amount.min' => 'Le montant doit être supérieur ou égal à 0.',
            'amount.max' => 'Le montant dépasse la limite autorisée.',
            'currency.required' => 'La devise est obligatoire.',
            'currency.size' => 'La devise doit être un code à 3 caractères.',
            'currency.in' => 'La devise sélectionnée est invalide.',
            'frequency.required' => 'La fréquence est obligatoire.',
            'frequency.in' => 'La fréquence sélectionnée est invalide.',
            'sector.in' => 'Le secteur sélectionné est invalide.',
            'province_id.exists' => 'La province sélectionnée est invalide.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        try {
            $user = $request->user();
            $query = Taxe::with('province');
            $provinceId = $request->query('province_id');

            // Filtrage par province
            $assignedIds = $user ? $user->assignedProvinceIds() : [];
            if (! empty($assignedIds)) {
                $query->where(function ($q) use ($assignedIds) {
                    $q->whereIn('province_id', $assignedIds)
                        ->orWhereNull('province_id'); // Taxes nationales visibles
                });
            } elseif ($provinceId && $provinceId !== 'all') {
                $query->where('province_id', $provinceId);
            }

            // Récupération des données
            $taxes = $query->latest()->get();
            $provinces = Province::orderBy('name')->get();

            // Statistiques
            $stats = [
                'total' => Taxe::count(),
                'active' => Taxe::where('is_active', true)->count(),
                'by_target' => Taxe::groupBy('target')
                    ->selectRaw('target, count(*) as total')
                    ->pluck('total', 'target'),
            ];

            return Inertia::render('taxes/Index', [
                'taxes' => $taxes,
                'provinces' => $provinces,
                'currentProvinceId' => $provinceId,
                'stats' => $stats,
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des taxes', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Inertia::render('taxes/Index', [
                'taxes' => [],
                'stats' => [
                    'total' => 0,
                    'active' => 0,
                    'by_target' => [],
                ],
            ])->with('error', 'Une erreur est survenue lors du chargement des taxes.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Traitement préventif des chaînes vides envoyées par le front
        if ($request->input('province_id') === '') {
            $request->merge(['province_id' => null]);
        }
        if ($request->input('sector') === '') {
            $request->merge(['sector' => null]);
        }

        $validated = $request->validate(
            $this->validationRules(),
            $this->validationMessages()
        );

        $validated['is_active'] = $request->boolean('is_active', true);

        $taxe = Taxe::create($validated);

        return Redirect::route('taxes.index')->with('success', "La taxe '{$taxe->label}' a été créée avec succès.");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Taxe $tax): RedirectResponse
    {
        // Traitement préventif des chaînes vides
        if ($request->input('province_id') === '') {
            $request->merge(['province_id' => null]);
        }
        if ($request->input('sector') === '') {
            $request->merge(['sector' => null]);
        }

        $validated = $request->validate(
            $this->validationRules(),
            $this->validationMessages()
        );

        $validated['is_active'] = $request->boolean('is_active', false);

        $tax->update($validated);

        return Redirect::route('taxes.index')->with('success', "La taxe '{$tax->label}' a été mise à jour avec succès.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Taxe $tax): RedirectResponse
    {
        try {
            // Vérifier si la ressource existe
            if (! $tax || ! $tax->exists) {
                Log::warning('Tentative de suppression d\'une taxe inexistante', [
                    'id' => request()->route('tax'),
                ]);

                return Redirect::route('taxes.index')
                    ->with('error', 'La taxe n\'existe pas ou a déjà été supprimée.');
            }

            // Enregistrer les informations avant la suppression
            $taxeLabel = $tax->label;
            $taxeId = $tax->id;

            // Suppression de la taxe
            $tax->delete();

            Log::info('Taxe supprimée avec succès', [
                'taxe_id' => $taxeId,
                'label' => $taxeLabel,
                'deleted_at' => now(),
            ]);

            return Redirect::route('taxes.index')
                ->with('success', "La taxe '{$taxeLabel}' a été supprimée avec succès.");
        } catch (Exception $e) {
            Log::error('Erreur critique lors de la suppression d\'une taxe', [
                'taxe_id' => $tax->id ?? null,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Redirect::back()
                ->with('error', 'Une erreur est survenue lors de la suppression de la taxe. Veuillez réessayer.');
        }
    }
}
