@extends('layouts.app')

@section('titre', 'Poste de relecture')
@section('sprint-actuel', 'Relecture & contrôle qualité')

@section('contenu')
    @if ($demande->mode_production === 'assiste')
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">
            Une transcription automatique a pré-rempli ce texte. Relisez-le intégralement avant de le transmettre —
            aucune restitution ne peut avoir lieu sans validation humaine complète.
        </div>
    @endif
    <div class="flex items-start justify-between mb-8 pb-5 border-b border-ligne">
        <div>
            <h1 class="font-titre text-2xl">Relecture — {{ $demande->reference }}</h1>
            <p class="font-mono text-[12px] text-meta mt-1">{{ $demande->etablissement->nom }} — {{ $demande->service->nom }} — {{ $demande->type_document }}</p>
        </div>
        <a href="{{ route('relecture.index') }}" class="text-[13px] text-meta hover:text-encre transition shrink-0 ml-4">← Retour</a>
    </div>

    @if ($audioUrl)
        <div class="mb-6 border border-ligne p-4">
            <audio id="lecteur-audio" controls class="w-full">
                <source src="{{ $audioUrl }}">
            </audio>
            <div class="flex gap-3 mt-3 text-[12px] font-mono text-meta">
                <span>vitesse</span>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=0.75" class="underline hover:text-encre">0.75x</button>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=1" class="underline hover:text-encre">1x</button>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=1.25" class="underline hover:text-encre">1.25x</button>
            </div>
        </div>
    @endif

    <div class="mb-2 flex items-center justify-between">
        <label class="text-[12px] text-meta">Texte transcrit — relecture</label>
        <span id="statut-sauvegarde" class="font-mono text-[11px] text-meta"></span>
    </div>

    <textarea id="texte-relecture" rows="16"
              class="w-full border border-ligne p-4 text-[14px] font-mono focus:outline-none focus:border-encre">{{ $texteActuel }}</textarea>

    <div class="mt-5">
        <label class="text-[12px] text-meta block mb-1.5">Signalement d'anomalie — facultatif</label>
        <textarea id="signalement-anomalie" rows="3" placeholder="Ex. : passage inaudible à 12min30, identité du dictant incertaine…"
                  class="w-full border border-ligne p-3 text-[13.5px] focus:outline-none focus:border-encre"></textarea>
    </div>

    <div class="flex flex-wrap items-center gap-3 mt-5">
        <button type="button" id="btn-sauvegarder"
                class="border border-ligne px-4 py-2.5 text-[13px] hover:bg-papier-ombre transition">
            Sauvegarder le brouillon
        </button>

        <button type="button" id="btn-ouvrir-renvoi"
                class="border border-encre px-4 py-2.5 text-[13px] hover:bg-encre hover:text-papier transition">
            Renvoyer en correction
        </button>

        <form method="POST" action="{{ route('relecture.envoyer-validation', $demande) }}" id="form-valider" class="ml-auto">
            @csrf
            <input type="hidden" name="texte" id="texte-hidden-valider">
            <input type="hidden" name="signalement_anomalie" id="anomalie-hidden-valider">
            <button type="submit" class="bg-encre text-papier px-5 py-2.5 text-[13px] hover:bg-encre/85 transition">
                Envoyer en validation
            </button>
        </form>
    </div>

    <div id="panneau-renvoi" class="hidden mt-5 border border-ligne p-5">
        <label class="text-[12px] text-meta block mb-1.5">Motif du renvoi — obligatoire</label>
        <form method="POST" action="{{ route('relecture.renvoyer-correction', $demande) }}">
            @csrf
            <textarea name="motif_renvoi" required rows="3"
                      class="w-full border border-ligne p-3 text-[13.5px] focus:outline-none focus:border-encre"
                      placeholder="Précisez ce qui doit être corrigé…"></textarea>
            <div class="flex gap-3 mt-3">
                <button type="submit" class="bg-encre text-papier px-4 py-2.5 text-[13px] hover:bg-encre/85 transition">
                    Confirmer le renvoi
                </button>
                <button type="button" id="btn-annuler-renvoi" class="border border-ligne px-4 py-2.5 text-[13px]">
                    Annuler
                </button>
            </div>
        </form>
    </div>

    <p class="font-mono text-[11px] text-meta mt-6 max-w-2xl">
        Données fictives — la relecture précède la validation humaine obligatoire, elle-même
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
        statutEl.textContent = 'Sauvegarde…';
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