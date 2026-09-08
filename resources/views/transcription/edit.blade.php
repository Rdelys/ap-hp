@extends('layouts.app')

@section('titre', 'Poste de transcription')
@section('sprint-actuel', 'Transcription (Sprint 3)')

@section('contenu')
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-xl font-semibold">Transcription — {{ $demande->reference }}</h1>
            <p class="text-sm text-black/50">{{ $demande->etablissement->nom }} — {{ $demande->service->nom }} — {{ $demande->type_document }}</p>
        </div>
        <a href="{{ route('transcription.index') }}" class="text-sm underline hover:no-underline">← Retour</a>
    </div>

    @if ($audioUrl)
        <div class="mb-4 border border-black/10 rounded p-4">
            <audio id="lecteur-audio" controls class="w-full">
                <source src="{{ $audioUrl }}">
                Votre navigateur ne prend pas en charge la lecture audio.
            </audio>
            <div class="flex gap-2 mt-2 text-xs">
                <span class="text-black/50">Vitesse :</span>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=0.75" class="underline">0.75x</button>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=1" class="underline">1x</button>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=1.25" class="underline">1.25x</button>
                <button type="button" onclick="document.getElementById('lecteur-audio').playbackRate=1.5" class="underline">1.5x</button>
            </div>
        </div>
    @else
        <div class="mb-4 text-sm text-black/50 border border-black/10 rounded p-4">Aucun fichier audio associé.</div>
    @endif

    <div class="mb-2 flex items-center justify-between">
        <label class="text-sm font-medium">Texte de la transcription</label>
        <span id="statut-sauvegarde" class="text-xs text-black/40"></span>
    </div>

    <textarea id="texte-transcription" rows="16"
              class="w-full border border-black/20 rounded p-4 text-sm font-mono focus:outline-none focus:border-black">{{ $texteActuel }}</textarea>

    <div id="zone-alerte-ia" class="hidden mt-2 text-xs text-black bg-gray-100 border border-black/20 rounded p-3"></div>

    <div class="flex flex-wrap gap-3 mt-4">
        <button type="button" id="btn-assister-ia"
                class="border border-black rounded px-4 py-2 text-sm hover:bg-black hover:text-white transition">
            Assister avec IA (correction & structuration)
        </button>
        <button type="button" id="btn-sauvegarder"
                class="border border-black/20 rounded px-4 py-2 text-sm hover:bg-gray-100 transition">
            Sauvegarder le brouillon
        </button>
        <form method="POST" action="{{ route('transcription.terminer', $demande) }}" id="form-terminer" class="ml-auto">
            @csrf
            <input type="hidden" name="texte" id="texte-hidden-terminer">
            <button type="submit"
                    class="bg-black text-white rounded px-4 py-2 text-sm hover:bg-black/80 transition">
                Envoyer en relecture →
            </button>
        </form>
    </div>

    <p class="text-xs text-black/40 mt-4">
        Données fictives — l'assistance IA (Claude, Anthropic) n'effectue pas de reconnaissance vocale ;
        elle corrige et structure le texte déjà saisi. Toute utilisation en production nécessite une
        déclaration préalable à l'AP-HP conformément au CCTP.
    </p>

<script>
    const demandeId = {{ $demande->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const textarea = document.getElementById('texte-transcription');
    const statutEl = document.getElementById('statut-sauvegarde');
    const alerteIA = document.getElementById('zone-alerte-ia');

    document.getElementById('btn-sauvegarder').addEventListener('click', () => sauvegarder());

    document.getElementById('form-terminer').addEventListener('submit', (e) => {
        document.getElementById('texte-hidden-terminer').value = textarea.value;
    });

    document.getElementById('btn-assister-ia').addEventListener('click', async () => {
        const bouton = document.getElementById('btn-assister-ia');
        bouton.disabled = true;
        bouton.textContent = 'Analyse en cours...';
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
                alerteIA.textContent = 'Texte corrigé par l\'assistant IA — relisez attentivement avant de continuer.';
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
            bouton.textContent = 'Assister avec IA (correction & structuration)';
        }
    });

    async function sauvegarder() {
        statutEl.textContent = 'Sauvegarde...';
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

    // Autosave toutes les 30 secondes
    setInterval(sauvegarder, 30000);
</script>
@endsection