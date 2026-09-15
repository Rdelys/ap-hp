@extends('layouts.app')

@section('titre', 'Configurer la double authentification')

@section('contenu')
    <a href="{{ route('securite.index') }}" class="text-[13px] text-meta hover:text-encre transition">← Sécurité du compte</a>
    <h1 class="font-titre text-2xl mt-4 mb-8 pb-5 border-b border-ligne">Configurer la double authentification</h1>

    @if ($errors->any())
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">{{ $errors->first() }}</div>
    @endif

    <div class="max-w-sm">
        <p class="text-[13px] text-meta mb-4">1. Scannez ce code avec une application d'authentification (Google Authenticator, Authy…)</p>
        <div class="border border-ligne p-4 mb-6 bg-papier flex justify-center">{!! $qrCodeSvg !!}</div>

        <p class="text-[12px] text-meta mb-1">Ou saisissez cette clé manuellement :</p>
        <div class="font-mono text-[13px] border border-ligne px-3 py-2 mb-6 break-all">{{ $secret }}</div>

        <p class="text-[13px] text-meta mb-3">2. Saisissez le code généré pour confirmer :</p>
        <form method="POST" action="{{ route('securite.mfa.confirmer') }}" class="space-y-4">
            @csrf
            <input type="text" name="code" required maxlength="6" autocomplete="one-time-code"
                   class="w-full border border-ligne px-3 py-2.5 text-[14px] font-mono tracking-widest focus:outline-none focus:border-encre">
            <button type="submit" class="w-full bg-encre text-papier py-3 text-[14px] hover:bg-encre/85 transition">
                Confirmer et activer
            </button>
        </form>
    </div>
@endsection