<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Document;
use App\Models\JournalAudit;
use App\Services\AssistantTranscriptionIA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\GouvernanceIA;

class TranscriptionController extends Controller
{
    /** Liste des dossiers disponibles pour transcription (statut en_file). */
    public function index()
    {
        $demandes = Demande::where(function ($q) {
                $q->whereIn('statut', ['depose', 'en_file', 'renvoye_correction'])
                ->orWhere(function ($q2) {
                    $q2->where('statut', 'en_transcription')->where('operateur_id', auth()->id());
                });
            })
            ->orderByRaw("FIELD(niveau_urgence, 'urgent', 'normal', 'economique')")
            ->orderBy('echeance_sla')
            ->with(['service', 'etablissement'])
            ->paginate(20);

        return view('transcription.index', compact('demandes'));
    }
    
    /** Ouvre le poste de transcription pour un dossier donné. */
    public function edit(Demande $demande)
    {
        if (! in_array($demande->statut, ['en_file', 'en_transcription', 'renvoye_correction'])) {
            abort(403, "Ce dossier n'est plus disponible pour transcription.");
        }

        if (in_array($demande->statut, ['en_file', 'renvoye_correction'])) {
            $demande->update(['statut' => 'en_transcription', 'operateur_id' => auth()->id()]);
            JournalAudit::tracer('debut_transcription', $demande, ['operateur' => auth()->user()->name]);
        }

        $audio = $demande->documents()->where('type', 'audio_source')->first();
        $transcription = $demande->documents()->where('type', 'transcription')->latest('version')->first();

        return view('transcription.edit', [
            'demande' => $demande,
            'audioUrl' => $audio ? route('transcription.audio', $demande) : null,
            'texteActuel' => $transcription ? app(\App\Services\StockageChiffre::class)->lire($transcription->chemin_stockage) : '',
        ]);
    }

    /** Diffuse le fichier audio de façon sécurisée (jamais d'URL publique directe). */
    public function audio(Demande $demande)
    {
        $audio = $demande->documents()->where('type', 'audio_source')->firstOrFail();

        JournalAudit::tracer('lecture_audio', $demande);

        return Storage::disk('documents_prives')->response($audio->chemin_stockage);
    }

    /** Sauvegarde manuelle (brouillon) du texte transcrit. */
    public function sauvegarder(Request $request, Demande $demande)
    {
        $validated = $request->validate([
            'texte' => ['required', 'string'],
        ]);

        $this->enregistrerVersionTranscription($demande, $validated['texte']);

        JournalAudit::tracer('sauvegarde_transcription', $demande);

        return response()->json(['succes' => true]);
    }

    /** Envoie le texte courant à l'assistant IA pour correction/structuration. */
    public function assisterIA(Request $request, Demande $demande, AssistantTranscriptionIA $assistant, GouvernanceIA $gouvernance)
    {
        $validated = $request->validate([
            'texte' => ['required', 'string'],
        ]);

        if (! $gouvernance->estAutoriseePour($demande)) {
            return response()->json([
                'succes' => false,
                'erreur' => "L'assistance IA est désactivée pour ce type de document ou ce service par l'AP-HP.",
            ], 403);
        }

        $resultat = $assistant->corrigerEtStructurer($validated['texte'], $demande->type_document);

        JournalAudit::tracer('assistance_ia_transcription', $demande, [
            'succes' => $resultat['succes'],
            'outil' => 'Claude (Anthropic)',
        ]);

        if ($resultat['succes']) {
            $demande->update(['mode_production' => 'hybride']);
        }

        return response()->json($resultat);
    }

    /** Termine la transcription et envoie le dossier en relecture. */
    public function terminer(Request $request, Demande $demande)
    {
        $validated = $request->validate([
            'texte' => ['required', 'string'],
        ]);

        $this->enregistrerVersionTranscription($demande, $validated['texte']);

        $demande->update(['statut' => 'en_relecture']);

        JournalAudit::tracer('transcription_terminee', $demande, ['mode_production' => $demande->mode_production]);

        return redirect()->route('transcription.index')
            ->with('succes', "Dossier {$demande->reference} envoyé en relecture.");
    }

    private function enregistrerVersionTranscription(Demande $demande, string $texte): void
    {
        $dernierNumero = $demande->documents()->where('type', 'transcription')->max('version') ?? 0;
        $nomFichier = $demande->reference.'_v'.($dernierNumero + 1).'.txt';
        $chemin = 'transcriptions/'.$demande->reference.'/'.$nomFichier;

        app(\App\Services\StockageChiffre::class)->ecrire($chemin, $texte);

        Document::create([
            'demande_id' => $demande->id,
            'type' => 'transcription',
            'nom_fichier' => $nomFichier,
            'chemin_stockage' => $chemin,
            'format' => 'txt',
            'chiffre' => true,
            'taille_octets' => strlen($texte),
            'version' => $dernierNumero + 1,
        ]);
    }
}