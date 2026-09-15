<?php

namespace Database\Seeders;

use App\Enums\RoleUtilisateur;
use App\Models\Etablissement;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $etablissement = Etablissement::create([
            'nom' => 'Hôpital Cochin',
            'code' => 'COCHIN',
        ]);

        $service = Service::create([
            'etablissement_id' => $etablissement->id,
            'nom' => 'Service de Cardiologie',
            'referent_nom' => 'Dr. Martin',
            'referent_email' => 'martin@aphp.fr',
        ]);

        // ⚠️ Mot de passe de démo uniquement — à changer avant toute démo/prod réelle
        User::create([
            'name' => 'Admin Titulaire',
            'email' => 'admin.titulaire@demo.aphp',
            'password' => Hash::make('MotDePasse!Demo2026'),
            'role' => RoleUtilisateur::AdminTitulaire,
            'etablissement_id' => $etablissement->id,
            'service_id' => $service->id,
            'actif' => true,
            'mot_de_passe_defini' => true,
            'email_verified_at' => now(),
        ]);
    }
}