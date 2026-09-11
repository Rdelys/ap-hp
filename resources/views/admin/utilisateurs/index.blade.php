@extends('layouts.app')

@section('titre', 'Utilisateurs')
@section('sprint-actuel', 'Administration')

@section('contenu')
    <div class="flex items-end justify-between mb-8 pb-5 border-b border-ligne">
        <h1 class="font-titre text-2xl">Utilisateurs</h1>
        <a href="{{ route('admin.utilisateurs.create') }}" class="bg-encre text-papier px-4 py-2.5 text-[13px] hover:bg-encre/85 transition">
            Ajouter un utilisateur
        </a>
    </div>

    @if (session('succes'))
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">{{ session('succes') }}</div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-[13.5px]">
            <thead>
                <tr class="border-b border-encre text-left">
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Nom</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Email</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Rôle</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Service</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Statut</th>
                    <th class="py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($utilisateurs as $u)
                    <tr class="border-b border-ligne">
                        <td class="py-3 pr-4">{{ $u->name }}</td>
                        <td class="py-3 pr-4 font-mono text-[12.5px]">{{ $u->email }}</td>
                        <td class="py-3 pr-4">{{ $u->role->libelle() }}</td>
                        <td class="py-3 pr-4 text-meta">{{ $u->service->nom ?? '—' }}</td>
                        <td class="py-3 pr-4">
                            @if (! $u->mot_de_passe_defini)
                                <span class="text-meta">En attente d'activation</span>
                            @elseif ($u->actif)
                                <span>Actif</span>
                            @else
                                <span class="text-meta">Désactivé</span>
                            @endif
                        </td>
                        <td class="py-3 text-right space-x-3 whitespace-nowrap">
                            @if (! $u->mot_de_passe_defini)
                                <form method="POST" action="{{ route('admin.utilisateurs.renvoyer-invitation', $u) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[13px] underline decoration-ligne hover:decoration-encre underline-offset-4">Renvoyer</button>
                                </form>
                            @endif
                            <a href="{{ route('admin.utilisateurs.edit', $u) }}" class="text-[13px] underline decoration-ligne hover:decoration-encre underline-offset-4">Modifier</a>
                            @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.utilisateurs.basculer-activation', $u) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[13px] underline decoration-ligne hover:decoration-encre underline-offset-4">
                                        {{ $u->actif ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-16 text-center text-meta">Aucun utilisateur.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $utilisateurs->links() }}</div>
@endsection