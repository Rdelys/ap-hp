<?php

namespace App\Services\Transcription;

use App\Contracts\TranscripteurAutomatique;

class TranscripteurSimule implements TranscripteurAutomatique
{
    public function transcrire(string $cheminAudioAbsolu): array
    {
        $texte = "[Transcription simulée — aucun fournisseur de reconnaissance vocale configuré]\n\n"
            ."Ce texte est un espace réservé généré automatiquement, le temps de configurer "
            ."le service de transcription (OVHcloud AI Endpoints / Whisper). "
            ."L'opérateur doit remplacer ce contenu par la transcription réelle de l'audio ci-dessus.";

        return ['succes' => true, 'texte' => $texte, 'erreur' => null];
    }
}