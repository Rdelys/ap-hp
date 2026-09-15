<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemandeController;
use App\Http\Controllers\FileTraitementController;
use App\Http\Controllers\TranscriptionController;
use App\Http\Controllers\RelectureController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\RestitutionController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\Admin\UtilisateurController;
use App\Http\Controllers\Admin\EtablissementController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\GouvernanceIAController;

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

    Route::middleware('role:relecteur_valideur')->group(function () {
        Route::get('/relecture', [RelectureController::class, 'index'])->name('relecture.index');
        Route::get('/relecture/{demande}', [RelectureController::class, 'edit'])->name('relecture.edit');
        Route::post('/relecture/{demande}/sauvegarder', [RelectureController::class, 'sauvegarder'])->name('relecture.sauvegarder');
        Route::post('/relecture/{demande}/valider', [RelectureController::class, 'envoyerValidation'])->name('relecture.envoyer-validation');
        Route::post('/relecture/{demande}/renvoyer', [RelectureController::class, 'renvoyerCorrection'])->name('relecture.renvoyer-correction');
    });

    // Retirez la route "audio" du groupe restreint à operateur_titulaire, placez-la ainsi :
    Route::middleware('role:operateur_titulaire,relecteur_valideur')->group(function () {
        Route::get('/transcription/{demande}/audio', [TranscriptionController::class, 'audio'])->name('transcription.audio');
    });

    Route::middleware('role:relecteur_valideur')->group(function () {
        Route::get('/validation', [ValidationController::class, 'index'])->name('validation.index');
        Route::get('/validation/{demande}', [ValidationController::class, 'show'])->name('validation.show');
        Route::post('/validation/{demande}/valider', [ValidationController::class, 'valider'])->name('validation.valider');
        Route::post('/validation/{demande}/reouvrir', [ValidationController::class, 'reouvrir'])->name('validation.reouvrir');
    });

    Route::middleware('role:relecteur_valideur,admin_titulaire')->group(function () {
    Route::get('/restitution/a-generer', [RestitutionController::class, 'aGenerer'])->name('restitution.a-generer');
    Route::post('/restitution/{demande}/generer', [RestitutionController::class, 'generer'])->name('restitution.generer');
    });

    Route::middleware('auth')->group(function () {
        Route::get('/restitution', [RestitutionController::class, 'index'])->name('restitution.index');
        Route::get('/restitution/{demande}/telecharger', [RestitutionController::class, 'telecharger'])->name('restitution.telecharger');
    });

    Route::middleware('role:admin_titulaire,admin_aphp')->group(function () {
        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    });

    Route::middleware('role:admin_aphp,referent_service,admin_titulaire')->group(function () {
        Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
        Route::get('/rapports/export', [RapportController::class, 'exporterCsv'])->name('rapports.export');
    });

    Route::middleware(['signed'])->group(function () {
        Route::get('/invitation/{user}', [InvitationController::class, 'formulaire'])->name('invitation.formulaire');
        Route::post('/invitation/{user}', [InvitationController::class, 'definir'])->name('invitation.definir');
    });

    Route::middleware('role:admin_titulaire')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('utilisateurs', UtilisateurController::class)->except(['show', 'destroy']);
    Route::post('utilisateurs/{utilisateur}/basculer-activation', [UtilisateurController::class, 'basculerActivation'])->name('utilisateurs.basculer-activation');
    Route::post('utilisateurs/{utilisateur}/renvoyer-invitation', [UtilisateurController::class, 'renvoyerInvitation'])->name('utilisateurs.renvoyer-invitation');

    Route::resource('etablissements', EtablissementController::class)->except(['show', 'destroy']);
        Route::resource('services', ServiceController::class)->except(['show', 'destroy']);
    });

    Route::middleware('role:admin_aphp,admin_titulaire')->prefix('admin')->name('admin.')->group(function () {
        Route::get('gouvernance-ia', [GouvernanceIAController::class, 'index'])->name('gouvernance-ia.index');
        Route::post('gouvernance-ia/{service}/basculer-service', [GouvernanceIAController::class, 'basculerService'])->name('gouvernance-ia.basculer-service');
        Route::post('gouvernance-ia/{service}/basculer-type', [GouvernanceIAController::class, 'basculerType'])->name('gouvernance-ia.basculer-type');
    });
});

