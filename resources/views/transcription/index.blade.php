@extends('layouts.app')

@section('titre', 'Transcription')
@section('sprint-actuel', 'Transcription (Sprint 3)')

@section('contenu')
    <h1 class="text-xl font-semibold mb-6">Dossiers à transcrire</h1>

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
                    <th class="px-4 py-2">Établissement / Service</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Urgence</th>
                    <th class="px-4 py-2">Échéance</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($demandes as $demande)
                    <tr class="border-t border-black/10">
                        <td class="px-4 py-2 font-mono text-xs">{{ Str::limit($demande->reference, 8, '') }}</td>
                        <td class="px-4 py-2">
                            <div>{{ $demande->etablissement->nom }}</div>
                            <div class="text-xs text-black/50">{{ $demande->service->nom }}</div>
                        </td>
                        <td class="px-4 py-2">{{ $demande->type_document }}</td>
                        <td class="px-4 py-2">{{ strtoupper($demande->niveau_urgence) }}</td>
                        <td class="px-4 py-2">{{ $demande->echeance_sla?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('transcription.edit', $demande) }}"
                               class="bg-black text-white rounded px-3 py-1.5 text-xs hover:bg-black/80 transition">
                                {{ $demande->statut === 'en_transcription' ? 'Reprendre' : 'Transcrire' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-black/40">Aucun dossier à transcrire.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $demandes->links() }}</div>
@endsection