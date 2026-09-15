<?php

namespace App\Http\Controllers;

use App\Models\JournalAudit;
use App\Services\AuthentificationMultiFacteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MfaController extends Controller
{
    public function index(Request $request)
    {
        return view('securite.index', ['user' => $request->user()]);
    }

    public function afficherConfiguration(Request $request, AuthentificationMultiFacteur $mfa)
    {
        if ($request->user()->two_factor_confirmed_at) {
            return redirect()->route('securite.index');
        }

        $secret = session('mfa_secret_temporaire') ?? $mfa->genererSecret();
        session(['mfa_secret_temporaire' => $secret]);

        $qrCodeSvg = $mfa->genererQrCodeSvg($request->user(), $secret);

        return view('securite.mfa-configurer', compact('secret', 'qrCodeSvg'));
    }

    public function confirmer(Request $request, AuthentificationMultiFacteur $mfa)
    {
        $validated = $request->validate(['code' => ['required', 'digits:6']]);

        $secret = session('mfa_secret_temporaire');

        if (! $secret || ! $mfa->verifierCodeAvecSecret($secret, $validated['code'])) {
            return back()->withErrors(['code' => 'Code invalide.']);
        }

        $codesRecuperation = $mfa->genererCodesRecuperation();

        $request->user()->update([
            'two_factor_secret' => Crypt::encryptString($secret),
            'two_factor_recovery_codes' => json_encode(array_map(fn ($c) => Hash::make($c), $codesRecuperation)),
            'two_factor_confirmed_at' => now(),
        ]);

        session()->forget('mfa_secret_temporaire');

        JournalAudit::tracer('mfa_active', $request->user());

        return view('securite.mfa-codes-recuperation', ['codes' => $codesRecuperation]);
    }

    public function desactiver(Request $request)
    {
        $request->validate(['password' => ['required', 'current_password']]);

        $request->user()->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);

        JournalAudit::tracer('mfa_desactive', $request->user());

        return redirect()->route('securite.index')->with('succes', 'Authentification à deux facteurs désactivée.');
    }
}