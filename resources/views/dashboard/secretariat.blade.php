@extends('layouts.app')

@section('titre', 'Secrétariat médical')

@section('contenu')
    <div class="mb-10 pb-6 border-b border-ligne">
        <h1 class="font-titre text-2xl">Bonjour {{ $user->name }}</h1>
        <p class="font-mono text-[12px] text-meta mt-1">{{ $user->service->nom ?? '—' }} · {{ $user->etablissement->nom ?? '—' }}</p>
    </div>

    <div class="grid sm:grid-cols-2 gap-px bg-ligne">
        <a href="{{ route('demandes.create') }}" class="bg-papier p-6 hover:bg-papier-ombre transition">
            <div class="font-titre text-lg mb-1">Déposer une demande</div>
            <p class="text-[13px] text-meta">Envoyer un nouveau fichier audio à transcrire.</p>
        </a>
        <a href="{{ route('demandes.index') }}" class="bg-papier p-6 hover:bg-papier-ombre transition">
            <div class="font-titre text-lg mb-1">Mes demandes</div>
            <p class="text-[13px] text-meta">Suivre l'état de traitement de vos dossiers en cours.</p>
        </a>
    </div>
@endsection