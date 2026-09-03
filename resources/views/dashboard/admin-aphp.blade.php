@extends('layouts.app')

@section('titre', 'Secrétariat médical')

@section('contenu')
    <h1 class="text-xl font-semibold mb-4">Bonjour {{ $user->name }}</h1>
    <p class="text-gray-600">
        Tableau de bord secrétariat médical — module de dépôt disponible au Sprint 1.
    </p>
@endsection