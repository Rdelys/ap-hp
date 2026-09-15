<?php

namespace App\Models;

use App\Enums\RoleUtilisateur;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'role', 'etablissement_id', 'service_id', 'actif',
        'mot_de_passe_defini',
        'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RoleUtilisateur::class,
            'actif' => 'boolean',
            'derniere_connexion_at' => 'datetime',
            'verrouille_jusqu_a' => 'datetime',
            'mot_de_passe_defini' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class, 'demandeur_id');
    }

    public function hasRole(RoleUtilisateur $role): bool
    {
        return $this->role === $role;
    }

    public function estVerrouille(): bool
    {
        return $this->verrouille_jusqu_a && $this->verrouille_jusqu_a->isFuture();
    }
}