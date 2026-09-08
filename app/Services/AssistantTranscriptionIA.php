<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AssistantTranscriptionIA
{
    /**
     * Corrige et structure un texte déjà saisi manuellement.
     * ⚠️ N'effectue PAS de transcription audio — travaille uniquement sur du texte fourni.
     * ⚠️ À n'utiliser qu'avec des données fictives tant que la déclaration AP-HP / conformité HDS-RGPD
     *    n'est pas formalisée pour un usage en production (cf. art. 4-B-II du CCTP).
     */
    public function corrigerEtStructurer(string $texteBrut, string $typeDocument): array
    {
        $prompt = <<<PROMPT
        Tu es un assistant de relecture pour des comptes rendus médicaux français.
        Voici un brouillon de transcription (type : {$typeDocument}) saisi par un opérateur.

        Corrige l'orthographe, la grammaire et la ponctuation.
        Respecte la terminologie médicale standard.
        Ne modifie AUCUNE donnée médicale, chiffre, dosage, nom propre ou date.
        Si un passage te semble incertain ou incohérent, signale-le entre crochets [À VÉRIFIER: ...] sans le supprimer.
        Ne reformule pas le sens, corrige uniquement la forme.

        Texte à corriger :
        {$texteBrut}

        Réponds uniquement avec le texte corrigé, sans commentaire ni préambule.
        PROMPT;

        try {
            $response = Http::withHeaders([
                'x-api-key' => config('anthropic.api_key'),
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])
                ->timeout(config('anthropic.timeout'))
                ->post(config('anthropic.endpoint'), [
                    'model' => config('anthropic.model'),
                    'max_tokens' => 4096,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Erreur API Anthropic', ['status' => $response->status(), 'body' => $response->body()]);
                return ['succes' => false, 'erreur' => 'Le service IA est momentanément indisponible.'];
            }

            $texteCorrige = collect($response->json('content'))
                ->where('type', 'text')
                ->pluck('text')
                ->implode("\n");

            return ['succes' => true, 'texte' => $texteCorrige];

        } catch (\Throwable $e) {
            Log::error('Exception appel Anthropic', ['message' => $e->getMessage()]);
            return ['succes' => false, 'erreur' => 'Une erreur technique est survenue.'];
        }
    }
}