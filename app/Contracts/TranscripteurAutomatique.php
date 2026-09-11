<?php

namespace App\Contracts;

interface TranscripteurAutomatique
{
    /**
     * Transcrit un fichier audio en texte brut.
     *
     * @param string $cheminAudioAbsolu Chemin absolu vers le fichier audio sur disque.
     * @return array{succes: bool, texte: ?string, erreur: ?string}
     */
    public function transcrire(string $cheminAudioAbsolu): array;
}