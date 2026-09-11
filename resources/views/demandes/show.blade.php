@extends('layouts.app')

@section('titre', 'Détail de la demande')
@section('sprint-actuel', 'Dépôt & réception (Sprint 1)')

@section('contenu')
    <a href="{{ route('demandes.index') }}" class="text-[13px] text-meta hover:text-encre transition">← Retour à la liste</a>

    <div class="mt-4 mb-8 pb-5 border-b border-ligne">
        <h1 class="font-titre text-2xl">Dossier {{ $demande->reference }}</h1>
        <p class="font-mono text-[12px] text-meta mt-1">{{ $demande->service->nom }} — {{ $demande->etablissement->nom }}</p>
    </div>

    <x-stepper-dossier :statut-actuel="$demande->statut" />

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-[13px] mb-8 pb-8 border-b border-ligne">
        <div>
            <div class="font-mono text-[11px] text-meta mb-1">Type de document</div>
            <div>{{ $demande->type_document }}</div>
        </div>
        <div>
            <div class="font-mono text-[11px] text-meta mb-1">Demandeur</div>
            <div>{{ $demande->nom_demandeur ?? '—' }}</div>
        </div>
        <div>
            <div class="font-mono text-[11px] text-meta mb-1">Niveau d'urgence</div>
            <div>{{ ucfirst($demande->niveau_urgence) }}</div>
        </div>
        <div>
            <div class="font-mono text-[11px] text-meta mb-1">Échéance SLA</div>
            <div class="font-mono">{{ $demande->echeance_sla?->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <h2 class="font-titre text-lg mb-3">Documents associés</h2>
    <div class="space-y-px">
        @foreach ($demande->documents as $document)
            <div class="flex items-center justify-between border-b border-ligne py-3 text-[13.5px]">
                <span>{{ $document->nom_fichier }}</span>
                <span class="font-mono text-[11px] text-meta">{{ $document->type }} · {{ round($document->taille_octets / 1024 / 1024, 2) }} Mo</span>
            </div>
        @endforeach
    </div>
@endsection