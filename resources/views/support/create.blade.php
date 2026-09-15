@extends('layouts.app')

@section('titre', 'Ouvrir un ticket')
@section('sprint-actuel', 'Support & incidents (Sprint 11)')

@section('contenu')
    <a href="{{ route('support.index') }}" class="text-[13px] text-meta hover:text-encre transition">← Support</a>
    <h1 class="font-titre text-2xl mt-4 mb-8 pb-5 border-b border-ligne">Ouvrir un ticket</h1>

    @if ($errors->any())
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">
            <ul class="space-y-0.5">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('support.store') }}" class="space-y-5 max-w-lg">
        @csrf

        <div>
            <label class="block text-[12px] text-meta mb-1.5">Catégorie</label>
            <select name="categorie" required id="select-categorie" class="w-full border border-ligne px-3 py-2.5 text-[14px] bg-papier">
                <option value="fichier_audio">Fichier audio</option>
                <option value="document">Document restitué</option>
                <option value="acces_plateforme">Accès à la plateforme</option>
                <option value="compte">Compte utilisateur</option>
                <option value="delai">Délai de traitement</option>
                <option value="incident_securite">Incident de sécurité</option>
                <option value="autre">Autre</option>
            </select>
        </div>

        <div id="alerte-securite" class="hidden text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">
            Ce signalement sera traité en priorité critique et transmis immédiatement à l'équipe compétente.
        </div>

        <div>
            <label class="block text-[12px] text-meta mb-1.5">Dossier concerné — facultatif</label>
            <select name="demande_id" class="w-full border border-ligne px-3 py-2.5 text-[14px] bg-papier">
                <option value="">—</option>
                @foreach ($mesDemandes as $d)
                    <option value="{{ $d->id }}">{{ $d->reference }} — {{ $d->type_document }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[12px] text-meta mb-1.5">Sujet</label>
            <input type="text" name="sujet" value="{{ old('sujet') }}" required class="w-full border border-ligne px-3 py-2.5 text-[14px]">
        </div>

        <div>
            <label class="block text-[12px] text-meta mb-1.5">Description</label>
            <textarea name="description" required rows="6" class="w-full border border-ligne px-3 py-2.5 text-[14px]">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="bg-encre text-papier px-5 py-3 text-[14px] hover:bg-encre/85 transition">
            Envoyer
        </button>
    </form>

<script>
    document.getElementById('select-categorie').addEventListener('change', (e) => {
        document.getElementById('alerte-securite').classList.toggle('hidden', e.target.value !== 'incident_securite');
    });
</script>
@endsection