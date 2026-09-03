<?php

namespace App\Enums;

enum RoleUtilisateur: string
{
    case SecretariatMedical = 'secretariat_medical';
    case ReferentService = 'referent_service';
    case AdminAphp = 'admin_aphp';
    case OperateurTitulaire = 'operateur_titulaire';
    case RelecteurValideur = 'relecteur_valideur';
    case AdminTitulaire = 'admin_titulaire';

    public function libelle(): string
    {
        return match ($this) {
            self::SecretariatMedical => 'Secrétariat médical',
            self::ReferentService => 'Référent de service',
            self::AdminAphp => 'Administrateur AP-HP',
            self::OperateurTitulaire => 'Opérateur (titulaire)',
            self::RelecteurValideur => 'Relecteur / Valideur',
            self::AdminTitulaire => 'Administrateur titulaire',
        };
    }

    /** Route du tableau de bord associé à ce rôle (layout top navigation). */
    public function routeDashboard(): string
    {
        return match ($this) {
            self::SecretariatMedical => 'dashboard.secretariat',
            self::ReferentService => 'dashboard.referent',
            self::AdminAphp => 'dashboard.admin-aphp',
            self::OperateurTitulaire => 'dashboard.operateur',
            self::RelecteurValideur => 'dashboard.relecteur',
            self::AdminTitulaire => 'dashboard.admin-titulaire',
        };
    }
}