<?php

namespace App\Services;

use App\Models\Demande;
use App\Models\RestrictionIAParType;

class GouvernanceIA
{
    /** Vérifie si l'IA est autorisée pour une demande donnée, en tenant compte de la granularité par type de document. */
    public function estAutoriseePour(Demande $demande): bool
    {
        if (! $demande->service->ia_autorisee) {
            return false; // désactivation globale du service = prioritaire
        }

        $restriction = RestrictionIAParType::where('service_id', $demande->service_id)
            ->where('type_document', $demande->type_document)
            ->first();

        return $restriction ? $restriction->ia_autorisee : true;
    }
}