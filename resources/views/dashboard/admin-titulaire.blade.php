@extends('layouts.app')

@section('titre', 'Administration titulaire')

@section('contenu')
    <div class="mb-10 pb-6 border-b border-ligne">
        <h1 class="font-titre text-2xl">Bonjour {{ $user->name }}</h1>
        <p class="font-mono text-[12px] text-meta mt-1">Administrateur — titulaire</p>
    </div>

    <div class="grid sm:grid-cols-3 gap-px bg-ligne">
        <a href="{{ route('file-traitement.index') }}" class="bg-papier p-6 hover:bg-papier-ombre transition">
            <div class="font-titre text-lg mb-1">File de traitement</div>
            <p class="text-[13px] text-meta">Vue d'ensemble de tous les dossiers en cours.</p>
        </a>
        <a href="{{ route('restitution.a-generer') }}" class="bg-papier p-6 hover:bg-papier-ombre transition">
            <div class="font-titre text-lg mb-1">Restitution</div>
            <p class="text-[13px] text-meta">Dossiers validés en attente de génération.</p>
        </a>
        <div class="bg-papier p-6 opacity-40">
            <div class="font-titre text-lg mb-1">Administration</div>
            <p class="text-[13px] text-meta">Disponible au sprint 9.</p>
        </div>
    </div>
@endsection