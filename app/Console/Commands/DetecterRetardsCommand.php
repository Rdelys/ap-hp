<?php

namespace App\Console\Commands;

use App\Models\Demande;
use App\Models\JournalAudit;
use Illuminate\Console\Command;

class DetecterRetardsCommand extends Command
{
    protected $signature = 'demandes:detecter-retards';
    protected $description = "Marque comme 'hors_delai' les demandes ayant dépassé leur échéance SLA sans restitution.";

    public function handle(): int
    {
        $demandes = Demande::where('echeance_sla', '<', now())
            ->whereNotIn('statut', ['restitue', 'hors_delai'])
            ->get();

        foreach ($demandes as $demande) {
            $demande->update(['statut' => 'hors_delai']);
            JournalAudit::tracer('depassement_sla', $demande, ['reference' => $demande->reference]);
        }

        $this->info("{$demandes->count()} demande(s) marquée(s) hors délai.");

        return self::SUCCESS;
    }
}