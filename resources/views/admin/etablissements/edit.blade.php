@extends('layouts.app')
@section('titre', "Modifier l'établissement")
@section('sprint-actuel', 'Administration')
@section('contenu')
    <a href="{{ route('admin.etablissements.index') }}" class="text-[13px] text-meta hover:text-encre transition">← Établissements</a>
    <h1 class="font-titre text-2xl mt-4 mb-8 pb-5 border-b border-ligne">{{ $etablissement->nom }}</h1>

    @if ($errors->any())
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">
            <ul class="space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.etablissements.update', $etablissement) }}" class="space-y-5 max-w-lg">
        @csrf @method('PUT')
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Nom</label>
            <input type="text" name="nom" value="{{ old('nom', $etablissement->nom) }}" required class="w-full border border-ligne px-3 py-2.5 text-[14px]">
        </div>
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Code</label>
            <input type="text" name="code" value="{{ old('code', $etablissement->code) }}" required class="w-full border border-ligne px-3 py-2.5 text-[14px] font-mono">
        </div>
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Adresse</label>
            <input type="text" name="adresse" value="{{ old('adresse', $etablissement->adresse) }}" class="w-full border border-ligne px-3 py-2.5 text-[14px]">
        </div>
        <label class="flex items-center gap-2 text-[13px]">
            <input type="checkbox" name="actif" value="1" @checked($etablissement->actif) class="accent-black">
            Établissement actif
        </label>
        <button type="submit" class="bg-encre text-papier px-5 py-3 text-[14px] hover:bg-encre/85 transition">Enregistrer</button>
    </form>
@endsection