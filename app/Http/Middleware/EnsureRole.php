<?php

namespace App\Http\Middleware;

use App\Enums\RoleUtilisateur;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (! $user->actif) {
            auth()->logout();
            abort(403, 'Compte désactivé.');
        }

        $rolesAutorises = array_map(fn ($r) => RoleUtilisateur::from($r), $roles);

        if (! in_array($user->role, $rolesAutorises, true)) {
            abort(403, "Vous n'avez pas les droits pour accéder à cette ressource.");
        }

        return $next($request);
    }
}