<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\RoleUtilisateur;
use App\Mail\InvitationCompte;
use App\Models\Etablissement;
use App\Models\JournalAudit;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class UtilisateurController extends Controller
{
    public function index()
    {
        $utilisateurs = User::with(['etablissement', 'service'])->orderBy('name')->paginate(25);

        return view('admin.utilisateurs.index', compact('utilisateurs'));
    }

    public function create()
    {
        $etablissements = Etablissement::orderBy('nom')->get();
        $services = Service::orderBy('nom')->get();
        $roles = RoleUtilisateur::cases();

        return view('admin.utilisateurs.create', compact('etablissements', 'services', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'string', 'in:'.implode(',', array_column(RoleUtilisateur::cases(), 'value'))],
            'etablissement_id' => ['nullable', 'exists:etablissements,id'],
            'service_id' => ['nullable', 'exists:services,id'],
        ]);

        $utilisateur = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'etablissement_id' => $validated['etablissement_id'] ?? null,
            'service_id' => $validated['service_id'] ?? null,
            'password' => Hash::make(Str::random(40)), // inutilisable — le compte s'active via l'invitation
            'actif' => true,
            'mot_de_passe_defini' => false,
        ]);

        $this->envoyerInvitation($utilisateur);

        return redirect()->route('admin.utilisateurs.index')
            ->with('succes', "Compte créé pour {$utilisateur->name}. Un email d'invitation a été envoyé.");
    }

    public function edit(User $utilisateur)
    {
        $etablissements = Etablissement::orderBy('nom')->get();
        $services = Service::orderBy('nom')->get();
        $roles = RoleUtilisateur::cases();

        return view('admin.utilisateurs.edit', compact('utilisateur', 'etablissements', 'services', 'roles'));
    }

    public function update(Request $request, User $utilisateur)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$utilisateur->id],
            'role' => ['required', 'string', 'in:'.implode(',', array_column(RoleUtilisateur::cases(), 'value'))],
            'etablissement_id' => ['nullable', 'exists:etablissements,id'],
            'service_id' => ['nullable', 'exists:services,id'],
        ]);

        $utilisateur->update($validated);

        JournalAudit::tracer('modification_utilisateur', $utilisateur, ['par' => auth()->user()->name]);

        return redirect()->route('admin.utilisateurs.index')->with('succes', 'Compte mis à jour.');
    }

    public function basculerActivation(User $utilisateur)
    {
        if ($utilisateur->id === auth()->id()) {
            abort(403, 'Vous ne pouvez pas désactiver votre propre compte.');
        }

        $utilisateur->update(['actif' => ! $utilisateur->actif]);

        JournalAudit::tracer($utilisateur->actif ? 'activation_utilisateur' : 'desactivation_utilisateur', $utilisateur, [
            'par' => auth()->user()->name,
        ]);

        return back()->with('succes', $utilisateur->actif ? 'Compte activé.' : 'Compte désactivé.');
    }

    public function renvoyerInvitation(User $utilisateur)
    {
        if ($utilisateur->mot_de_passe_defini) {
            return back()->with('succes', 'Ce compte est déjà activé.');
        }

        $this->envoyerInvitation($utilisateur);

        return back()->with('succes', 'Invitation renvoyée.');
    }

    private function envoyerInvitation(User $utilisateur): void
    {
        $lien = URL::temporarySignedRoute('invitation.formulaire', now()->addHours(48), ['user' => $utilisateur->id]);

        Mail::to($utilisateur->email)->send(new InvitationCompte($utilisateur, $lien));

        JournalAudit::tracer('invitation_envoyee', $utilisateur, ['par' => auth()->user()->name]);
    }
}