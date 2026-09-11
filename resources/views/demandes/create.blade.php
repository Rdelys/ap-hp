@extends('layouts.app')

@section('titre', 'Déposer une demande')
@section('sprint-actuel', 'Dépôt & réception')

@section('contenu')
    <a href="{{ route('demandes.index') }}" class="text-[13px] text-meta hover:text-encre transition">← Mes demandes</a>

    <h1 class="font-titre text-2xl mt-4 mb-8 pb-5 border-b border-ligne">Déposer un fichier audio</h1>

    @if ($errors->any())
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">
            <ul class="space-y-0.5">
                @foreach ($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('demandes.store') }}" enctype="multipart/form-data" class="space-y-6 max-w-lg">
        @csrf

        <div>
            <label class="block text-[12px] text-meta mb-1.5">Fichier audio — WAV, MP3, DSS, DS2 · 250 Mo max</label>
            <input type="file" name="fichier_audio" required accept=".wav,.mp3,.dss,.ds2"
                   class="w-full border border-ligne px-3 py-2.5 text-[14px] bg-papier">
        </div>

        <div>
            <label class="block text-[12px] text-meta mb-1.5">Type de document</label>
            <select name="type_document" required class="w-full border border-ligne px-3 py-2.5 text-[14px] bg-papier">
                <option value="">— Sélectionner —</option>
                <option value="courrier">Courrier médical</option>
                <option value="cr_hospitalisation">Compte rendu d'hospitalisation</option>
                <option value="cr_operatoire">Compte rendu opératoire</option>
                <option value="cr_consultation">Compte rendu de consultation</option>
                <option value="autre">Autre</option>
            </select>
        </div>

        <div>
            <label class="block text-[12px] text-meta mb-1.5">Nom du demandeur / dictant</label>
            <input type="text" name="nom_demandeur" value="{{ old('nom_demandeur') }}"
                   class="w-full border border-ligne px-3 py-2.5 text-[14px]">
        </div>

        <div>
            <label class="block text-[12px] text-meta mb-1.5">Numéro de dictant</label>
            <input type="text" name="numero_dictant" value="{{ old('numero_dictant') }}"
                   class="w-full border border-ligne px-3 py-2.5 text-[14px]">
        </div>

        <div>
            <label class="block text-[12px] text-meta mb-1.5">Niveau d'urgence</label>
            <select name="niveau_urgence" required class="w-full border border-ligne px-3 py-2.5 text-[14px] bg-papier">
                <option value="normal" selected>Normal — 24h ouvrables</option>
                <option value="urgent">Urgent — 2h ouvrables</option>
                <option value="economique">Économique — 72h ouvrables</option>
            </select>
        </div>

        <button type="submit" class="bg-encre text-papier px-5 py-3 text-[14px] hover:bg-encre/85 transition">
            Déposer la demande
        </button>
    </form>
@endsection