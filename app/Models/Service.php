<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'etablissement_id', 'nom', 'referent_nom', 'referent_email',
        'referent_telephone', 'modele_documentaire_path', 'regle_nommage',
        'ia_autorisee', 'actif',
    ];

    protected $casts = [
        'ia_autorisee' => 'boolean',
        'actif' => 'boolean',
    ];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class);
    }
}