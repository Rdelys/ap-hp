<?php

return [

    [
        'label' => 'Tableau de bord',
        'route_dashboard' => true, // résolu dynamiquement selon le rôle
        'roles' => ['secretariat_medical', 'referent_service', 'admin_aphp', 'operateur_titulaire', 'relecteur_valideur', 'admin_titulaire'],
        'disponible' => true,
        'badge' => null,
    ],
    [
        'label' => 'Mes demandes',
        'route' => 'demandes.index',
        'roles' => ['secretariat_medical', 'referent_service'],
        'disponible' => true,
        'badge' => 'demandes_en_attente',
    ],
    [
        'label' => 'Déposer une demande',
        'route' => 'demandes.create',
        'roles' => ['secretariat_medical', 'referent_service'],
        'disponible' => true,
        'badge' => null,
    ],
    [
        'label' => 'File de traitement',
        'route' => 'file-traitement.index',
        'roles' => ['operateur_titulaire', 'relecteur_valideur', 'admin_titulaire'],
        'disponible' => true,
        'badge' => 'demandes_urgentes',
    ],
    [
        'label' => 'Transcription',
        'route' => null,
        'roles' => ['operateur_titulaire'],
        'disponible' => false,
        'sprint' => 3,
        'badge' => null,
    ],
    [
        'label' => 'Relecture & contrôle qualité',
        'route' => null,
        'roles' => ['relecteur_valideur'],
        'disponible' => false,
        'sprint' => 4,
        'badge' => null,
    ],
    [
        'label' => 'Validation',
        'route' => null,
        'roles' => ['relecteur_valideur'],
        'disponible' => false,
        'sprint' => 5,
        'badge' => null,
    ],
    [
        'label' => 'Documents restitués',
        'route' => null,
        'roles' => ['secretariat_medical', 'referent_service'],
        'disponible' => false,
        'sprint' => 6,
        'badge' => null,
    ],
    [
        'label' => 'Suivi & reporting',
        'route' => null,
        'roles' => ['admin_aphp', 'referent_service', 'admin_titulaire'],
        'disponible' => false,
        'sprint' => 8,
        'badge' => null,
    ],
    [
        'label' => 'Administration',
        'route' => null,
        'roles' => ['admin_titulaire'],
        'disponible' => false,
        'sprint' => 9,
        'badge' => null,
    ],
    [
        'label' => 'Gouvernance IA',
        'route' => null,
        'roles' => ['admin_aphp', 'admin_titulaire'],
        'disponible' => false,
        'sprint' => 10,
        'badge' => null,
    ],
    [
        'label' => 'Support & incidents',
        'route' => null,
        'roles' => ['secretariat_medical', 'referent_service', 'admin_aphp', 'operateur_titulaire', 'relecteur_valideur', 'admin_titulaire'],
        'disponible' => false,
        'sprint' => 11,
        'badge' => null,
    ],

];