<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Demande extends Model
{
    protected $fillable = [
        'reference', 'etablissement_id', 'service_id', 'demandeur_id',
        'type_document', 'nom_demandeur', 'numero_dictant',
        'niveau_urgence', 'statut', 'mode_production',
        'date_depot', 'echeance_sla', 'date_restitution', 'signalement_anomalie',
    ];

    protected $casts = [
        'date_depot' => 'datetime',
        'echeance_sla' => 'datetime',
        'date_restitution' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Demande $demande) {
            $demande->reference ??= (string) Str::uuid();
            $demande->date_depot ??= now();
            $demande->echeance_sla ??= $demande->calculerEcheanceSla();
        });
    }

    /** SLA contractuel — art. 4-II-4 du CCTP lot 3. */
    public function calculerEcheanceSla(): \Carbon\Carbon
    {
        $heures = match ($this->niveau_urgence) {
            'urgent' => 2,
            'economique' => 72,
            default => 24,
        };

        return now()->addHours($heures); // NB: passer en jours/heures ouvrables lors du Sprint 2
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function demandeur()
    {
        return $this->belongsTo(User::class, 'demandeur_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}