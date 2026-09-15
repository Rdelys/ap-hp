<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcerMfaAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user
            && in_array($user->role->value, config('securite.roles_mfa_obligatoire', []), true)
            && ! $user->two_factor_confirmed_at
            && ! $request->routeIs('securite.*')
            && ! $request->routeIs('logout')
        ) {
            return redirect()->route('securite.mfa.configurer')
                ->with('avertissement', "Votre rôle exige l'activation de la double authentification avant de continuer.");
        }

        return $next($request);
    }
}