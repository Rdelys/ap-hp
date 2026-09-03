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

        // Limitation anti-bruteforce (indépendante du verrouillage en base)
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

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            if ($user) {
                $user->increment('tentatives_echouees');
                if ($user->tentatives_echouees >= 5) {
                    $user->update(['verrouille_jusqu_a' => now()->addMinutes(15)]);
                }
            }

            JournalAudit::tracer('connexion_echouee', null, ['email' => $credentials['email']]);

            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate(); // anti session fixation

        $authUser = Auth::user();

        if (! $authUser->actif) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Ce compte est désactivé.',
            ]);
        }

        $authUser->update([
            'tentatives_echouees' => 0,
            'verrouille_jusqu_a' => null,
            'derniere_connexion_at' => now(),
        ]);

        JournalAudit::tracer('connexion_reussie', $authUser);

        return redirect()->route($authUser->role->routeDashboard());
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