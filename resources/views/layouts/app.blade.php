<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('titre', 'Tableau de bord')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,600;1,400&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        encre: '#000000',
                        papier: '#FFFFFF',
                        'papier-ombre': '#F4F4F4',
                        ligne: '#DADAD8',
                        meta: '#7A7A77',
                    },
                    fontFamily: {
                        titre: ['Newsreader', 'serif'],
                        sans: ['IBM Plex Sans', 'sans-serif'],
                        mono: ['IBM Plex Mono', 'monospace'],
                    },
                },
            },
        }
    </script>
</head>
<body class="bg-papier text-encre font-sans antialiased">

<div class="flex min-h-screen">

    {{-- ===== SIDEBAR ===== --}}
    <aside id="sidebar"
           class="fixed md:sticky top-0 left-0 h-screen w-64 bg-encre text-papier flex flex-col z-50
                  -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out">

        <div class="px-6 py-6 border-b border-white/10">
            <div class="font-titre text-lg leading-none">{{ config('app.name') }}</div>
            <div class="font-mono text-[10px] text-white/40 mt-1.5 tracking-wide">environnement de démonstration</div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-5 space-y-0.5">
            @foreach ($menuItems as $item)
                @if ($item['disponible'])
                    <a href="{{ route($item['route_name']) }}"
                       class="flex items-center justify-between px-3 py-2.5 text-[13.5px] transition
                              {{ request()->routeIs($item['route_name'])
                                    ? 'bg-papier text-encre'
                                    : 'text-white/75 hover:text-white hover:bg-white/[0.07]' }}">
                        <span>{{ $item['label'] }}</span>
                        @if ($item['badge_count'])
                            <span class="font-mono text-[10px] bg-papier text-encre w-5 h-5 rounded-full flex items-center justify-center">
                                {{ $item['badge_count'] }}
                            </span>
                        @endif
                    </a>
                @else
                    <div title="Disponible au sprint {{ $item['sprint'] }}"
                         class="flex items-center justify-between px-3 py-2.5 text-[13.5px] text-white/25 cursor-not-allowed select-none">
                        <span>{{ $item['label'] }}</span>
                        <span class="font-mono text-[9px] border border-white/15 px-1.5 py-0.5">S{{ $item['sprint'] }}</span>
                    </div>
                @endif
            @endforeach
        </nav>

        @auth
            <div class="px-4 py-5 border-t border-white/10">
                <div class="mb-3 leading-tight">
                    <div class="text-[13px] font-medium truncate">{{ auth()->user()->name }}</div>
                    <div class="font-mono text-[10px] text-white/40 mt-0.5">{{ auth()->user()->role->libelle() }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-[12px] border border-white/25 px-3 py-2 hover:bg-papier hover:text-encre transition">
                        Se déconnecter
                    </button>
                </form>
            </div>
        @endauth
    </aside>

    <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/50 z-40 md:hidden"></div>

    {{-- ===== ZONE PRINCIPALE ===== --}}
    <div class="flex-1 flex flex-col min-w-0">

        <header class="md:hidden sticky top-0 z-30 bg-encre text-papier h-14 flex items-center px-4 border-b border-white/10">
            <button id="sidebar-toggle" class="p-2 -ml-2" aria-label="Ouvrir le menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <span class="ml-3 font-titre text-base">{{ config('app.name') }}</span>
        </header>

        <div class="bg-papier-ombre border-b border-ligne text-center text-[12px] text-meta py-2 px-4">
            Environnement de démonstration — développement en cours.
            @hasSection('sprint-actuel')
                Module actif : @yield('sprint-actuel').
            @else
                Le contenu évolue au fil des sprints.
            @endif
        </div>

        <main class="flex-1 px-5 md:px-12 py-10 max-w-6xl w-full mx-auto">
            @yield('contenu')
        </main>

        <footer class="border-t border-ligne py-6 text-center font-mono text-[11px] text-meta">
            Prototype technique — Consultation AP-HP n° 26.093
        </footer>
    </div>

</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const toggleBtn = document.getElementById('sidebar-toggle');

    toggleBtn?.addEventListener('click', () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    });
    overlay?.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
</script>

</body>
</html>