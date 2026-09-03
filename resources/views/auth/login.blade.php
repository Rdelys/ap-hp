<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — {{ config('app.name') }}</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white text-black min-h-screen flex flex-col items-center justify-center px-4">

    <div class="mb-8 text-center">
        <div class="text-lg font-semibold tracking-tight">{{ config('app.name') }}</div>
        <div class="text-xs text-black/50 mt-1">Environnement de démonstration</div>
    </div>

    <div class="border border-black/10 rounded-lg p-8 w-full max-w-sm">
        <h1 class="text-base font-semibold mb-6">Connexion</h1>

        @if ($errors->any())
            <div class="mb-4 text-xs text-black bg-gray-100 border border-black/20 rounded p-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs mb-1 text-black/70">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border border-black/20 rounded px-3 py-2 text-sm focus:outline-none focus:border-black">
            </div>

            <div>
                <label class="block text-xs mb-1 text-black/70">Mot de passe</label>
                <input type="password" name="password" required
                       class="w-full border border-black/20 rounded px-3 py-2 text-sm focus:outline-none focus:border-black">
            </div>

            <label class="flex items-center gap-2 text-xs text-black/70">
                <input type="checkbox" name="remember" class="accent-black">
                Se souvenir de moi
            </label>

            <button type="submit" class="w-full bg-black text-white rounded py-2.5 text-sm hover:bg-black/80 transition">
                Se connecter
            </button>
        </form>
    </div>

    <div class="mt-6 text-[11px] text-black/40 text-center max-w-sm">
        Comptes de démonstration — les identifiants et fonctionnalités sont susceptibles d'évoluer.
    </div>

</body>
</html>