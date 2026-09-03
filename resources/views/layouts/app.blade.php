<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('titre', 'Tableau de bord')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-black antialiased">

<div class="flex min-h-screen">

    {{-- ===== SIDEBAR (desktop fixe / mobile coulissante) ===== --}}
    <aside id="sidebar"
           class="fixed md:sticky top-0 left-0 h-screen w-64 bg-black text-white flex flex-col z-50
                  -translate-x-full md:translate-x-0 transition-transform duration-200 ease-in-out">

        <div class="px-5 py-5 border-b border-white/10">
            <div class="font-semibold text-sm tracking-tight">{{ config('app.name') }}</div>
            <div class="text-[10px] text-white/40 mt-0.5">Environnement de démonstration</div>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
            @foreach ($menuItems as $item)
                @if ($item['disponible'])
                    <a href="{{ route($item['route_name']) }}"
                       class="flex items-center justify-between px-3 py-2 rounded text-sm transition
                              {{ request()->routeIs($item['route_name']) ? 'bg-white text-black' : 'text-white/80 hover:text-white hover:bg-white/10' }}">
                        <span>{{ $item['label'] }}</span>
                        @if ($item['badge_count'])
                            <span class="bg-white text-black text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center">
                                {{ $item['badge_count'] }}
                            </span>
                        @endif
                    </a>
                @else
                    <span title="Disponible au Sprint {{ $item['sprint'] }}"
                          class="flex items-center justify-between px-3 py-2 rounded text-sm text-white/30 cursor-not-allowed select-none">
                        <span>{{ $item['label'] }}</span>
                        <span class="text-[9px] border border-white/20 rounded px-1.5 py-0.5">S{{ $item['sprint'] }}</span>
                    </span>
                @endif
            @endforeach
        </nav>

        {{-- Compte utilisateur en bas de sidebar --}}
        @auth
            <div class="px-3 py-4 border-t border-white/10">
                <div class="px-2 mb-3 leading-tight">
                    <div class="text-xs font-medium truncate">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-white/50">{{ auth()->user()->role->libelle() }}</div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-xs border border-white/30 rounded px-3 py-2 hover:bg-white hover:text-black transition">
                        Déconnexion
                    </button>
                </form>
            </div>
        @endauth
    </aside>

    {{-- Overlay mobile (clic pour fermer) --}}
    <div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/50 z-40 md:hidden"></div>

    {{-- ===== ZONE PRINCIPALE ===== --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Barre supérieure mobile --}}
        <header class="md:hidden sticky top-0 z-30 bg-black text-white h-14 flex items-center px-4 border-b border-white/10">
            <button id="sidebar-toggle" class="p-2 -ml-2" aria-label="Ouvrir le menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <span class="ml-3 font-semibold text-sm">{{ config('app.name') }}</span>
        </header>

        {{-- Bandeau prototype/démo --}}
        <div class="bg-gray-100 border-b border-black/10 text-center text-xs text-black/70 py-2 px-4">
            Environnement de démonstration — développement en cours.
            @hasSection('sprint-actuel')
                Module actif : @yield('sprint-actuel').
            @else
                Le contenu et les fonctionnalités évoluent au fil des sprints.
            @endif
        </div>

        <main class="flex-1 px-4 md:px-8 py-8">
            @yield('contenu')
        </main>

        <footer class="border-t border-black/10 py-6 text-center text-xs text-black/40">
            Prototype technique — Consultation AP-HP n° 26.093
        </footer>
    </div>

</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const toggleBtn = document.getElementById('sidebar-toggle');

    function ouvrirSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }

    function fermerSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    toggleBtn?.addEventListener('click', ouvrirSidebar);
    overlay?.addEventListener('click', fermerSidebar);
</script>

</body>
</html>