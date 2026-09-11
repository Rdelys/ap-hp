@props(['statut'])

@php
    $libelles = [
        'depose' => 'Déposé',
        'en_file' => 'En file d\'attente',
        'en_transcription' => 'En transcription',
        'renvoye_correction' => 'Renvoyé en correction',
        'en_relecture' => 'En relecture',
        'en_validation' => 'En attente de validation',
        'valide' => 'Validé',
        'restitue' => 'Restitué',
        'hors_delai' => 'Hors délai',
    ];
@endphp

<span class="inline-flex items-center gap-1.5 text-[12.5px] font-mono">
    <span class="w-1.5 h-1.5 rounded-full {{ $statut === 'hors_delai' ? 'bg-encre' : 'bg-ligne' }}"></span>
    {{ $libelles[$statut] ?? $statut }}
</span>