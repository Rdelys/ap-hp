<?php

namespace App\Support;

use Carbon\Carbon;

class CalculateurSla
{
    /** Calcule l'échéance en heures ouvrables à partir d'un instant de départ. */
    public static function echeance(Carbon $depart, int $heuresOuvrables): Carbon
    {
        $config = config('sla');
        $curseur = self::prochainInstantOuvrable($depart->copy(), $config);
        $heuresRestantes = $heuresOuvrables;

        while ($heuresRestantes > 0) {
            $finJournee = $curseur->copy()->setTime($config['heure_fin'], 0);
            $heuresDisponiblesAujourdhui = max(0, $curseur->diffInMinutes($finJournee, false) / 60);

            if ($heuresRestantes <= $heuresDisponiblesAujourdhui) {
                $curseur->addMinutes((int) round($heuresRestantes * 60));
                $heuresRestantes = 0;
            } else {
                $heuresRestantes -= $heuresDisponiblesAujourdhui;
                $curseur = self::prochainJourOuvrable(
                    $curseur->copy()->addDay()->setTime($config['heure_debut'], 0),
                    $config
                );
            }
        }

        return $curseur;
    }

    private static function prochainInstantOuvrable(Carbon $date, array $config): Carbon
    {
        $date = self::prochainJourOuvrable($date, $config);

        if ($date->hour < $config['heure_debut']) {
            $date->setTime($config['heure_debut'], 0);
        } elseif ($date->hour >= $config['heure_fin']) {
            $date = self::prochainJourOuvrable($date->copy()->addDay()->setTime($config['heure_debut'], 0), $config);
        }

        return $date;
    }

    private static function prochainJourOuvrable(Carbon $date, array $config): Carbon
    {
        while (
            ! in_array($date->dayOfWeekIso, $config['jours_ouvrables'], true)
            || in_array($date->format('Y-m-d'), $config['jours_feries'], true)
        ) {
            $date->addDay()->setTime($config['heure_debut'], 0);
        }

        return $date;
    }
}