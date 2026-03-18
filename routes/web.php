<?php

use App\Http\Controllers\FamilleController;
use Illuminate\Support\Facades\Route;

// Page d'accueil → splash screen
Route::get('/', function () {
    return view('welcome');
});

// ── Zone familiale ────────────────────────────────────────────
Route::prefix('famille')->name('famille.')->group(function () {

    // Sélection du profil (pas besoin d'être connecté)
    Route::get('/', [FamilleController::class, 'index'])
         ->name('index');

    // Saisie du PIN
    Route::get('/pin/{profile}',  [FamilleController::class, 'showPin'])
         ->name('pin.show');
    Route::post('/pin/{profile}', [FamilleController::class, 'verifyPin'])
         ->name('pin.verify');

    // Déconnexion du profil
    Route::post('/logout', [FamilleController::class, 'logout'])
         ->name('logout');

    // Pages protégées par session de profil
    Route::middleware('profile.session')->group(function () {
        Route::get('/home',          [FamilleController::class, 'home'])
             ->name('home');
        Route::get('/mediatheque',   [FamilleController::class, 'mediatheque'])
             ->name('mediatheque');
        Route::get('/mes-prets',     [FamilleController::class, 'mesPrets'])
             ->name('prets');
        Route::get('/historique',    [FamilleController::class, 'historique'])
             ->name('historique');
        Route::get('/collections',   [FamilleController::class, 'collections'])
             ->name('collections');

        // Suggestions
        Route::get('/suggestion',    [FamilleController::class, 'suggestionForm'])
             ->name('suggestion.form');
        Route::post('/suggestion',   [FamilleController::class, 'suggestionStore'])
             ->name('suggestion.store');
        
        /**
         * Avis et critiques
         */
        Route::post('/review/{item}', [FamilleController::class, 'storeReview'])
               ->name('review.store');
    });
});