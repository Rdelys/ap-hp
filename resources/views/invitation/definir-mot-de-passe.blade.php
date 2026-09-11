<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activation du compte — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,400;0,500;0,600;1,400&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { encre: '#000000', papier: '#FFFFFF', ligne: '#DADAD8', meta: '#7A7A77' },
            fontFamily: { titre: ['Newsreader', 'serif'], sans: ['IBM Plex Sans', 'sans-serif'] },
        }}}
    </script>
</head>
<body class="bg-papier text-encre font-sans min-h-screen flex flex-col items-center justify-center px-4">

    <div class="mb-10 text-center">
        <div class="font-titre text-2xl">{{ config('app.name') }}</div>
        <div class="text-[11px] text-meta mt-1.5">activation de compte</div>
    </div>

    <div class="w-full max-w-sm border-t-2 border-encre pt-8">
        <h1 class="font-titre text-xl mb-1">Bienvenue, {{ $user->name }}</h1>
        <p class="text-[13px] text-meta mb-7">Définissez votre mot de passe pour activer votre compte.</p>

        @if ($errors->any())
            <div class="mb-5 text-[13px] bg-papier-ombre border-l-2 border-encre pl-3 py-2">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('invitation.definir', $user) }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-[12px] text-meta mb-1.5">Nouveau mot de passe</label>
                <input type="password" name="password" required minlength="10"
                       class="w-full border border-ligne px-3 py-2.5 text-[14px] focus:outline-none focus:border-encre">
                <p class="text-[11px] text-meta mt-1">10 caractères minimum.</p>
            </div>
            <div>
                <label class="block text-[12px] text-meta mb-1.5">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" required minlength="10"
                       class="w-full border border-ligne px-3 py-2.5 text-[14px] focus:outline-none focus:border-encre">
            </div>
            <button type="submit" class="w-full bg-encre text-papier py-3 text-[14px] hover:bg-encre/85 transition">
                Activer mon compte
            </button>
        </form>
    </div>
</body>
</html>