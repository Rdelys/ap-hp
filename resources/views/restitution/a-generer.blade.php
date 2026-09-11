@extends('layouts.app')

@section('titre', 'Dossiers validés — à restituer')
@section('sprint-actuel', 'Restitution')

@section('contenu')
    <div class="mb-8 pb-5 border-b border-ligne">
        <h1 class="font-titre text-2xl">Dossiers validés en attente de restitution</h1>
    </div>

    @if (session('succes'))
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">{{ session('succes') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-[13.5px]">
            <thead>
                <tr class="border-b border-encre text-left">
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Référence</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Établissement / Service</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Type</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Validé le</th>
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
                        <td class="py-3 pr-4 font-mono text-[12.5px]">{{ $demande->valide_le?->format('d/m/Y H:i') }}</td>
                        <td class="py-3 text-right">
                            <form method="POST" action="{{ route('restitution.generer', $demande) }}">
                                @csrf
                                <button type="submit" class="bg-encre text-papier px-3 py-1.5 text-[12.5px] hover:bg-encre/85 transition">
                                    Générer & restituer
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center text-meta">Aucun dossier en attente de restitution.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $demandes->links() }}</div>
@endsection