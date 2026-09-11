@extends('layouts.app')

@section('titre', 'Documents restitués')
@section('sprint-actuel', 'Restitution')

@section('contenu')
    <div class="mb-8 pb-5 border-b border-ligne">
        <h1 class="font-titre text-2xl">Documents restitués</h1>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-[13.5px]">
            <thead>
                <tr class="border-b border-encre text-left">
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Référence</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Type</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Restitué le</th>
                    <th class="py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($demandes as $demande)
                    <tr class="border-b border-ligne hover:bg-papier-ombre/60 transition">
                        <td class="py-3 pr-4 font-mono text-[12.5px]">{{ Str::limit($demande->reference, 8, '') }}</td>
                        <td class="py-3 pr-4">{{ $demande->type_document }}</td>
                        <td class="py-3 pr-4 font-mono text-[12.5px]">{{ $demande->date_restitution?->format('d/m/Y H:i') }}</td>
                        <td class="py-3 text-right">
                            <a href="{{ route('restitution.telecharger', $demande) }}"
                               class="bg-encre text-papier px-3 py-1.5 text-[12.5px] hover:bg-encre/85 transition">
                                Télécharger
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-16 text-center text-meta">Aucun document restitué pour le moment.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $demandes->links() }}</div>
@endsection