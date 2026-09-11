@extends('layouts.app')

@section('titre', 'Administration AP-HP')

@section('contenu')
    <div class="mb-10 pb-6 border-b border-ligne">
        <h1 class="font-titre text-2xl">Bonjour {{ $user->name }}</h1>
        <p class="font-mono text-[12px] text-meta mt-1">Administrateur fonctionnel AP-HP</p>
    </div>

    <p class="text-[13.5px] text-meta max-w-md">
        Le tableau de suivi et de reporting global sera disponible au sprint 8.
        Les fonctionnalités de ce compte s'activeront progressivement.
    </p>
@endsection