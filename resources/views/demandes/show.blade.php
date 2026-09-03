@extends('layouts.app')

@section('titre', 'Détail de la demande')
@section('sprint-actuel', 'Dépôt & réception (Sprint 1)')

@section('contenu')
    <h1 class="text-xl font-semibold mb-2">Demande {{ $demande->reference }}</h1>
    <p class="text-sm text-gray-500 mb-6">{{ $demande->service->nom }} — {{ $demande->etablissement->nom }}</p>

    <div class="bg-white shadow rounded p-6 space-y-2 text-sm max-w-xl">
        <div><strong>Type de document :</strong> {{ $demande->type_document }}</div>
        <div><strong>Demandeur :</strong> {{ $demande->nom_demandeur ?? '—' }}</div>
        <div><strong>Numéro de dictant :</strong> {{ $demande->numero_dictant ?? '—' }}</div>
        <div><strong>Niveau d'urgence :</strong> {{ $demande->niveau_urgence }}</div>
        <div><strong>Statut :</strong> {{ $demande->statut }}</div>
        <div><strong>Échéance SLA :</strong> {{ $demande->echeance_sla?->format('d/m/Y H:i') }}</div>
        <div><strong>Déposé par :</strong> {{ $demande->demandeur->name }}</div>
        <div><strong>Déposé le :</strong> {{ $demande->date_depot?->format('d/m/Y H:i') }}</div>
    </div>

    <h2 class="text-lg font-semibold mt-6 mb-2">Documents associés</h2>
    <ul class="text-sm space-y-1">
        @foreach ($demande->documents as $document)
            <li class="bg-white shadow-sm rounded p-3">
                [{{ $document->type }}] {{ $document->nom_fichier }} — {{ round($document->taille_octets / 1024 / 1024, 2) }} Mo
            </li>
        @endforeach
    </ul>

    <a href="{{ route('demandes.index') }}" class="inline-block mt-6 underline hover:no-underline text-sm">
        ← Retour à la liste
    </a>
@endsection