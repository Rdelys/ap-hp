<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

/**
 * Chiffre/déchiffre le contenu textuel avant écriture sur disque (transcriptions, documents finaux).
 * Utilise la clé applicative (APP_KEY) via le chiffreur natif de Laravel (AES-256-CBC).
 * Volontairement réservé aux fichiers texte/documents — voir note Sprint 12 sur l'audio.
 */
class StockageChiffre
{
    public function ecrire(string $chemin, string $contenu, string $disque = 'documents_prives'): bool
    {
        return Storage::disk($disque)->put($chemin, Crypt::encryptString($contenu));
    }

    public function lire(string $chemin, string $disque = 'documents_prives'): string
    {
        return Crypt::decryptString(Storage::disk($disque)->get($chemin));
    }
}