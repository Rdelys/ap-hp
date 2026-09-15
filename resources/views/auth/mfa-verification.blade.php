<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,600;1,400&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { encre: '#000000', papier: '#FFFFFF', ligne: '#DADAD8', meta: '#7A7A77' },
            fontFamily: { titre: ['Newsreader', 'serif'], sans: ['IBM Plex Sans', 'sans-serif'], mono: ['IBM Plex Mono', 'monospace'] },
        }}}
    </script>
</head>
<body class="bg-papier text-encre font-sans min-h-screen flex flex-col items-center justify-center px-4">

    <div class="mb-10 text-center">
        <div class="font-titre text-2xl">{{ config('app.name') }}</div>
        <div class="font-mono text-[11px] text-meta mt-1.5">vérification en deux étapes</div>
    </div>

    <div class="w-full max-w-sm border-t-2 border-encre pt-8">
        <h1 class="font-titre text-xl mb-2">Code de vérification</h1>
        <p class="text-[13px] text-meta mb-7">Saisissez le code à 6 chiffres de votre application d'authentification, ou l'un de vos codes de récupération.</p>

        @if ($errors->any())
            <div class="mb-5 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('mfa.verification.verifier') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-[12px] text-meta mb-1.5">Code</label>
                <input type="text" name="code" required autofocus autocomplete="one-time-code"
                       class="w-full border border-ligne px-3 py-2.5 text-[14px] font-mono tracking-widest focus:outline-none focus:border-encre">
            </div>
            <button type="submit" class="w-full bg-encre text-papier py-3 text-[14px] hover:bg-encre/85 transition">
                Vérifier
            </button>
        </form>
    </div>
</body>
</html>