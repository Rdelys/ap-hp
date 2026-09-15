@extends('layouts.app')

@section('titre', 'Support & incidents')
@section('sprint-actuel', 'Support & incidents (Sprint 11)')

@section('contenu')
    <div class="flex items-end justify-between mb-8 pb-5 border-b border-ligne">
        <h1 class="font-titre text-2xl">Support & incidents</h1>
        <a href="{{ route('support.create') }}" class="bg-encre text-papier px-4 py-2.5 text-[13px] hover:bg-encre/85 transition">
            Ouvrir un ticket
        </a>
    </div>

    @if (session('succes'))
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">{{ session('succes') }}</div>
    @endif

    <div class="flex gap-2 mb-6 text-[12.5px]">
        <a href="{{ route('support.index') }}" class="px-3 py-1.5 border {{ !request('statut') ? 'border-encre' : 'border-ligne text-meta' }}">Tous</a>
        <a href="{{ route('support.index', ['statut' => 'ouvert']) }}" class="px-3 py-1.5 border {{ request('statut') === 'ouvert' ? 'border-encre' : 'border-ligne text-meta' }}">Ouverts</a>
        <a href="{{ route('support.index', ['statut' => 'en_cours']) }}" class="px-3 py-1.5 border {{ request('statut') === 'en_cours' ? 'border-encre' : 'border-ligne text-meta' }}">En cours</a>
        <a href="{{ route('support.index', ['statut' => 'resolu']) }}" class="px-3 py-1.5 border {{ request('statut') === 'resolu' ? 'border-encre' : 'border-ligne text-meta' }}">Résolus</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-[13.5px]">
            <thead>
                <tr class="border-b border-encre text-left">
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Sujet</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Catégorie</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Ouvert par</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Statut</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Ouvert le</th>
                    <th class="py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($tickets as $ticket)
                    <tr class="border-b border-ligne hover:bg-papier-ombre/60 transition">
                        <td class="py-3 pr-4">{{ $ticket->sujet }}</td>
                        <td class="py-3 pr-4 text-meta">{{ str_replace('_', ' ', $ticket->categorie) }}</td>
                        <td class="py-3 pr-4">{{ $ticket->demandeur->name }}</td>
                        <td class="py-3 pr-4"><x-statut-ticket :statut="$ticket->statut" :criticite="$ticket->criticite" /></td>
                        <td class="py-3 pr-4 font-mono text-[12.5px]">{{ $ticket->created_at->format('d/m H:i') }}</td>
                        <td class="py-3 text-right">
                            <a href="{{ route('support.show', $ticket) }}" class="text-[13px] underline decoration-ligne hover:decoration-encre underline-offset-4">Ouvrir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-16 text-center text-meta">Aucun ticket pour le moment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $tickets->links() }}</div>
@endsection