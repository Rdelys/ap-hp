@extends('layouts.app')

@section('titre', "Journal d'audit")
@section('sprint-actuel', "Journalisation & audit (Sprint 7)")

@section('contenu')
    <div class="mb-8 pb-5 border-b border-ligne">
        <h1 class="font-titre text-2xl">Journal d'audit</h1>
        <p class="font-mono text-[12px] text-meta mt-1">{{ $entrees->total() }} entrée(s) — table append-only, aucune modification possible</p>
    </div>

    <form method="GET" class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-8 pb-8 border-b border-ligne">
        <div class="col-span-2 sm:col-span-1">
            <label class="block text-[11px] text-meta mb-1.5">Action</label>
            <select name="action" class="w-full border border-ligne px-2.5 py-2 text-[13px] bg-papier">
                <option value="">Toutes</option>
                @foreach ($actionsDisponibles as $action)
                    <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[11px] text-meta mb-1.5">Utilisateur</label>
            <select name="utilisateur_id" class="w-full border border-ligne px-2.5 py-2 text-[13px] bg-papier">
                <option value="">Tous</option>
                @foreach ($utilisateurs as $u)
                    <option value="{{ $u->id }}" @selected((int) request('utilisateur_id') === $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-[11px] text-meta mb-1.5">Référence dossier</label>
            <input type="text" name="reference" value="{{ request('reference') }}" placeholder="ex: aae1cb9d"
                   class="w-full border border-ligne px-2.5 py-2 text-[13px]">
        </div>

        <div>
            <label class="block text-[11px] text-meta mb-1.5">Du</label>
            <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                   class="w-full border border-ligne px-2.5 py-2 text-[13px]">
        </div>

        <div class="flex gap-2 items-end">
            <div class="flex-1">
                <label class="block text-[11px] text-meta mb-1.5">Au</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                       class="w-full border border-ligne px-2.5 py-2 text-[13px]">
            </div>
            <button type="submit" class="bg-encre text-papier px-4 py-2 text-[13px] hover:bg-encre/85 transition shrink-0">
                Filtrer
            </button>
        </div>
    </form>

    @if (request()->anyFilled(['action', 'utilisateur_id', 'reference', 'date_debut', 'date_fin']))
        <div class="mb-6">
            <a href="{{ route('audit.index') }}" class="text-[13px] underline decoration-ligne hover:decoration-encre underline-offset-4">
                Réinitialiser les filtres
            </a>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-[13.5px]">
            <thead>
                <tr class="border-b border-encre text-left">
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Horodatage</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Utilisateur</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Action</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Objet</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal hidden md:table-cell">Adresse IP</th>
                    <th class="py-3 font-mono text-[11px] text-meta font-normal hidden lg:table-cell">Détails</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entrees as $entree)
                    <tr class="border-b border-ligne align-top">
                        <td class="py-3 pr-4 font-mono text-[12px] whitespace-nowrap">{{ $entree->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="py-3 pr-4">{{ $entree->user->name ?? '—' }}</td>
                        <td class="py-3 pr-4 font-mono text-[12px]">{{ $entree->action }}</td>
                        <td class="py-3 pr-4">
                            @if ($entree->objet_type && $entree->objet_id)
                                <span class="text-[12.5px] text-meta">{{ class_basename($entree->objet_type) }} #{{ $entree->objet_id }}</span>
                            @else
                                <span class="text-meta">—</span>
                            @endif
                        </td>
                        <td class="py-3 pr-4 font-mono text-[12px] text-meta hidden md:table-cell">{{ $entree->adresse_ip ?? '—' }}</td>
                        <td class="py-3 font-mono text-[11px] text-meta hidden lg:table-cell max-w-xs truncate" title="{{ json_encode($entree->contexte) }}">
                            {{ $entree->contexte ? json_encode($entree->contexte, JSON_UNESCAPED_UNICODE) : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center text-meta">Aucune entrée ne correspond à ces critères.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $entrees->links() }}</div>
@endsection