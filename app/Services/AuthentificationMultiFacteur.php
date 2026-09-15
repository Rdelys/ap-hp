<?php

namespace App\Services;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

class AuthentificationMultiFacteur
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function genererSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /** Génère un QR code SVG en local — le secret ne quitte jamais le serveur vers un tiers. */
    public function genererQrCodeSvg(User $user, string $secretEnClair): string
    {
        $uri = $this->google2fa->getQRCodeUrl(config('app.name'), $user->email, $secretEnClair);

        $renderer = new ImageRenderer(new RendererStyle(220, 1), new SvgImageBackEnd());

        return (new Writer($renderer))->writeString($uri);
    }

    public function verifierCodeAvecSecret(string $secretEnClair, string $code): bool
    {
        return (bool) $this->google2fa->verifyKey($secretEnClair, $code);
    }

    public function verifierCode(User $user, string $code): bool
    {
        if (! $user->two_factor_secret) {
            return false;
        }

        return $this->verifierCodeAvecSecret(Crypt::decryptString($user->two_factor_secret), $code);
    }

    public function genererCodesRecuperation(): array
    {
        return collect(range(1, 8))
            ->map(fn () => Str::upper(Str::random(4).'-'.Str::random(4)))
            ->all();
    }

    public function verifierCodeRecuperation(User $user, string $code): bool
    {
        $codes = json_decode($user->two_factor_recovery_codes ?? '[]', true);

        foreach ($codes as $index => $hash) {
            if (Hash::check($code, $hash)) {
                unset($codes[$index]);
                $user->update(['two_factor_recovery_codes' => json_encode(array_values($codes))]);
                return true;
            }
        }

        return false;
    }
}