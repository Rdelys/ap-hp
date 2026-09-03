@extends('layouts.app')

@section('titre', 'Déposer une demande')
@section('sprint-actuel', 'Dépôt & réception (Sprint 1)')

@section('contenu')
    <h1 class="text-xl font-semibold mb-6">Déposer un fichier audio</h1>

    @if ($errors->any())
        <div class="mb-4 text-sm text-black bg-gray-100 border border-black/20 rounded p-3">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $erreur)
                    <li>{{ $erreur }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('demandes.store') }}" enctype="multipart/form-data" class="space-y-4 max-w-xl">
        @csrf

        <div>
            <label class="block text-sm mb-1">Fichier audio (WAV, MP3, DSS, DS2 — 250 Mo max)</label>
            <input type="file" name="fichier_audio" required accept=".wav,.mp3,.dss,.ds2"
                   class="w-full border border-black/20 rounded px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm mb-1">Type de document</label>
            <select name="type_document" required class="w-full border border-black/20 rounded px-3 py-2 text-sm">
                <option value="">-- Sélectionner --</option>
                <option value="courrier">Courrier médical</option>
                <option value="cr_hospitalisation">Compte rendu d'hospitalisation</option>
                <option value="cr_operatoire">Compte rendu opératoire</option>
                <option value="cr_consultation">Compte rendu de consultation</option>
                <option value="autre">Autre</option>
            </select>
        </div>

        <div>
            <label class="block text-sm mb-1">Nom du demandeur / dictant</label>
            <input type="text" name="nom_demandeur" value="{{ old('nom_demandeur') }}"
                   class="w-full border border-black/20 rounded px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm mb-1">Numéro de dictant</label>
            <input type="text" name="numero_dictant" value="{{ old('numero_dictant') }}"
                   class="w-full border border-black/20 rounded px-3 py-2 text-sm">
        </div>

        <div>
            <label class="block text-sm mb-1">Niveau d'urgence</label>
            <select name="niveau_urgence" required class="w-full border border-black/20 rounded px-3 py-2 text-sm">
                <option value="normal" selected>Normal (24h ouvrables)</option>
                <option value="urgent">Urgent (2h ouvrables)</option>
                <option value="economique">Économique (72h ouvrables)</option>
            </select>
        </div>

        <button type="submit" class="bg-black text-white rounded px-4 py-2 text-sm hover:bg-black/80 transition">
            Déposer la demande
        </button>
    </form>
@endsection