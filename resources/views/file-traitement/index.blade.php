@extends('layouts.app')

@section('titre', 'File de traitement')
@section('sprint-actuel', 'Gestion des demandes & file de traitement')

@section('contenu')
    <div class="mb-8 pb-5 border-b border-ligne">
        <h1 class="font-titre text-2xl">File de traitement</h1>
        <p class="font-mono text-[12px] text-meta mt-1">{{ $demandes->total() }} dossier(s) actif(s)</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-[13.5px]">
            <thead>
                <tr class="border-b border-encre text-left">
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Référence</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Établissement / Service</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Type</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Urgence</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Statut</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Échéance</th>
                    <th class="py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($demandes as $demande)
                    <tr class="border-b border-ligne hover:bg-papier-ombre/60 transition">
                        <td class="py-3 pr-4 font-mono text-[12.5px]">{{ Str::limit($demande->reference, 8, '') }}</td>
                        <td class="py-3 pr-4">
                            <div>{{ $demande->etablissement->nom }}</div>
                            <div class="text-[12px] text-meta">{{ $demande->service->nom }}</div>
                        </td>
                        <td class="py-3 pr-4">{{ $demande->type_document }}</td>
                        <td class="py-3 pr-4">
                            <span class="{{ $demande->niveau_urgence === 'urgent' ? 'font-medium' : 'text-meta' }}">
                                {{ ucfirst($demande->niveau_urgence) }}
                            </span>
                        </td>
                        <td class="py-3 pr-4"><x-statut-dossier :statut="$demande->statut" /></td>
                        <td class="py-3 pr-4 font-mono text-[12.5px]">
                            {{ $demande->echeance_sla?->format('d/m H:i') }}
                            @if ($demande->estEnRetard())
                                <div class="text-[11px] font-medium mt-0.5">en retard</div>
                            @endif
                        </td>
                        <td class="py-3 text-right">
                            <a href="{{ route('demandes.show', $demande) }}" class="text-[13px] underline decoration-ligne hover:decoration-encre underline-offset-4">
                                Ouvrir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-meta">Aucune demande en file de traitement.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $demandes->links() }}</div>
@endsection