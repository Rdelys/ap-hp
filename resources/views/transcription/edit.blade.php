@extends('layouts.app')

@section('titre', 'Poste de transcription')
@section('sprint-actuel', 'Transcription')

@section('contenu')
    @if ($demande->mode_production === 'assiste')
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">
            Une transcription automatique a pré-rempli ce texte. Relisez-le intégralement avant de le transmettre —
            aucune restitution ne peut avoir lieu sans validation humaine complète.
        </div>
    @endif

    <div class="flex items-start justify-between mb-8 pb-5 border-b border-ligne">
        <div>
            <h1 class="font-titre text-2xl">Transcription — {{ $demande->reference }}</h1>
            <p class="font-mono text-[12px] text-meta mt-1">{{ $demande->etablissement->nom }} — {{ $demande->service->nom }} — {{ $demande->type_document }}</p>
        </div>
        <a href="{{ route('transcription.index') }}" class="text-[13px] text-meta hover:text-encre transition shrink-0 ml-4">← Retour</a>
    </div>

    @if ($audioUrl)
        <div class="mb-6 border border-ligne p-4">
            <audio id="lecteur-audio" controls class="w-full">
                <source src="{{ $audioUrl }}">
                Votre navigateur ne prend pas en charge la lecture audio.
            </audio>
            <div class="flex gap-3 mt-3 text-[12px] font-mono text-meta">
                <span>vitesse</span>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=0.75" class="underline hover:text-encre">0.75x</button>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=1" class="underline hover:text-encre">1x</button>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=1.25" class="underline hover:text-encre">1.25x</button>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=1.5" class="underline hover:text-encre">1.5x</button>
            </div>
        </div>
    @else
        <div class="mb-6 text-[13px] text-meta border border-ligne p-4">Aucun fichier audio associé.</div>
    @endif

    <div class="mb-2 flex items-center justify-between">
        <label class="text-[12px] text-meta">Texte de la transcription</label>
        <span id="statut-sauvegarde" class="font-mono text-[11px] text-meta"></span>
    </div>

    <textarea id="texte-transcription" rows="16"
              class="w-full border border-ligne p-4 text-[14px] font-mono focus:outline-none focus:border-encre">{{ $texteActuel }}</textarea>

    <div id="zone-alerte-ia" class="hidden mt-3 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2"></div>

    <div class="flex flex-wrap items-center gap-3 mt-5">
        <button type="button" id="btn-assister-ia"
                class="border border-encre px-4 py-2.5 text-[13px] hover:bg-encre hover:text-papier transition">
            Assister avec l'IA
        </button>
        <button type="button" id="btn-sauvegarder"
                class="border border-ligne px-4 py-2.5 text-[13px] hover:bg-papier-ombre transition">
            Sauvegarder le brouillon
        </button>
        <form method="POST" action="{{ route('transcription.terminer', $demande) }}" id="form-terminer" class="ml-auto">
            @csrf
            <input type="hidden" name="texte" id="texte-hidden-terminer">
            <button type="submit" class="bg-encre text-papier px-5 py-2.5 text-[13px] hover:bg-encre/85 transition">
                Envoyer en relecture
            </button>
        </form>
    </div>

    <p class="font-mono text-[11px] text-meta mt-6 max-w-2xl">
        Données fictives — l'assistance IA (Claude, Anthropic) corrige et structure le texte déjà saisi ;
        elle n'effectue pas de reconnaissance vocale. Usage en production soumis à déclaration préalable auprès de l'AP-HP.
    </p>

<script>
    const demandeId = {{ $demande->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const textarea = document.getElementById('texte-transcription');
    const statutEl = document.getElementById('statut-sauvegarde');
    const alerteIA = document.getElementById('zone-alerte-ia');

    document.getElementById('btn-sauvegarder').addEventListener('click', () => sauvegarder());

    document.getElementById('form-terminer').addEventListener('submit', () => {
        document.getElementById('texte-hidden-terminer').value = textarea.value;
    });

    document.getElementById('btn-assister-ia').addEventListener('click', async () => {
        const bouton = document.getElementById('btn-assister-ia');
        bouton.disabled = true;
        bouton.textContent = 'Analyse en cours…';
        alerteIA.classList.add('hidden');

        try {
            const res = await fetch(`/transcription/${demandeId}/assister-ia`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ texte: textarea.value }),
            });
            const data = await res.json();

            if (data.succes) {
                textarea.value = data.texte;
                alerteIA.textContent = "Texte corrigé par l'assistant IA — relisez attentivement avant de continuer.";
                alerteIA.classList.remove('hidden');
                sauvegarder();
            } else {
                alerteIA.textContent = data.erreur || "L'assistance IA n'est pas disponible.";
                alerteIA.classList.remove('hidden');
            }
        } catch (e) {
            alerteIA.textContent = 'Erreur de connexion au service IA.';
            alerteIA.classList.remove('hidden');
        } finally {
            bouton.disabled = false;
            bouton.textContent = "Assister avec l'IA";
        }
    });

    async function sauvegarder() {
        statutEl.textContent = 'Sauvegarde…';
        try {
            await fetch(`/transcription/${demandeId}/sauvegarder`, {
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