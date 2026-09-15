<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\JournalAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = strtolower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $secondes = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "Trop de tentatives. Réessayez dans {$secondes} secondes.",
            ]);
        }

        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if ($user && $user->estVerrouille()) {
            throw ValidationException::withMessages([
                'email' => 'Compte temporairement verrouillé suite à plusieurs échecs de connexion.',
            ]);
        }

        if (! Auth::validate($credentials)) {
            RateLimiter::hit($throttleKey, 60);

            if ($user) {
                $user->increment('tentatives_echouees');
                if ($user->tentatives_echouees >= 5) {
                    $user->update(['verrouille_jusqu_a' => now()->addMinutes(15)]);
                }
            }

            JournalAudit::tracer('connexion_echouee', null, ['email' => $credentials['email']]);

            throw ValidationException::withMessages(['email' => 'Identifiants incorrects.']);
        }

        RateLimiter::clear($throttleKey);

        if (! $user->actif) {
            throw ValidationException::withMessages(['email' => 'Ce compte est désactivé.']);
        }

        // Double authentification activée : on ne connecte pas encore, on renvoie vers la vérification.
        if ($user->two_factor_confirmed_at) {
            $request->session()->put('mfa_user_id', $user->id);
            $request->session()->put('mfa_remember', $request->boolean('remember'));

            return redirect()->route('mfa.verification.formulaire');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $user->update([
            'tentatives_echouees' => 0,
            'verrouille_jusqu_a' => null,
            'derniere_connexion_at' => now(),
        ]);

        JournalAudit::tracer('connexion_reussie', $user);

        return redirect()->route($user->role->routeDashboard());
    }

    public function logout(Request $request)
    {
        JournalAudit::tracer('deconnexion', $request->user());

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}