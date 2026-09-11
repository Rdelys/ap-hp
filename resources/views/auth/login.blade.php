<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,600;1,400&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { encre: '#000000', papier: '#FFFFFF', ligne: '#DADAD8', meta: '#7A7A77' },
                fontFamily: {
                    titre: ['Newsreader', 'serif'],
                    sans: ['IBM Plex Sans', 'sans-serif'],
                    mono: ['IBM Plex Mono', 'monospace'],
                },
            }}
        }
    </script>
</head>
<body class="bg-papier text-encre font-sans min-h-screen flex flex-col items-center justify-center px-4">

    <div class="mb-10 text-center">
        <div class="font-titre text-2xl">{{ config('app.name') }}</div>
        <div class="font-mono text-[11px] text-meta mt-1.5">environnement de démonstration</div>
    </div>

    <div class="w-full max-w-sm border-t-2 border-encre pt-8">
        <h1 class="font-titre text-xl mb-7">Connexion</h1>

        @if ($errors->any())
            <div class="mb-5 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-[12px] text-meta mb-1.5">Adresse email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border border-ligne px-3 py-2.5 text-[14px] focus:outline-none focus:border-encre transition">
            </div>

            <div>
                <label class="block text-[12px] text-meta mb-1.5">Mot de passe</label>
                <input type="password" name="password" required
                       class="w-full border border-ligne px-3 py-2.5 text-[14px] focus:outline-none focus:border-encre transition">
            </div>

            <label class="flex items-center gap-2 text-[13px] text-meta pt-1">
                <input type="checkbox" name="remember" class="accent-black">
                Se souvenir de moi
            </label>

            <button type="submit"
                    class="w-full bg-encre text-papier py-3 text-[14px] hover:bg-encre/85 transition mt-2">
                Se connecter
            </button>
        </form>
    </div>

    <div class="mt-8 font-mono text-[11px] text-meta text-center max-w-sm">
        comptes de démonstration — identifiants et fonctionnalités évolutifs
    </div>

</body>
</html>