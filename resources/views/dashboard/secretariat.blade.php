@extends('layouts.app')

@section('titre', 'Secrétariat médical')
@section('sprint-actuel', 'Dépôt & réception (Sprint 1)')

@section('contenu')
    <h1 class="text-xl font-semibold mb-1">Bonjour {{ $user->name }}</h1>
    <p class="text-sm text-black/50 mb-6">{{ $user->service->nom ?? '—' }}</p>

    <a href="{{ route('demandes.index') }}"
       class="inline-block bg-black text-white text-sm rounded px-4 py-2 hover:bg-black/80 transition">
        Accéder à mes demandes
    </a>
@endsection