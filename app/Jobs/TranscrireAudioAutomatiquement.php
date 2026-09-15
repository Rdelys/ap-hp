<?php

namespace App\Jobs;

use App\Contracts\TranscripteurAutomatique;
use App\Models\Demande;
use App\Models\Document;
use App\Models\JournalAudit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class TranscrireAudioAutomatiquement implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 180;

    public function __construct(public Demande $demande) {}

    public function handle(TranscripteurAutomatique $transcripteur): void
    {

        if (! $gouvernance->estAutoriseePour($this->demande)) {
            JournalAudit::tracer('transcription_automatique_bloquee', $this->demande, [
                'motif' => "IA désactivée par gouvernance pour ce service/type de document",
            ]);
            return; // reste en_file, transcription manuelle requise
        }
        
        $audio = $this->demande->documents()->where('type', 'audio_source')->first();

        if (! $audio) {
            JournalAudit::tracer('echec_transcription_automatique', $this->demande, [
                'motif' => 'Aucun fichier audio source trouvé.',
            ]);
            return;
        }

        $cheminAbsolu = Storage::disk('documents_prives')->path($audio->chemin_stockage);

        $resultat = $transcripteur->transcrire($cheminAbsolu);

        if (! $resultat['succes']) {
            JournalAudit::tracer('echec_transcription_automatique', $this->demande, [
                'erreur' => $resultat['erreur'],
            ]);
            return; // le dossier reste en_file, l'opérateur transcrira manuellement
        }

        $nomFichier = $this->demande->reference.'_v1.txt';
        $chemin = 'transcriptions/'.$this->demande->reference.'/'.$nomFichier;

        app(\App\Services\StockageChiffre::class)->ecrire($chemin, $resultat['texte']);

        Document::create([
            'demande_id' => $this->demande->id,
            'type' => 'transcription',
            'nom_fichier' => $nomFichier,
            'chemin_stockage' => $chemin,
            'format' => 'txt',
            'chiffre' => true,
            'taille_octets' => strlen($resultat['texte']),
            'version' => 1,
        ]);

        $this->demande->update(['mode_production' => 'assiste']);

        JournalAudit::tracer('transcription_automatique_terminee', $this->demande, [
            'fournisseur' => config('ovhcloud.token') ? 'OVHcloud Whisper' : 'Simulé (aucune clé configurée)',
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        JournalAudit::tracer('echec_transcription_automatique', $this->demande, [
            'exception' => $exception->getMessage(),
        ]);
    }
}