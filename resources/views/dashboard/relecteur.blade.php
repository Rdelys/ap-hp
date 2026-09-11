@extends('layouts.app')

@section('titre', 'Relecteur / Valideur')

@section('contenu')
    <div class="mb-10 pb-6 border-b border-ligne">
        <h1 class="font-titre text-2xl">Bonjour {{ $user->name }}</h1>
        <p class="font-mono text-[12px] text-meta mt-1">Relecteur / valideur — titulaire</p>
    </div>

    <div class="grid sm:grid-cols-3 gap-px bg-ligne">
        <a href="" class="bg-papier p-6 hover:bg-papier-ombre transition">
            <div class="font-titre text-lg mb-1">Relecture</div>
            <p class="text-[13px] text-meta">Contrôle qualité des transcriptions produites.</p>
        </a>
        <a href="{{ route('validation.index') }}" class="bg-papier p-6 hover:bg-papier-ombre transition">
            <div class="font-titre text-lg mb-1">Validation</div>
            <p class="text-[13px] text-meta">Dossiers en attente de validation humaine.</p>
        </a>
        <a href="" class="bg-papier p-6 hover:bg-papier-ombre transition">
            <div class="font-titre text-lg mb-1">Restitution</div>
            <p class="text-[13px] text-meta">Générer les documents validés.</p>
        </a>
    </div>
@endsection