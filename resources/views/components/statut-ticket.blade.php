@props(['statut', 'criticite' => 'normale'])

@php
    $libelles = ['ouvert' => 'Ouvert', 'en_cours' => 'En cours', 'resolu' => 'Résolu', 'ferme' => 'Fermé'];
@endphp

<span class="inline-flex items-center gap-1.5 text-[12.5px] font-mono">
    <span class="w-1.5 h-1.5 rounded-full {{ $criticite === 'critique' ? 'bg-encre' : 'bg-ligne' }}"></span>
    {{ $libelles[$statut] ?? $statut }}
</span>