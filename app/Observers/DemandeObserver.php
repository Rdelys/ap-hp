<?php

namespace App\Observers;

use App\Models\Demande;
use App\Models\JournalAudit;

class DemandeObserver
{
    /** Dès la création (dépôt + accusé de réception), la demande entre en file d'attente. */
    public function created(Demande $demande): void
    {
        $demande->update(['statut' => 'en_file']);

        JournalAudit::tracer('entree_file_traitement', $demande, [
            'reference' => $demande->reference,
            'niveau_urgence' => $demande->niveau_urgence,
        ]);
    }
}