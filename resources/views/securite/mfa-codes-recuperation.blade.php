@extends('layouts.app')

@section('titre', 'Codes de récupération')

@section('contenu')
    <h1 class="font-titre text-2xl mb-3">Double authentification activée</h1>
    <p class="text-[13px] text-meta mb-6 max-w-lg">
        Conservez ces codes dans un endroit sûr. Chacun ne peut être utilisé qu'une seule fois,
        pour vous connecter si vous perdez l'accès à votre application d'authentification.
    </p>

    <div class="border border-ligne p-5 max-w-sm font-mono text-[14px] space-y-2 mb-6">
        @foreach ($codes as $code)
            <div>{{ $code }}</div>
        @endforeach
    </div>

    <a href="{{ route('securite.index') }}" class="bg-encre text-papier px-5 py-3 text-[14px] hover:bg-encre/85 transition inline-block">
        J'ai noté mes codes
    </a>
@endsection