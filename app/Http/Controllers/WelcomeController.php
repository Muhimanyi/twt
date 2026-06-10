<?php

namespace App\Http\Controllers;

use App\Models\Annonce;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Fortify\Features;

class WelcomeController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'annonces' => Annonce::query()
                ->published()
                ->latest('published_at')
                ->take(6)
                ->get(['id', 'title', 'excerpt', 'content', 'published_at']),
        ]);
    }
}
