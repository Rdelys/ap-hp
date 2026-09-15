<?php

namespace App\View\Composers;

use App\Models\Demande;
use Illuminate\View\View;
use App\Models\TicketSupport;

class NavigationComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();

        if (! $user) {
            $view->with('menuItems', []);
            return;
        }

        $items = collect(config('menu'))
            ->filter(fn ($item) => in_array($user->role->value, $item['roles'], true))
            ->map(function ($item) use ($user) {
                $item['route_name'] = $item['route_dashboard'] ?? false
                    ? $user->role->routeDashboard()
                    : ($item['route'] ?? null);

                $item['badge_count'] = $this->resolveBadge($item['badge'] ?? null, $user);

                return $item;
            })
            ->values();

        $view->with('menuItems', $items);
    }

    private function resolveBadge(?string $type, $user): ?int
    {
        return match ($type) {
            'mes_demandes' => $this->compter(
                Demande::where('service_id', $user->service_id)
                    ->whereNotIn('statut', ['restitue'])
            ),

            'file_traitement' => $this->compter(
                Demande::whereNotIn('statut', ['restitue'])
            ),

            'a_transcrire' => $this->compter(
                Demande::where(function ($q) use ($user) {
                    $q->whereIn('statut', ['en_file', 'renvoye_correction'])
                      ->orWhere(function ($q2) use ($user) {
                          $q2->where('statut', 'en_transcription')->where('operateur_id', $user->id);
                      });
                })
            ),

            'a_relire' => $this->compter(
                Demande::where('statut', 'en_relecture')
            ),

            'a_valider' => $this->compter(
                Demande::where('statut', 'en_validation')
            ),

            'a_restituer' => $this->compter(
                Demande::where('statut', 'valide')->where('verrouille', true)
            ),

            'tickets_ouverts' => $this->compter(
                in_array($user->role->value, ['operateur_titulaire', 'relecteur_valideur', 'admin_titulaire', 'admin_aphp'], true)
                    ? TicketSupport::whereIn('statut', ['ouvert', 'en_cours'])
                    : TicketSupport::where('demandeur_id', $user->id)->whereIn('statut', ['ouvert', 'en_cours'])
            ),

            default => null,
        };
    }

    private function compter($query): ?int
    {
        $count = $query->count();

        return $count > 0 ? $count : null;
    }
}