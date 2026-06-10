<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class AnnonceController extends Controller
{
    private function validationRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'featured_image' => ['nullable', 'string', 'max:500'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'province_id' => ['nullable', 'exists:provinces,id'],
        ];
    }

    private function validationMessages(): array
    {
        return [
            'title.required' => __('validation.required', ['attribute' => __('annonces.title')]),
            'content.required' => __('validation.required', ['attribute' => __('annonces.content')]),
        ];
    }

    public function index(Request $request): Response
    {
        $query = Annonce::with(['author:id,name', 'province:id,name']);

        if ($request->user()) {
            $provinceIds = $request->user()->assignedProvinceIds();
            if (!empty($provinceIds)) {
                $query->where(function ($q) use ($provinceIds) {
                    $q->whereIn('province_id', $provinceIds)->orWhereNull('province_id');
                });
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $sortField = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        return Inertia('annonces/Index', [
            'annonces' => $query->paginate(15)->withQueryString(),
            'stats' => [
                'total' => Annonce::count(),
                'published' => Annonce::where('is_published', true)->count(),
                'drafts' => Annonce::where('is_published', false)->count(),
            ],
            'filters' => $request->only(['search', 'sort', 'order']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->validationRules(), $this->validationMessages());

        $validated['author_id'] = $request->user()->id;

        if ($request->boolean('is_published') && !$request->filled('published_at')) {
            $validated['published_at'] = now();
        }

        Annonce::create($validated);

        return redirect()->route('annonces.index')
            ->with('success', __('annonces.created'));
    }

    public function update(Request $request, Annonce $annonce): RedirectResponse
    {
        $validated = $request->validate($this->validationRules(), $this->validationMessages());

        if ($request->boolean('is_published') && !$annonce->published_at && !$request->filled('published_at')) {
            $validated['published_at'] = now();
        }

        $annonce->update($validated);

        return redirect()->route('annonces.index')
            ->with('success', __('annonces.updated'));
    }

    public function destroy(Annonce $annonce): RedirectResponse
    {
        $annonce->delete();

        return redirect()->route('annonces.index')
            ->with('success', __('annonces.deleted'));
    }
}
