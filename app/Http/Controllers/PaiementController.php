<?php

namespace App\Http\Controllers;

use App\Models\Conducteur;
use App\Models\Debardeur;
use App\Models\Engin;
use App\Models\Paiement;
use App\Models\Province;
use App\Models\Taxe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PaiementController extends Controller
{
    /**
     * Mappings des types de cibles vers les modèles correspondants.
     */
    private function targetModelMap(): array
    {
        return [
            'Conducteur' => Conducteur::class,
            'Engin' => Engin::class,
            'Transporteur' => Debardeur::class,
        ];
    }

    /**
     * Affiche la liste des paiements.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $query = Paiement::with(['taxe', 'user', 'province'])
            ->latest('paid_at');

        // Scope par province si l'utilisateur est lié à des provinces
        $assignedIds = $user ? $user->assignedProvinceIds() : [];
        if (! empty($assignedIds)) {
            $query->whereIn('province_id', $assignedIds);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhereHas('taxe', fn ($t) => $t->where('label', 'like', "%{$search}%"));
            });
        }

        $paiements = $query->get()->map(function ($p) {
            // Résoudre le payable (Conducteur, Engin, etc.)
            $payable = $p->payable;
            $p->payable_label = $this->resolvePayableLabel($p->payable_type, $payable);
            $p->payable_type_label = $this->resolvePayableTypeLabel($p->payable_type);

            return $p;
        });

        $taxes = Taxe::where('is_active', true)->orderBy('label')->get();
        $provinces = Province::orderBy('name')->get();

        // Données disponibles pour la sélection du payeur
        $conducteurs = Conducteur::select('id', 'name', 'identification_number', 'sector')->orderBy('name')->get();
        $engins = Engin::select('id', 'plate_number', 'owner_name', 'identification_number', 'category')->orderBy('owner_name')->get();
        $debardeurs = Debardeur::select('id', 'last_name', 'first_name', 'middle_name', 'permanent_number')->orderBy('last_name')->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'name' => trim($d->last_name.' '.$d->first_name.' '.$d->middle_name),
                'identification_number' => $d->permanent_number,
            ]);

        $stats = [
            'total' => Paiement::count(),
            'today' => Paiement::whereDate('paid_at', today())->count(),
            'total_amount' => Paiement::sum('amount'),
        ];

        return Inertia::render('taxes/Paiements', [
            'paiements' => $paiements,
            'taxes' => $taxes,
            'provinces' => $provinces,
            'conducteurs' => $conducteurs,
            'engins' => $engins,
            'debardeurs' => $debardeurs,
            'stats' => $stats,
            'currentProvinceId' => $request->query('province_id'),
        ]);
    }

    /**
     * Enregistre un nouveau paiement et retourne pour impression.
     */
    public function store(Request $request): RedirectResponse
    {
        // Prétraitement
        if ($request->input('province_id') === '') {
            $request->merge(['province_id' => null]);
        }

        $validated = $request->validate([
            'taxe_id' => 'required|exists:taxes,id',
            'payable_type' => 'required|in:Conducteur,Engin,Transporteur',
            'payable_id' => 'required|integer',
            'payment_method' => 'required|in:cash,bank,mobile_money',
            'province_id' => 'nullable|exists:provinces,id',
            'notes' => 'nullable|string|max:500',
        ]);

        // Récupérer la taxe pour le montant
        $taxe = Taxe::findOrFail($validated['taxe_id']);

        // Résoudre le type polymorphique
        $modelClass = $this->targetModelMap()[$validated['payable_type']] ?? null;
        if (! $modelClass || ! $modelClass::find($validated['payable_id'])) {
            return Redirect::back()->withInput()->with('error', 'Le payeur sélectionné est invalide.');
        }

        $paiement = Paiement::create([
            'reference' => 'PAY-'.strtoupper(Str::random(8)),
            'taxe_id' => $taxe->id,
            'payable_type' => $modelClass,
            'payable_id' => $validated['payable_id'],
            'amount' => $taxe->amount,
            'currency' => $taxe->currency,
            'user_id' => $request->user()?->id,
            'province_id' => $validated['province_id'] ?? (count($assignedIds) === 1 ? $assignedIds[0] : null),
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
            'paid_at' => now(),
        ]);

        Log::info('Paiement enregistré', ['reference' => $paiement->reference]);

        return Redirect::route('paiements.print', $paiement)
            ->with('success', "Paiement enregistré. Référence: {$paiement->reference}");
    }

    /**
     * Affiche la quittance imprimable.
     */
    public function print(Paiement $paiement): Response
    {
        $paiement->load(['taxe', 'user', 'province']);
        $payable = $paiement->payable;

        return Inertia::render('taxes/Quittance', [
            'paiement' => $paiement,
            'payable_label' => $this->resolvePayableLabel($paiement->payable_type, $payable),
            'payable_type_label' => $this->resolvePayableTypeLabel($paiement->payable_type),
            'payable' => $payable,
        ]);
    }

    /**
     * Suppression d'un paiement.
     */
    public function destroy(Paiement $paiement): RedirectResponse
    {
        $ref = $paiement->reference;
        $paiement->delete();

        return Redirect::route('paiements.index')->with('success', "Paiement {$ref} supprimé.");
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    private function resolvePayableLabel(string $type, $payable): string
    {
        if (! $payable) {
            return 'Inconnu';
        }

        return match (class_basename($type)) {
            'Conducteur' => $payable->name.' ('.$payable->identification_number.')',
            'Engin' => ($payable->plate_number ?? $payable->registration_number ?? 'N/A').' — '.$payable->owner_name,
            'Debardeur' => trim($payable->last_name.' '.$payable->first_name.' '.$payable->middle_name).' ('.$payable->permanent_number.')',
            default => 'Inconnu',
        };
    }

    private function resolvePayableTypeLabel(string $type): string
    {
        return match (class_basename($type)) {
            'Conducteur' => 'Conducteur',
            'Engin' => 'Engin',
            'Debardeur' => 'Transporteur',
            default => $type,
        };
    }
}
