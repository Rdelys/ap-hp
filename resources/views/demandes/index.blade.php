@extends('layouts.app')

@section('titre', 'Mes demandes')
@section('sprint-actuel', 'Dépôt & réception (Sprint 1)')

@section('contenu')
    <div class="flex items-end justify-between mb-8 pb-5 border-b border-ligne">
        <div>
            <h1 class="font-titre text-2xl">Demandes de mon service</h1>
            <p class="font-mono text-[12px] text-meta mt-1">{{ $demandes->total() }} dossier(s)</p>
        </div>
        <a href="{{ route('demandes.create') }}" class="bg-encre text-papier text-[13px] px-4 py-2.5 hover:bg-encre/85 transition">
            Déposer une demande
        </a>
    </div>

    @if (session('succes'))
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">{{ session('succes') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-[13.5px]">
            <thead>
                <tr class="border-b border-encre text-left">
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Référence</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Type</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Urgence</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Statut</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal hidden sm:table-cell">Échéance</th>
                    <th class="py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($demandes as $demande)
                    <tr class="border-b border-ligne hover:bg-papier-ombre/60 transition">
                        <td class="py-3 pr-4 font-mono text-[12.5px]">{{ Str::limit($demande->reference, 8, '') }}</td>
                        <td class="py-3 pr-4">{{ $demande->type_document }}</td>
                        <td class="py-3 pr-4">
                            <span class="{{ $demande->niveau_urgence === 'urgent' ? 'font-medium' : 'text-meta' }}">
                                {{ ucfirst($demande->niveau_urgence) }}
                            </span>
                        </td>
                        <td class="py-3 pr-4"><x-statut-dossier :statut="$demande->statut" /></td>
                        <td class="py-3 pr-4 font-mono text-[12.5px] hidden sm:table-cell">
                            {{ $demande->echeance_sla?->format('d/m H:i') }}
                        </td>
                        <td class="py-3 text-right">
                            <a href="{{ route('demandes.show', $demande) }}" class="text-[13px] underline decoration-ligne hover:decoration-encre underline-offset-4">
                                Ouvrir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center text-meta">
                            Aucune demande pour le moment. Déposez un premier fichier audio pour démarrer.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $demandes->links() }}</div>
@endsection