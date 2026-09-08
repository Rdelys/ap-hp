<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\FileTraitementController;
use App\Http\Controllers\TranscriptionController;

Route::get('/', fn () => redirect()->route('login'));

// --- Authentification ---
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])
        ->middleware('throttle:10,1'); // limite globale niveau route en complément du RateLimiter applicatif
});

Route::post('/deconnexion', [AuthController::class, 'logout'])
    ->middleware('auth')->name('logout');

// --- Tableaux de bord par rôle (RBAC) ---
Route::middleware('auth')->group(function () {

    Route::get('/dashboard/secretariat', [DashboardController::class, 'secretariat'])
        ->middleware('role:secretariat_medical')->name('dashboard.secretariat');

    Route::get('/dashboard/referent', [DashboardController::class, 'referent'])
        ->middleware('role:referent_service')->name('dashboard.referent');

    Route::get('/dashboard/admin-aphp', [DashboardController::class, 'adminAphp'])
        ->middleware('role:admin_aphp')->name('dashboard.admin-aphp');

    Route::get('/dashboard/operateur', [DashboardController::class, 'operateur'])
        ->middleware('role:operateur_titulaire')->name('dashboard.operateur');

    Route::get('/dashboard/relecteur', [DashboardController::class, 'relecteur'])
        ->middleware('role:relecteur_valideur')->name('dashboard.relecteur');

    Route::get('/dashboard/admin-titulaire', [DashboardController::class, 'adminTitulaire'])
        ->middleware('role:admin_titulaire')->name('dashboard.admin-titulaire');

    Route::middleware('role:secretariat_medical,referent_service')->group(function () {
        Route::get('/demandes', [DemandeController::class, 'index'])->name('demandes.index');
        Route::get('/demandes/creer', [DemandeController::class, 'create'])->name('demandes.create');
        Route::post('/demandes', [DemandeController::class, 'store'])->name('demandes.store');
    });

    Route::get('/demandes/{demande}', [DemandeController::class, 'show'])
    ->middleware('auth')->name('demandes.show');
Route::middleware('role:operateur_titulaire')->group(function () {
    Route::get('/transcription', [TranscriptionController::class, 'index'])->name('transcription.index');
    Route::get('/transcription/{demande}', [TranscriptionController::class, 'edit'])->name('transcription.edit');
    Route::get('/transcription/{demande}/audio', [TranscriptionController::class, 'audio'])->name('transcription.audio');
    Route::post('/transcription/{demande}/sauvegarder', [TranscriptionController::class, 'sauvegarder'])->name('transcription.sauvegarder');
    Route::post('/transcription/{demande}/assister-ia', [TranscriptionController::class, 'assisterIA'])->name('transcription.assister-ia');
    Route::post('/transcription/{demande}/terminer', [TranscriptionController::class, 'terminer'])->name('transcription.terminer');
});

    Route::middleware('role:operateur_titulaire,relecteur_valideur,admin_titulaire')->group(function () {
    Route::get('/file-traitement', [FileTraitementController::class, 'index'])->name('file-traitement.index');
});

});

