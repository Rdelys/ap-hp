@extends('layouts.app')

@section('titre', 'Poste de relecture')
@section('sprint-actuel', 'Relecture & contrôle qualité (Sprint 4)')

@section('contenu')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold">Relecture — {{ $demande->reference }}</h1>
            <p class="text-sm text-black/50">{{ $demande->etablissement->nom }} — {{ $demande->service->nom }} — {{ $demande->type_document }}</p>
        </div>
        <a href="{{ route('relecture.index') }}" class="text-sm underline hover:no-underline">← Retour</a>
    </div>

    @if ($audioUrl)
        <div class="mb-4 border border-black/10 rounded p-4">
            <audio id="lecteur-audio" controls class="w-full">
                <source src="{{ $audioUrl }}">
            </audio>
            <div class="flex gap-2 mt-2 text-xs">
                <span class="text-black/50">Vitesse :</span>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=0.75" class="underline">0.75x</button>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=1" class="underline">1x</button>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=1.25" class="underline">1.25x</button>
            </div>
        </div>
    @endif

    <div class="mb-2 flex items-center justify-between">
        <label class="text-sm font-medium">Texte transcrit — relecture</label>
        <span id="statut-sauvegarde" class="text-xs text-black/40"></span>
    </div>

    <textarea id="texte-relecture" rows="16"
              class="w-full border border-black/20 rounded p-4 text-sm font-mono focus:outline-none focus:border-black">{{ $texteActuel }}</textarea>

    <div class="mt-4">
        <label class="text-sm font-medium block mb-1">Signalement d'anomalie (facultatif)</label>
        <textarea id="signalement-anomalie" rows="3" placeholder="Ex. : passage inaudible à 12min30, identité du dictant incertaine..."
                  class="w-full border border-black/20 rounded p-3 text-sm focus:outline-none focus:border-black"></textarea>
    </div>

    <div class="flex flex-wrap gap-3 mt-4">
        <button type="button" id="btn-sauvegarder"
                class="border border-black/20 rounded px-4 py-2 text-sm hover:bg-gray-100 transition">
            Sauvegarder le brouillon
        </button>

        <button type="button" id="btn-ouvrir-renvoi"
                class="border border-black rounded px-4 py-2 text-sm hover:bg-gray-100 transition">
            Renvoyer en correction
        </button>

        <form method="POST" action="{{ route('relecture.envoyer-validation', $demande) }}" id="form-valider" class="ml-auto">
            @csrf
            <input type="hidden" name="texte" id="texte-hidden-valider">
            <input type="hidden" name="signalement_anomalie" id="anomalie-hidden-valider">
            <button type="submit"
                    class="bg-black text-white rounded px-4 py-2 text-sm hover:bg-black/80 transition">
                Envoyer en validation →
            </button>
        </form>
    </div>

    {{-- Panneau de renvoi en correction (masqué par défaut) --}}
    <div id="panneau-renvoi" class="hidden mt-4 border border-black/20 rounded p-4">
        <label class="text-sm font-medium block mb-1">Motif du renvoi (obligatoire)</label>
        <form method="POST" action="{{ route('relecture.renvoyer-correction', $demande) }}">
            @csrf
            <textarea name="motif_renvoi" required rows="3"
                      class="w-full border border-black/20 rounded p-3 text-sm focus:outline-none focus:border-black"
                      placeholder="Précisez ce qui doit être corrigé..."></textarea>
            <div class="flex gap-2 mt-2">
                <button type="submit" class="bg-black text-white rounded px-4 py-2 text-sm hover:bg-black/80 transition">
                    Confirmer le renvoi
                </button>
                <button type="button" id="btn-annuler-renvoi" class="border border-black/20 rounded px-4 py-2 text-sm">
                    Annuler
                </button>
            </div>
        </form>
    </div>

    <p class="text-xs text-black/40 mt-4">
        Données fictives — la relecture précède la validation humaine obligatoire (Sprint 5), elle-même
        obligatoire avant toute restitution, conformément au CCTP.
    </p>

<script>
    const demandeId = {{ $demande->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const textarea = document.getElementById('texte-relecture');
    const statutEl = document.getElementById('statut-sauvegarde');

    document.getElementById('btn-sauvegarder').addEventListener('click', sauvegarder);

    document.getElementById('btn-ouvrir-renvoi').addEventListener('click', () => {
        document.getElementById('panneau-renvoi').classList.remove('hidden');
    });
    document.getElementById('btn-annuler-renvoi').addEventListener('click', () => {
        document.getElementById('panneau-renvoi').classList.add('hidden');
    });

    document.getElementById('form-valider').addEventListener('submit', () => {
        document.getElementById('texte-hidden-valider').value = textarea.value;
        document.getElementById('anomalie-hidden-valider').value = document.getElementById('signalement-anomalie').value;
    });

    async function sauvegarder() {
        statutEl.textContent = 'Sauvegarde...';
        try {
            await fetch(`/relecture/${demandeId}/sauvegarder`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ texte: textarea.value }),
            });
            statutEl.textContent = 'Sauvegardé à ' + new Date().toLocaleTimeString();
        } catch (e) {
            statutEl.textContent = 'Échec de la sauvegarde.';
        }
    }

    setInterval(sauvegarder, 30000);
</script>
@endsection