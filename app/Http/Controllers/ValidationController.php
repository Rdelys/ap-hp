<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\JournalAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ValidationController extends Controller
{
    public function index()
    {
        $demandes = Demande::where('statut', 'en_validation')
            ->orderByRaw("FIELD(niveau_urgence, 'urgent', 'normal', 'economique')")
            ->orderBy('echeance_sla')
            ->with(['service', 'etablissement', 'relecteur'])
            ->paginate(20);

        return view('validation.index', compact('demandes'));
    }

    /** Affichage en lecture seule du dossier avant décision de validation. */
    public function show(Demande $demande)
    {
        if ($demande->statut !== 'en_validation') {
            abort(403, "Ce dossier n'est pas en attente de validation.");
        }

        $audio = $demande->documents()->where('type', 'audio_source')->first();
        $transcription = $demande->documents()->where('type', 'transcription')->latest('version')->first();

        return view('validation.show', [
            'demande' => $demande,
            'audioUrl' => $audio ? route('transcription.audio', $demande) : null,
            'texte' => $transcription ? Storage::disk('documents_prives')->get($transcription->chemin_stockage) : '',
        ]);
    }

    /**
     * Validation humaine définitive — point de blocage contractuel.
     * Verrouille le dossier : plus aucune modification de contenu possible sans réouverture tracée.
     */
    public function valider(Request $request, Demande $demande)
    {
        $request->validate([
            'confirmation' => ['required', 'accepted'],
        ]);

        if ($demande->statut !== 'en_validation') {
            abort(403, "Ce dossier n'est pas (ou plus) en attente de validation.");
        }

        $demande->update([
            'statut' => 'valide',
            'valide_par_id' => auth()->id(),
            'valide_le' => now(),
            'verrouille' => true,
        ]);

        JournalAudit::tracer('validation_humaine', $demande, [
            'valideur' => auth()->user()->name,
            'reference' => $demande->reference,
            'horodatage' => now()->toIso8601String(),
        ]);

        return redirect()->route('validation.index')
            ->with('succes', "Dossier {$demande->reference} validé et verrouillé — prêt pour restitution.");
    }

    /**
     * Réouverture tracée d'un dossier validé par erreur (avant restitution effective).
     * Réservée aux cas exceptionnels ; toute réouverture est journalisée avec motif obligatoire.
     */
    public function reouvrir(Request $request, Demande $demande)
    {
        $validated = $request->validate([
            'motif_reouverture' => ['required', 'string', 'max:2000'],
        ]);

        if ($demande->statut !== 'valide' || $demande->verrouille === false) {
            abort(403, "Ce dossier n'est pas dans un état permettant la réouverture.");
        }

        if ($demande->statut === 'restitue') {
            abort(403, "Un dossier déjà restitué ne peut pas être rouvert depuis cet écran.");
        }

        $demande->update([
            'statut' => 'en_relecture',
            'verrouille' => false,
            'valide_par_id' => null,
            'valide_le' => null,
        ]);

        JournalAudit::tracer('reouverture_dossier_valide', $demande, [
            'valideur_precedent_annule' => true,
            'motif' => $validated['motif_reouverture'],
            'demande_par' => auth()->user()->name,
        ]);

        return redirect()->route('validation.index')
            ->with('succes', "Dossier {$demande->reference} rouvert et renvoyé en relecture.");
    }
}