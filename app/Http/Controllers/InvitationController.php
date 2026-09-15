<?php

namespace App\Http\Controllers;

use App\Models\JournalAudit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class InvitationController extends Controller
{
    public function formulaire(User $user, Request $request)
    {
        if ($user->mot_de_passe_defini) {
            abort(403, 'Ce compte a déjà été activé.');
        }

        return view('invitation.definir-mot-de-passe', [
            'user' => $user,
            'urlSoumission' => $request->fullUrl(),
        ]);
    }

    public function definir(Request $request, User $user)
    {
        if ($user->mot_de_passe_defini) {
            abort(403, 'Ce compte a déjà été activé.');
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(10)->mixedCase()->numbers()->symbols()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
            'mot_de_passe_defini' => true,
        ]);

        JournalAudit::tracer('activation_compte', $user);

        return redirect()->route('login')->with('succes', 'Votre mot de passe a été défini. Vous pouvez maintenant vous connecter.');
    }
}