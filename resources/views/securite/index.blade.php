@extends('layouts.app')

@section('titre', 'Sécurité du compte')

@section('contenu')
    <div class="mb-8 pb-5 border-b border-ligne">
        <h1 class="font-titre text-2xl">Sécurité du compte</h1>
    </div>

    @if (session('succes'))
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">{{ session('succes') }}</div>
    @endif

    <div class="border border-ligne p-5 max-w-lg">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <div class="font-medium">Authentification à deux facteurs</div>
                <p class="text-[13px] text-meta mt-1">
                    {{ $user->two_factor_confirmed_at ? 'Activée depuis le '.$user->two_factor_confirmed_at->format('d/m/Y') : 'Non activée' }}
                </p>
            </div>

            @if ($user->two_factor_confirmed_at)
                <form method="POST" action="{{ route('securite.mfa.desactiver') }}" onsubmit="return confirm('Désactiver la double authentification ?')" class="flex items-center gap-2">
                    @csrf
                    <input type="password" name="password" placeholder="Mot de passe" required
                           class="border border-ligne px-2.5 py-1.5 text-[13px]">
                    <button type="submit" class="border border-encre px-3 py-1.5 text-[13px] hover:bg-encre hover:text-papier transition">
                        Désactiver
                    </button>
                </form>
            @else
                <a href="{{ route('securite.mfa.configurer') }}" class="bg-encre text-papier px-4 py-2 text-[13px] hover:bg-encre/85 transition">
                    Activer
                </a>
            @endif
        </div>
    </div>
@endsection