<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\JournalAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RelectureController extends Controller
{
    public function index()
    {
        $demandes = Demande::where('statut', 'en_relecture')
            ->orderByRaw("FIELD(niveau_urgence, 'urgent', 'normal', 'economique')")
            ->orderBy('echeance_sla')
            ->with(['service', 'etablissement'])
            ->paginate(20);

        return view('relecture.index', compact('demandes'));
    }

    public function edit(Demande $demande)
    {
        if ($demande->statut !== 'en_relecture') {
            abort(403, "Ce dossier n'est pas (ou plus) en relecture.");
        }

        if ($demande->verrouille) {
            abort(403, "Ce dossier est verrouillé suite à validation humaine. Une réouverture tracée est nécessaire.");
        }

        $audio = $demande->documents()->where('type', 'audio_source')->first();
        $transcription = $demande->documents()->where('type', 'transcription')->latest('version')->first();

        return view('relecture.edit', [
            'demande' => $demande,
            'audioUrl' => $audio ? route('transcription.audio', $demande) : null,
            'texteActuel' => $transcription ? Storage::disk('documents_prives')->get($transcription->chemin_stockage) : '',
        ]);
    }

    /** Enregistre les corrections du relecteur (nouvelle version du document transcription). */
    public function sauvegarder(Request $request, Demande $demande)
    {
        $validated = $request->validate([
            'texte' => ['required', 'string'],
        ]);

        if ($demande->verrouille) {
            abort(403, "Ce dossier est verrouillé suite à validation humaine. Une réouverture tracée est nécessaire.");
        }

        $this->enregistrerVersion($demande, $validated['texte']);

        JournalAudit::tracer('sauvegarde_relecture', $demande);

        return response()->json(['succes' => true]);
    }

    /** Le relecteur juge le dossier prêt : il part vers la validation humaine (Sprint 5). */
    public function envoyerValidation(Request $request, Demande $demande)
    {
        $validated = $request->validate([
            'texte' => ['required', 'string'],
            'signalement_anomalie' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($demande->verrouille) {
            abort(403, "Ce dossier est verrouillé suite à validation humaine. Une réouverture tracée est nécessaire.");
        }
        
        $this->enregistrerVersion($demande, $validated['texte']);

        $demande->update([
            'statut' => 'en_validation',
            'relecteur_id' => auth()->id(),
            'relu_le' => now(),
            'signalement_anomalie' => $validated['signalement_anomalie'] ?? null,
        ]);

        JournalAudit::tracer('relecture_terminee', $demande, [
            'relecteur' => auth()->user()->name,
            'anomalie_signalee' => ! empty($validated['signalement_anomalie']),
        ]);

        return redirect()->route('relecture.index')
            ->with('succes', "Dossier {$demande->reference} envoyé en validation.");
    }

    /** Le relecteur juge le dossier insuffisant : retour à l'opérateur pour correction. */
    public function renvoyerCorrection(Request $request, Demande $demande)
    {
        $validated = $request->validate([
            'motif_renvoi' => ['required', 'string', 'max:2000'],
        ]);

        $demande->update([
            'statut' => 'renvoye_correction',
            'operateur_id' => null, // libère le dossier pour n'importe quel opérateur
        ]);

        JournalAudit::tracer('renvoi_correction', $demande, [
            'relecteur' => auth()->user()->name,
            'motif' => $validated['motif_renvoi'],
        ]);

        return redirect()->route('relecture.index')
            ->with('succes', "Dossier {$demande->reference} renvoyé en correction.");
    }

    private function enregistrerVersion(Demande $demande, string $texte): void
    {
        $dernierNumero = $demande->documents()->where('type', 'transcription')->max('version') ?? 0;
        $nomFichier = $demande->reference.'_v'.($dernierNumero + 1).'.txt';
        $chemin = 'transcriptions/'.$demande->reference.'/'.$nomFichier;

        Storage::disk('documents_prives')->put($chemin, $texte);

        \App\Models\Document::create([
            'demande_id' => $demande->id,
            'type' => 'transcription',
            'nom_fichier' => $nomFichier,
            'chemin_stockage' => $chemin,
            'format' => 'txt',
            'taille_octets' => strlen($texte),
            'version' => $dernierNumero + 1,
        ]);
    }
}