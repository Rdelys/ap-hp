@extends('layouts.app')

@section('titre', 'Gouvernance IA')
@section('sprint-actuel', 'Gouvernance IA (Sprint 10)')

@section('contenu')
    <div class="mb-8 pb-5 border-b border-ligne">
        <h1 class="font-titre text-2xl">Gouvernance IA</h1>
        <p class="text-[13px] text-meta mt-2 max-w-2xl">
            Pilotage de l'usage de l'IA par service et par type de document, conformément à l'article 4 du CCTP.
            Toute désactivation s'applique immédiatement, sans incidence sur les délais contractuels.
        </p>
    </div>

    @if (session('succes'))
        <div class="mb-6 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">{{ session('succes') }}</div>
    @endif

    <div class="space-y-px bg-ligne mb-10">
        @foreach ($services as $service)
            @php $restrictionsService = $restrictions->get($service->id, collect()); @endphp
            <div class="bg-papier p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <div class="font-medium">{{ $service->nom }}</div>
                        <div class="font-mono text-[11px] text-meta">{{ $service->etablissement->nom }}</div>
                    </div>
                    <form method="POST" action="{{ route('admin.gouvernance-ia.basculer-service', $service) }}"
                          onsubmit="return confirm('Confirmer ce changement ? Il s\'applique immédiatement.')">
                        @csrf
                        <button type="submit"
                                class="text-[13px] px-3 py-1.5 border {{ $service->ia_autorisee ? 'border-ligne hover:bg-papier-ombre' : 'bg-encre text-papier border-encre' }} transition">
                            IA {{ $service->ia_autorisee ? 'autorisée — désactiver' : 'désactivée — réactiver' }}
                        </button>
                    </form>
                </div>

                @if ($service->ia_autorisee)
                    <div class="pt-4 border-t border-ligne">
                        <div class="font-mono text-[11px] text-meta mb-3">Restrictions par type de document</div>
                        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                            @foreach ($typesDocuments as $type)
                                @php
                                    $restriction = $restrictionsService->firstWhere('type_document', $type);
                                    $autorise = $restriction ? $restriction->ia_autorisee : true;
                                @endphp
                                <form method="POST" action="{{ route('admin.gouvernance-ia.basculer-type', $service) }}">
                                    @csrf
                                    <input type="hidden" name="type_document" value="{{ $type }}">
                                    <button type="submit"
                                            class="w-full text-[12px] px-2 py-2 border {{ $autorise ? 'border-ligne text-meta hover:bg-papier-ombre' : 'border-encre bg-papier-ombre' }} transition">
                                        {{ $type }} — {{ $autorise ? 'IA ok' : 'IA off' }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <h2 class="font-titre text-lg mb-4">Historique des décisions</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-[13px]">
            <thead>
                <tr class="border-b border-encre text-left">
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Date</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Service</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Périmètre</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Décision</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Décidé par</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($historique as $decision)
                    <tr class="border-b border-ligne">
                        <td class="py-3 pr-4 font-mono text-[12px]">{{ $decision->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3 pr-4">{{ $decision->service->nom }}</td>
                        <td class="py-3 pr-4 text-meta">{{ $decision->type_document ?? 'service entier' }}</td>
                        <td class="py-3 pr-4">{{ $decision->ia_autorisee ? 'Autorisée' : 'Désactivée' }}</td>
                        <td class="py-3 pr-4 text-meta">{{ $decision->decidePar->name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-10 text-center text-meta">Aucune décision enregistrée.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection