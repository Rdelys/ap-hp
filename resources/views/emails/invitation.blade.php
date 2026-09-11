<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"></head>
<body style="font-family: sans-serif; color: #000; max-width: 480px; margin: 0 auto; padding: 32px 16px;">

    <p>Bonjour {{ $user->name }},</p>

    <p>
        Un compte vient d'être créé pour vous sur la plateforme {{ config('app.name') }},
        avec le rôle : <strong>{{ $user->role->libelle() }}</strong>.
    </p>

    <p>Pour l'activer, définissez votre mot de passe en cliquant sur le lien ci-dessous :</p>

    <p style="margin: 24px 0;">
        <a href="{{ $lienInvitation }}" style="background: #000; color: #fff; padding: 12px 20px; text-decoration: none; display: inline-block;">
            Définir mon mot de passe
        </a>
    </p>

    <p style="font-size: 13px; color: #666;">
        Ce lien expire dans 48 heures. Si vous n'êtes pas à l'origine de cette demande, ignorez ce message.
    </p>

    <p style="font-size: 12px; color: #999; margin-top: 32px;">
        Environnement de démonstration — Consultation AP-HP n° 26.093
    </p>

</body>
</html>