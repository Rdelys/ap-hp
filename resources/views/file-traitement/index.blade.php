@extends('layouts.app')

@section('titre', 'File de traitement')
@section('sprint-actuel', 'Gestion des demandes & file de traitement (Sprint 2)')

@section('contenu')
    <h1 class="text-xl font-semibold mb-6">File de traitement</h1>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border border-black/10 rounded overflow-hidden">
            <thead class="bg-black text-white text-left">
                <tr>
                    <th class="px-4 py-2">Référence</th>
                    <th class="px-4 py-2">Établissement / Service</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Urgence</th>
                    <th class="px-4 py-2">Statut</th>
                    <th class="px-4 py-2">Échéance SLA</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($demandes as $demande)
                    <tr class="border-t border-black/10 {{ $demande->estEnRetard() ? 'bg-black/5' : '' }}">
                        <td class="px-4 py-2 font-mono text-xs">{{ Str::limit($demande->reference, 8, '') }}</td>
                        <td class="px-4 py-2">
                            <div>{{ $demande->etablissement->nom }}</div>
                            <div class="text-xs text-black/50">{{ $demande->service->nom }}</div>
                        </td>
                        <td class="px-4 py-2">{{ $demande->type_document }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs border
                                {{ $demande->niveau_urgence === 'urgent' ? 'border-black border-2 font-semibold' : 'border-black/20' }}">
                                {{ strtoupper($demande->niveau_urgence) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs border border-black/20">{{ $demande->statut }}</span>
                        </td>
                        <td class="px-4 py-2">
                            {{ $demande->echeance_sla?->format('d/m/Y H:i') }}
                            @if ($demande->estEnRetard())
                                <div class="text-xs font-semibold mt-0.5">⚠ EN RETARD</div>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('demandes.show', $demande) }}" class="underline hover:no-underline">Voir</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-black/40">Aucune demande en file de traitement.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $demandes->links() }}
    </div>
@endsection