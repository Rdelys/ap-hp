<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Support\CalculateurSla;

class Demande extends Model
{
    protected $fillable = [
        'reference', 'etablissement_id', 'service_id', 'demandeur_id',
        'operateur_id', 'relecteur_id', 'relu_le',
        'type_document', 'nom_demandeur', 'numero_dictant',
        'niveau_urgence', 'statut', 'mode_production',
        'date_depot', 'echeance_sla', 'date_restitution', 'signalement_anomalie',
    ];

    protected $casts = [
        'date_depot' => 'datetime',
        'echeance_sla' => 'datetime',
        'date_restitution' => 'datetime',
        'relu_le' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Demande $demande) {
            $demande->reference ??= (string) Str::uuid();
            $demande->date_depot ??= now();
            $demande->echeance_sla ??= $demande->calculerEcheanceSla();
        });
    }

    public function calculerEcheanceSla(): \Carbon\Carbon
    {
        $heures = match ($this->niveau_urgence) {
            'urgent' => 2,
            'economique' => 72,
            default => 24,
        };

        return CalculateurSla::echeance(now(), $heures);
    }

    /** Une demande est en retard si l'échéance est dépassée et qu'elle n'a pas encore été restituée. */
    public function estEnRetard(): bool
    {
        return $this->echeance_sla && $this->echeance_sla->isPast() && $this->statut !== 'restitue';
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

    public function operateur()
    {
        return $this->belongsTo(User::class, 'operateur_id');
    }

    public function relecteur()
    {
        return $this->belongsTo(User::class, 'relecteur_id');
    }
}