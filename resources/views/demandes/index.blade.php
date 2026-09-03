@extends('layouts.app')

@section('titre', 'Mes demandes')
@section('sprint-actuel', 'Dépôt & réception (Sprint 1)')

@section('contenu')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Demandes de mon service</h1>
        <a href="{{ route('demandes.create') }}" class="bg-black text-white rounded px-4 py-2 text-sm hover:bg-black/80 transition">
            + Nouvelle demande
        </a>
    </div>

    @if (session('succes'))
        <div class="mb-4 text-sm text-black bg-gray-100 border border-black/20 rounded p-3">
            {{ session('succes') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm border border-black/10 rounded overflow-hidden">
            <thead class="bg-black text-white text-left">
                <tr>
                    <th class="px-4 py-2">Référence</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Urgence</th>
                    <th class="px-4 py-2">Statut</th>
                    <th class="px-4 py-2 hidden sm:table-cell">Échéance SLA</th>
                    <th class="px-4 py-2 hidden sm:table-cell">Déposé le</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($demandes as $demande)
                    <tr class="border-t border-black/10">
                        <td class="px-4 py-2 font-mono text-xs">{{ Str::limit($demande->reference, 8, '') }}</td>
                        <td class="px-4 py-2">{{ $demande->type_document }}</td>
                        <td class="px-4 py-2">{{ $demande->niveau_urgence }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs border border-black/20">{{ $demande->statut }}</span>
                        </td>
                        <td class="px-4 py-2 hidden sm:table-cell">{{ $demande->echeance_sla?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2 hidden sm:table-cell">{{ $demande->date_depot?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('demandes.show', $demande) }}" class="underline hover:no-underline">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-black/40">Aucune demande pour le moment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $demandes->links() }}
    </div>
@endsection