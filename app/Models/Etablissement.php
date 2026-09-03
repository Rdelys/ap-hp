<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    protected $fillable = ['nom', 'code', 'adresse', 'actif'];

    protected $casts = ['actif' => 'boolean'];

    public function services()
    {
        return $this->hasMany(Service::class);
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