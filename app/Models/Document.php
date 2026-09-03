<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'demande_id', 'type', 'nom_fichier', 'chemin_stockage',
        'format', 'taille_octets', 'version', 'chiffre',
    ];

    protected $casts = ['chiffre' => 'boolean'];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }
}