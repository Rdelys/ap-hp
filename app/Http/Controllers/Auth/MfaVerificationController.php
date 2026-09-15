<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\JournalAudit;
use App\Models\User;
use App\Services\AuthentificationMultiFacteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MfaVerificationController extends Controller
{
    public function formulaire(Request $request)
    {
        if (! $request->session()->has('mfa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.mfa-verification');
    }

    public function verifier(Request $request, AuthentificationMultiFacteur $mfa)
    {
        $userId = $request->session()->get('mfa_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $validated = $request->validate(['code' => ['required', 'string', 'max:20']]);
        $user = User::findOrFail($userId);

        $codeValide = ctype_digit($validated['code']) && strlen($validated['code']) === 6
            ? $mfa->verifierCode($user, $validated['code'])
            : $mfa->verifierCodeRecuperation($user, $validated['code']);

        if (! $codeValide) {
            JournalAudit::tracer('mfa_echec', $user);
            throw ValidationException::withMessages(['code' => 'Code invalide.']);
        }

        $remember = $request->session()->pull('mfa_remember', false);
        $request->session()->forget('mfa_user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $user->update(['derniere_connexion_at' => now(), 'tentatives_echouees' => 0, 'verrouille_jusqu_a' => null]);

        JournalAudit::tracer('connexion_reussie_mfa', $user);

        return redirect()->route($user->role->routeDashboard());
    }
}