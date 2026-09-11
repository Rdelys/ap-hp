@extends('layouts.app')

@section('titre', 'Suivi & reporting')
@section('sprint-actuel', 'Suivi & reporting (Sprint 8)')

@section('contenu')
    <div class="mb-8 pb-5 border-b border-ligne">
        <h1 class="font-titre text-2xl">Suivi & reporting</h1>
        <p class="font-mono text-[12px] text-meta mt-1">
            Du {{ $dateDebut->format('d/m/Y') }} au {{ $dateFin->format('d/m/Y') }}
        </p>
    </div>

    {{-- Filtres --}}
    <form method="GET" class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-8 pb-8 border-b border-ligne items-end">
        <div>
            <label class="block text-[11px] text-meta mb-1.5">Du</label>
            <input type="date" name="date_debut" value="{{ $dateDebut->format('Y-m-d') }}"
                   class="w-full border border-ligne px-2.5 py-2 text-[13px]">
        </div>
        <div>
            <label class="block text-[11px] text-meta mb-1.5">Au</label>
            <input type="date" name="date_fin" value="{{ $dateFin->format('Y-m-d') }}"
                   class="w-full border border-ligne px-2.5 py-2 text-[13px]">
        </div>

        @if ($etablissements->isNotEmpty())
            <div>
                <label class="block text-[11px] text-meta mb-1.5">Établissement</label>
                <select name="etablissement_id" class="w-full border border-ligne px-2.5 py-2 text-[13px] bg-papier">
                    <option value="">Tous</option>
                    @foreach ($etablissements as $e)
                        <option value="{{ $e->id }}" @selected((int) request('etablissement_id') === $e->id)>{{ $e->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] text-meta mb-1.5">Service</label>
                <select name="service_id" class="w-full border border-ligne px-2.5 py-2 text-[13px] bg-papier">
                    <option value="">Tous</option>
                    @foreach ($services as $s)
                        <option value="{{ $s->id }}" @selected((int) request('service_id') === $s->id)>{{ $s->nom }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="flex gap-2">
            <button type="submit" class="bg-encre text-papier px-4 py-2 text-[13px] hover:bg-encre/85 transition">
                Filtrer
            </button>
            <a href="{{ route('rapports.export', request()->query()) }}"
               class="border border-ligne px-4 py-2 text-[13px] hover:bg-papier-ombre transition">
                Export CSV
            </a>
        </div>
    </form>

    {{-- Indicateurs clés --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-px bg-ligne mb-10">
        <div class="bg-papier p-5">
            <div class="font-mono text-[11px] text-meta mb-1.5">Dossiers sur la période</div>
            <div class="font-titre text-3xl">{{ $indicateurs['total'] }}</div>
        </div>
        <div class="bg-papier p-5">
            <div class="font-mono text-[11px] text-meta mb-1.5">Restitués</div>
            <div class="font-titre text-3xl">{{ $indicateurs['restitues'] }}</div>
        </div>
        <div class="bg-papier p-5">
            <div class="font-mono text-[11px] text-meta mb-1.5">Hors délai</div>
            <div class="font-titre text-3xl">{{ $indicateurs['hors_delai'] }}</div>
        </div>
        <div class="bg-papier p-5">
            <div class="font-mono text-[11px] text-meta mb-1.5">Taux de respect SLA</div>
            <div class="font-titre text-3xl">{{ $indicateurs['taux_sla'] !== null ? $indicateurs['taux_sla'].'%' : '—' }}</div>
        </div>
        <div class="bg-papier p-5">
            <div class="font-mono text-[11px] text-meta mb-1.5">Délai moyen</div>
            <div class="font-titre text-3xl">{{ $indicateurs['delai_moyen_heures'] !== null ? $indicateurs['delai_moyen_heures'].'h' : '—' }}</div>
        </div>
    </div>

    {{-- Graphique --}}
    @if ($parService->isNotEmpty())
        <div class="mb-10 border border-ligne p-5">
            <div class="font-mono text-[11px] text-meta mb-4">Volumes par service</div>
            <canvas id="graphique-volumes" height="90"></canvas>
        </div>
    @endif

    {{-- Tableau détaillé --}}
    <div class="overflow-x-auto">
        <table class="w-full text-[13.5px]">
            <thead>
                <tr class="border-b border-encre text-left">
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Établissement</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Service</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Total</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Restitués</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Hors délai</th>
                    <th class="py-3 pr-4 font-mono text-[11px] text-meta font-normal">Taux SLA</th>
                    <th class="py-3 font-mono text-[11px] text-meta font-normal">Délai moyen</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($parService as $ligne)
                    <tr class="border-b border-ligne">
                        <td class="py-3 pr-4">{{ $ligne->etablissement_nom }}</td>
                        <td class="py-3 pr-4 text-meta">{{ $ligne->service_nom }}</td>
                        <td class="py-3 pr-4 font-mono">{{ $ligne->total }}</td>
                        <td class="py-3 pr-4 font-mono">{{ $ligne->restitues }}</td>
                        <td class="py-3 pr-4 font-mono">{{ $ligne->hors_delai }}</td>
                        <td class="py-3 pr-4 font-mono">{{ $ligne->taux_sla !== null ? $ligne->taux_sla.'%' : '—' }}</td>
                        <td class="py-3 font-mono">{{ $ligne->delai_moyen_heures !== null ? $ligne->delai_moyen_heures.'h' : '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-meta">Aucune donnée sur cette période.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($parService->isNotEmpty())
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
        <script>
            const ctx = document.getElementById('graphique-volumes');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($parService->pluck('service_nom')),
                    datasets: [
                        {
                            label: 'Restitués',
                            data: @json($parService->pluck('restitues')),
                            backgroundColor: '#000000',
                        },
                        {
                            label: 'Hors délai',
                            data: @json($parService->pluck('hors_delai')),
                            backgroundColor: '#DADAD8',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom', labels: { font: { family: 'IBM Plex Sans', size: 12 } } } },
                    scales: {
                        x: { ticks: { font: { family: 'IBM Plex Sans', size: 11 } } },
                        y: { ticks: { font: { family: 'IBM Plex Mono', size: 11 } }, beginAtZero: true },
                    },
                },
            });
        </script>
    @endif
@endsection