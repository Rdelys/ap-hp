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

        // ⚠️ Mots de passe de démo uniquement — à changer avant toute démo/prod réelle
        $comptes = [
            ['name' => 'Secrétariat Démo', 'email' => 'secretariat@demo.aphp', 'role' => RoleUtilisateur::SecretariatMedical],
            ['name' => 'Référent Démo', 'email' => 'referent@demo.aphp', 'role' => RoleUtilisateur::ReferentService],
            ['name' => 'Admin AP-HP Démo', 'email' => 'admin.aphp@demo.aphp', 'role' => RoleUtilisateur::AdminAphp],
            ['name' => 'Opérateur Démo', 'email' => 'operateur@demo.aphp', 'role' => RoleUtilisateur::OperateurTitulaire],
            ['name' => 'Relecteur Démo', 'email' => 'relecteur@demo.aphp', 'role' => RoleUtilisateur::RelecteurValideur],
            ['name' => 'Admin Titulaire Démo', 'email' => 'admin.titulaire@demo.aphp', 'role' => RoleUtilisateur::AdminTitulaire],
        ];

        foreach ($comptes as $compte) {
            User::create([
                'name' => $compte['name'],
                'email' => $compte['email'],
                'password' => Hash::make('MotDePasse!Demo2026'),
                'role' => $compte['role'],
                'etablissement_id' => $etablissement->id,
                'service_id' => $service->id,
                'actif' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}