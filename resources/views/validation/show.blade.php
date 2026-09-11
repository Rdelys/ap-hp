@extends('layouts.app')

@section('titre', 'Validation du dossier')
@section('sprint-actuel', 'Validation humaine')

@section('contenu')
    <div class="flex items-start justify-between mb-8 pb-5 border-b border-ligne">
        <div>
            <h1 class="font-titre text-2xl">Validation — {{ $demande->reference }}</h1>
            <p class="font-mono text-[12px] text-meta mt-1">{{ $demande->etablissement->nom }} — {{ $demande->service->nom }} — {{ $demande->type_document }}</p>
        </div>
        <a href="{{ route('validation.index') }}" class="text-[13px] text-meta hover:text-encre transition shrink-0 ml-4">← Retour</a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 text-[13px] mb-6 pb-6 border-b border-ligne">
        <div>
            <div class="font-mono text-[11px] text-meta mb-1">Relu par</div>
            <div>{{ $demande->relecteur->name ?? '—' }}</div>
        </div>
        <div>
            <div class="font-mono text-[11px] text-meta mb-1">Relu le</div>
            <div class="font-mono">{{ $demande->relu_le?->format('d/m/Y H:i') ?? '—' }}</div>
        </div>
        <div>
            <div class="font-mono text-[11px] text-meta mb-1">Urgence</div>
            <div>{{ ucfirst($demande->niveau_urgence) }}</div>
        </div>
        <div>
            <div class="font-mono text-[11px] text-meta mb-1">Mode de production</div>
            <div>{{ $demande->mode_production }}</div>
        </div>
    </div>

    @if ($demande->signalement_anomalie)
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">
            <span class="font-mono text-[11px] text-meta block mb-1">Anomalie signalée par le relecteur</span>
            {{ $demande->signalement_anomalie }}
        </div>
    @endif

    @if ($audioUrl)
        <div class="mb-6 border border-ligne p-4">
            <audio controls class="w-full">
                <source src="{{ $audioUrl }}">
            </audio>
        </div>
    @endif

    <label class="text-[12px] text-meta block mb-1.5">Texte transcrit — lecture seule</label>
    <textarea readonly rows="16"
              class="w-full border border-ligne p-4 text-[14px] font-mono bg-papier-ombre cursor-not-allowed">{{ $texte }}</textarea>

    <div class="mt-8 border-t-2 border-encre pt-6">
        <h2 class="font-titre text-lg mb-3">Validation humaine — action définitive</h2>
        <p class="text-[13px] text-meta mb-5 max-w-xl">
            En validant ce dossier, vous certifiez avoir vérifié l'exactitude de la transcription,
            les données médicales, l'identité des personnes mentionnées et la conformité du document.
            Cette action verrouille le dossier et l'engage vers la restitution.
        </p>

        <form method="POST" action="{{ route('validation.valider', $demande) }}" id="form-validation">
            @csrf
            <label class="flex items-start gap-2.5 text-[13.5px] mb-5">
                <input type="checkbox" name="confirmation" id="case-confirmation" required class="mt-1 accent-black">
                <span>Je certifie avoir relu et contrôlé ce document et je valide sa restitution.</span>
            </label>

            <button type="submit" id="btn-valider" disabled
                    class="bg-encre text-papier px-5 py-3 text-[14px] opacity-30 cursor-not-allowed transition">
                Valider définitivement le dossier
            </button>
        </form>
    </div>

    <p class="font-mono text-[11px] text-meta mt-6 max-w-2xl">
        Données fictives — ce verrou de validation humaine est indépendant du mode de production
        (manuel, assisté IA ou hybride), conformément au CCTP.
    </p>

<script>
    const checkbox = document.getElementById('case-confirmation');
    const bouton = document.getElementById('btn-valider');

    checkbox.addEventListener('change', () => {
        bouton.disabled = !checkbox.checked;
        bouton.classList.toggle('opacity-30', !checkbox.checked);
        bouton.classList.toggle('cursor-not-allowed', !checkbox.checked);
    });

    document.getElementById('form-validation').addEventListener('submit', (e) => {
        if (!confirm('Confirmez-vous définitivement la validation de ce dossier ? Cette action est tracée et verrouille le document.')) {
            e.preventDefault();
        }
    });
</script>
@endsection