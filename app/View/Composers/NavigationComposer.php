<?php

namespace App\View\Composers;

use App\Models\Demande;
use Illuminate\View\View;

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
        if ($type === 'demandes_en_attente' && $user->service_id) {
            $count = Demande::where('service_id', $user->service_id)
                ->where('statut', 'depose')
                ->count();

            return $count > 0 ? $count : null;
        }

        if ($type === 'demandes_urgentes') {
            $count = Demande::whereNotIn('statut', ['restitue'])
                ->where('niveau_urgence', 'urgent')
                ->count();

            return $count > 0 ? $count : null;
        }

        return null;
    }
}