@extends('layouts.app')
@section('titre', 'Modifier le service')
@section('sprint-actuel', 'Administration')
@section('contenu')
    <a href="{{ route('admin.services.index') }}" class="text-[13px] text-meta hover:text-encre transition">← Services</a>
    <h1 class="font-titre text-2xl mt-4 mb-8 pb-5 border-b border-ligne">{{ $service->nom }}</h1>

    @if ($errors->any())
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">
            <ul class="space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.services.update', $service) }}" class="space-y-5 max-w-lg">
        @csrf @method('PUT')
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Établissement</label>
            <select name="etablissement_id" required class="w-full border border-ligne px-3 py-2.5 text-[14px] bg-papier">
                @foreach ($etablissements as $e)<option value="{{ $e->id }}" @selected($service->etablissement_id === $e->id)>{{ $e->nom }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Nom du service</label>
            <input type="text" name="nom" value="{{ old('nom', $service->nom) }}" required class="w-full border border-ligne px-3 py-2.5 text-[14px]">
        </div>
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Référent — nom</label>
            <input type="text" name="referent_nom" value="{{ old('referent_nom', $service->referent_nom) }}" class="w-full border border-ligne px-3 py-2.5 text-[14px]">
        </div>
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Référent — email</label>
            <input type="email" name="referent_email" value="{{ old('referent_email', $service->referent_email) }}" class="w-full border border-ligne px-3 py-2.5 text-[14px]">
        </div>
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Référent — téléphone</label>
            <input type="text" name="referent_telephone" value="{{ old('referent_telephone', $service->referent_telephone) }}" class="w-full border border-ligne px-3 py-2.5 text-[14px]">
        </div>
        <div>
            <label class="block text-[12px] text-meta mb-1.5">Règle de nommage des documents</label>
            <input type="text" name="regle_nommage" value="{{ old('regle_nommage', $service->regle_nommage) }}"
                   class="w-full border border-ligne px-3 py-2.5 text-[14px] font-mono">
            <p class="text-[11px] text-meta mt-1">Variables : {reference}, {demandeur}, {dictant}, {date}, {type}</p>
        </div>
        <label class="flex items-center gap-2 text-[13px]">
            <input type="checkbox" name="ia_autorisee" value="1" @checked($service->ia_autorisee) class="accent-black">
            Assistance IA autorisée pour ce service
        </label>
        <label class="flex items-center gap-2 text-[13px]">
            <input type="checkbox" name="actif" value="1" @checked($service->actif) class="accent-black">
            Service actif
        </label>
        <button type="submit" class="bg-encre text-papier px-5 py-3 text-[14px] hover:bg-encre/85 transition">Enregistrer</button>
    </form>
@endsection