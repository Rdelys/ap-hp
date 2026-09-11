@extends('layouts.app')

@section('titre', 'Opérateur')

@section('contenu')
    <div class="mb-10 pb-6 border-b border-ligne">
        <h1 class="font-titre text-2xl">Bonjour {{ $user->name }}</h1>
        <p class="font-mono text-[12px] text-meta mt-1">Opérateur de transcription — titulaire</p>
    </div>

    <a href="{{ route('transcription.index') }}" class="block bg-papier border border-ligne p-6 hover:bg-papier-ombre transition max-w-md">
        <div class="font-titre text-lg mb-1">File de transcription</div>
        <p class="text-[13px] text-meta">Reprendre ou démarrer un dossier en attente.</p>
    </a>
@endsection