@extends('layouts.app')

@section('titre', 'Établissements')
@section('sprint-actuel', 'Administration')

@section('contenu')
    <div class="flex items-end justify-between mb-8 pb-5 border-b border-ligne">
        <h1 class="font-titre text-2xl">Établissements</h1>
        <a href="{{ route('admin.etablissements.create') }}" class="bg-encre text-papier px-4 py-2.5 text-[13px] hover:bg-encre/85 transition">Ajouter</a>
    </div>

    @if (session('succes'))
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">{{ session('succes') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-[13.5px]">
            <thead>
                <tr class="border-b border-encre text-left">
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Nom</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Code</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Services</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Statut</th>
                    <th class="py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($etablissements as $e)
                    <tr class="border-b border-ligne">
                        <td class="py-3 pr-4">{{ $e->nom }}</td>
                        <td class="py-3 pr-4 font-mono text-[12.5px]">{{ $e->code }}</td>
                        <td class="py-3 pr-4 font-mono">{{ $e->services_count }}</td>
                        <td class="py-3 pr-4">{{ $e->actif ? 'Actif' : 'Inactif' }}</td>
                        <td class="py-3 text-right">
                            <a href="{{ route('admin.etablissements.edit', $e) }}" class="text-[13px] underline decoration-ligne hover:decoration-encre underline-offset-4">Modifier</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-16 text-center text-meta">Aucun établissement.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $etablissements->links() }}</div>
@endsection