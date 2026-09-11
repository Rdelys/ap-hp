<?php

namespace App\Services\Transcription;

use App\Contracts\TranscripteurAutomatique;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranscripteurOvhWhisper implements TranscripteurAutomatique
{
    public function transcrire(string $cheminAudioAbsolu): array
    {
        if (! file_exists($cheminAudioAbsolu)) {
            return ['succes' => false, 'texte' => null, 'erreur' => 'Fichier audio introuvable.'];
        }

        try {
            $response = Http::withToken(config('ovhcloud.token'))
                ->timeout(config('ovhcloud.timeout'))
                ->attach('file', file_get_contents($cheminAudioAbsolu), basename($cheminAudioAbsolu))
                ->post(config('ovhcloud.endpoint'), [
                    'model' => config('ovhcloud.model'),
                    'language' => config('ovhcloud.langue'),
                ]);

            if ($response->failed()) {
                Log::error('Erreur transcription OVHcloud', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return ['succes' => false, 'texte' => null, 'erreur' => 'Le service de transcription est momentanément indisponible.'];
            }

            $texte = $response->json('text');

            if (! $texte) {
                return ['succes' => false, 'texte' => null, 'erreur' => 'Réponse vide du service de transcription.'];
            }

            return ['succes' => true, 'texte' => $texte, 'erreur' => null];

        } catch (\Throwable $e) {
            Log::error('Exception transcription OVHcloud', ['message' => $e->getMessage()]);
            return ['succes' => false, 'texte' => null, 'erreur' => 'Erreur technique lors de la transcription automatique.'];
        }
    }
}