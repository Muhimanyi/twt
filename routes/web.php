<?php

use App\Http\Controllers\AnnonceController;
use App\Http\Controllers\CertificatController;
use App\Http\Controllers\ConducteurController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebardeurController;
use App\Http\Controllers\EnginController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\PermisController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\TaxeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', WelcomeController::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('provinces', [ProvinceController::class, 'index'])->name('provinces.index')->middleware('permission:provinces.view');
    Route::get('provinces/{province}', [ProvinceController::class, 'show'])->name('provinces.show')->middleware('permission:provinces.view');
    Route::get('provinces/create', [ProvinceController::class, 'create'])->name('provinces.create')->middleware('permission:provinces.create');
    Route::post('provinces', [ProvinceController::class, 'store'])->name('provinces.store')->middleware('permission:provinces.create');
    Route::get('provinces/{province}/edit', [ProvinceController::class, 'edit'])->name('provinces.edit')->middleware('permission:provinces.edit');
    Route::put('provinces/{province}', [ProvinceController::class, 'update'])->name('provinces.update')->middleware('permission:provinces.edit');
    Route::delete('provinces/{province}', [ProvinceController::class, 'destroy'])->name('provinces.destroy')->middleware('permission:provinces.delete');

    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:users.view');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users.create');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users.edit');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');

    // Permission Routes
    Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::put('permissions/{user}', [PermissionController::class, 'update'])->name('permissions.update');

    // Debardeurs routes
    Route::get('debardeurs', [DebardeurController::class, 'index'])->name('debardeurs.index')->middleware('permission:debardeurs.view');
    Route::post('debardeurs', [DebardeurController::class, 'store'])->name('debardeurs.store')->middleware('permission:debardeurs.create');
    Route::get('debardeurs/{debardeur}', [DebardeurController::class, 'show'])->name('debardeurs.show')->middleware('permission:debardeurs.view');
    Route::put('debardeurs/{debardeur}', [DebardeurController::class, 'update'])->name('debardeurs.update')->middleware('permission:debardeurs.edit');
    Route::delete('debardeurs/{debardeur}', [DebardeurController::class, 'destroy'])->name('debardeurs.destroy')->middleware('permission:debardeurs.delete');
    Route::get('debardeurs/sector/{sector}', [DebardeurController::class, 'index'])->name('debardeurs.sector')->middleware('permission:debardeurs.view');

    // Engins Routes
    Route::get('engins', [EnginController::class, 'index'])->name('engins.index')->middleware('permission:engins.view');
    Route::post('engins', [EnginController::class, 'store'])->name('engins.store')->middleware('permission:engins.create');
    Route::get('engins/{engin}', [EnginController::class, 'show'])->name('engins.show')->middleware('permission:engins.view');
    Route::put('engins/{engin}', [EnginController::class, 'update'])->name('engins.update')->middleware('permission:engins.edit');
    Route::delete('engins/{engin}', [EnginController::class, 'destroy'])->name('engins.destroy')->middleware('permission:engins.delete');
    Route::get('/engins/category/{category}', [EnginController::class, 'index'])->name('engins.category')->middleware('permission:engins.view');

    // Conducteurs Routes
    Route::get('conducteurs', [ConducteurController::class, 'index'])->name('conducteurs.index')->middleware('permission:conducteurs.view');
    Route::post('conducteurs', [ConducteurController::class, 'store'])->name('conducteurs.store')->middleware('permission:conducteurs.create');
    Route::get('conducteurs/{conducteur}', [ConducteurController::class, 'show'])->name('conducteurs.show')->middleware('permission:conducteurs.view');
    Route::put('conducteurs/{conducteur}', [ConducteurController::class, 'update'])->name('conducteurs.update')->middleware('permission:conducteurs.edit');
    Route::delete('conducteurs/{conducteur}', [ConducteurController::class, 'destroy'])->name('conducteurs.destroy')->middleware('permission:conducteurs.delete');
    Route::get('conducteurs/search-engin/{idNumber}', [ConducteurController::class, 'searchEngin'])->name('conducteurs.search-engin')->middleware('permission:conducteurs.view');
    Route::get('conducteurs/sector/{sector}', [ConducteurController::class, 'index'])->name('conducteurs.sector')->middleware('permission:conducteurs.view');

    // Permis Routes (read-only)
    Route::get('permis', [PermisController::class, 'index'])->name('permis.index')->middleware('permission:permis.view');
    Route::get('permis/{permis}', [PermisController::class, 'show'])->name('permis.show')->middleware('permission:permis.view');

    // Certificats Routes (read-only)
    Route::get('certificats', [CertificatController::class, 'index'])->name('certificats.index')->middleware('permission:certificats.view');
    Route::get('certificats/{certificat}', [CertificatController::class, 'show'])->name('certificats.show')->middleware('permission:certificats.view');

    // Finance Routes
    Route::get('taxes', [TaxeController::class, 'index'])->name('taxes.index')->middleware('permission:taxes.view');
    Route::post('taxes', [TaxeController::class, 'store'])->name('taxes.store')->middleware('permission:taxes.create');
    Route::get('taxes/{taxe}', [TaxeController::class, 'show'])->name('taxes.show')->middleware('permission:taxes.view');
    Route::put('taxes/{taxe}', [TaxeController::class, 'update'])->name('taxes.update')->middleware('permission:taxes.edit');
    Route::delete('taxes/{taxe}', [TaxeController::class, 'destroy'])->name('taxes.destroy')->middleware('permission:taxes.delete');

    Route::get('paiements', [PaiementController::class, 'index'])->name('paiements.index')->middleware('permission:paiements.view');
    Route::post('paiements', [PaiementController::class, 'store'])->name('paiements.store')->middleware('permission:paiements.create');
    Route::get('paiements/{paiement}', [PaiementController::class, 'show'])->name('paiements.show')->middleware('permission:paiements.view');
    Route::delete('paiements/{paiement}', [PaiementController::class, 'destroy'])->name('paiements.destroy')->middleware('permission:paiements.delete');
    Route::get('/paiements/{paiement}/print', [PaiementController::class, 'print'])->name('paiements.print')->middleware('permission:paiements.view');

    // Annonces Routes
    Route::get('annonces', [AnnonceController::class, 'index'])->name('annonces.index')->middleware('permission:annonces.view');
    Route::post('annonces', [AnnonceController::class, 'store'])->name('annonces.store')->middleware('permission:annonces.create');
    Route::put('annonces/{annonce}', [AnnonceController::class, 'update'])->name('annonces.update')->middleware('permission:annonces.edit');
    Route::delete('annonces/{annonce}', [AnnonceController::class, 'destroy'])->name('annonces.destroy')->middleware('permission:annonces.delete');
});

require __DIR__.'/settings.php';
