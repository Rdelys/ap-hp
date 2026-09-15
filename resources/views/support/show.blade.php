@extends('layouts.app')

@section('titre', 'Ticket')
@section('sprint-actuel', 'Support & incidents (Sprint 11)')

@section('contenu')
    <a href="{{ route('support.index') }}" class="text-[13px] text-meta hover:text-encre transition">← Support</a>

    <div class="flex items-start justify-between mt-4 mb-6 pb-5 border-b border-ligne">
        <div>
            <h1 class="font-titre text-2xl">{{ $ticket->sujet }}</h1>
            <p class="font-mono text-[12px] text-meta mt-1">
                {{ str_replace('_', ' ', $ticket->categorie) }} — ouvert par {{ $ticket->demandeur->name }} le {{ $ticket->created_at->format('d/m/Y H:i') }}
                @if ($ticket->demande) — dossier {{ $ticket->demande->reference }} @endif
            </p>
        </div>
        <x-statut-ticket :statut="$ticket->statut" :criticite="$ticket->criticite" />
    </div>

    @can('gerer-tickets')
    @endcan
    @php $rolesTitulaire = ['operateur_titulaire', 'relecteur_valideur', 'admin_titulaire', 'admin_aphp']; @endphp

    @if (in_array(auth()->user()->role->value, $rolesTitulaire, true))
        <form method="POST" action="{{ route('support.changer-statut', $ticket) }}" class="flex items-center gap-2 mb-8 text-[13px]">
            @csrf
            <span class="text-meta">Changer le statut :</span>
            @foreach (['ouvert' => 'Ouvert', 'en_cours' => 'En cours', 'resolu' => 'Résolu', 'ferme' => 'Fermé'] as $valeur => $label)
                <button type="submit" name="statut" value="{{ $valeur }}"
                        class="px-3 py-1.5 border {{ $ticket->statut === $valeur ? 'border-encre bg-encre text-papier' : 'border-ligne hover:bg-papier-ombre' }} transition">
                    {{ $label }}
                </button>
            @endforeach
        </form>
    @endif

    <div class="mb-6 text-[14px] border border-ligne p-4">{{ $ticket->description }}</div>

    <div class="space-y-4 mb-8">
        @foreach ($ticket->messages as $message)
            <div class="border-l-2 border-ligne pl-4 py-1">
                <div class="font-mono text-[11px] text-meta mb-1">
                    {{ $message->auteur->name }} — {{ $message->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="text-[13.5px]">{{ $message->message }}</div>
            </div>
        @endforeach
    </div>

    <form method="POST" action="{{ route('support.repondre', $ticket) }}" class="max-w-lg">
        @csrf
        <label class="block text-[12px] text-meta mb-1.5">Répondre</label>
        <textarea name="message" required rows="4" class="w-full border border-ligne px-3 py-2.5 text-[14px]"></textarea>
        <button type="submit" class="mt-3 bg-encre text-papier px-4 py-2.5 text-[13px] hover:bg-encre/85 transition">
            Envoyer
        </button>
    </form>
@endsection