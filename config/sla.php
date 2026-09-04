<?php

return [
    'fuseau' => 'Europe/Paris',
    'heure_debut' => 9,
    'heure_fin' => 17,
    'jours_ouvrables' => [1, 2, 3, 4, 5], // ISO : 1=lundi ... 5=vendredi

    // Jours fériés français — à compléter chaque année
    'jours_feries' => [
        '2026-01-01', '2026-04-06', '2026-05-01', '2026-05-08', '2026-05-14',
        '2026-05-25', '2026-07-14', '2026-08-15', '2026-11-01', '2026-11-11', '2026-12-25',
        '2027-01-01',
    ],
];