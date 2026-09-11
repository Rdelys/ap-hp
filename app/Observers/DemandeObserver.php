<?php

namespace App\Observers;

use App\Jobs\TranscrireAudioAutomatiquement;
use App\Models\Demande;
use App\Models\JournalAudit;

class DemandeObserver
{
    public function created(Demande $demande): void
    {
        $demande->update(['statut' => 'en_file']);

        JournalAudit::tracer('entree_file_traitement', $demande, [
            'reference' => $demande->reference,
            'niveau_urgence' => $demande->niveau_urgence,
        ]);

        TranscrireAudioAutomatiquement::dispatch($demande);

        JournalAudit::tracer('transcription_automatique_lancee', $demande);
    }
}