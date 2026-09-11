@extends('layouts.app')
@section('titre', 'Services')
@section('sprint-actuel', 'Administration')
@section('contenu')
    <div class="flex items-end justify-between mb-8 pb-5 border-b border-ligne">
        <h1 class="font-titre text-2xl">Services</h1>
        <a href="{{ route('admin.services.create') }}" class="bg-encre text-papier px-4 py-2.5 text-[13px] hover:bg-encre/85 transition">Ajouter</a>
    </div>

    @if (session('succes'))
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">{{ session('succes') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-[13.5px]">
            <thead>
                <tr class="border-b border-encre text-left">
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Service</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Établissement</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Référent</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">IA</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Statut</th>
                    <th class="py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services as $s)
                    <tr class="border-b border-ligne">
                        <td class="py-3 pr-4">{{ $s->nom }}</td>
                        <td class="py-3 pr-4 text-meta">{{ $s->etablissement->nom }}</td>
                        <td class="py-3 pr-4">{{ $s->referent_nom ?? '—' }}</td>
                        <td class="py-3 pr-4">{{ $s->ia_autorisee ? 'Autorisée' : 'Désactivée' }}</td>
                        <td class="py-3 pr-4">{{ $s->actif ? 'Actif' : 'Inactif' }}</td>
                        <td class="py-3 text-right">
                            <a href="{{ route('admin.services.edit', $s) }}" class="text-[13px] underline decoration-ligne hover:decoration-encre underline-offset-4">Modifier</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-16 text-center text-meta">Aucun service.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $services->links() }}</div>
@endsection