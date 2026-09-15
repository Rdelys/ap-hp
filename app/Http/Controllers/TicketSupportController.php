<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\JournalAudit;
use App\Models\MessageTicket;
use App\Models\TicketSupport;
use Illuminate\Http\Request;

class TicketSupportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $rolesTitulaire = ['operateur_titulaire', 'relecteur_valideur', 'admin_titulaire', 'admin_aphp'];

        $query = TicketSupport::with(['demandeur', 'assigneA'])->latest();

        // Un utilisateur "métier" (secrétariat/référent) ne voit que ses propres tickets.
        if (! in_array($user->role->value, $rolesTitulaire, true)) {
            $query->where('demandeur_id', $user->id);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        $tickets = $query->paginate(20)->withQueryString();

        return view('support.index', compact('tickets'));
    }

    public function create()
    {
        $mesDemandes = Demande::where('demandeur_id', auth()->id())->latest()->limit(50)->get();

        return view('support.create', compact('mesDemandes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categorie' => ['required', 'string', 'in:fichier_audio,document,acces_plateforme,compte,delai,incident_securite,autre'],
            'sujet' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'demande_id' => ['nullable', 'exists:demandes,id'],
        ]);

        // Un incident de sécurité est toujours critique par défaut — cohérent avec l'exigence "sans délai" du CCTP.
        $criticite = $validated['categorie'] === 'incident_securite' ? 'critique' : 'normale';

        $ticket = TicketSupport::create([
            ...$validated,
            'demandeur_id' => auth()->id(),
            'criticite' => $criticite,
            'statut' => 'ouvert',
        ]);

        JournalAudit::tracer('ticket_support_ouvert', $ticket, [
            'categorie' => $ticket->categorie,
            'criticite' => $ticket->criticite,
        ]);

        return redirect()->route('support.show', $ticket)
            ->with('succes', $ticket->criticite === 'critique'
                ? 'Incident signalé — traitement prioritaire.'
                : 'Votre demande a été enregistrée.');
    }

    public function show(TicketSupport $ticket)
    {
        $this->autoriserAcces($ticket);

        $ticket->load('messages.auteur', 'demandeur', 'assigneA', 'demande');

        return view('support.show', compact('ticket'));
    }

    public function repondre(Request $request, TicketSupport $ticket)
    {
        $this->autoriserAcces($ticket);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        MessageTicket::create([
            'ticket_id' => $ticket->id,
            'auteur_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        $rolesTitulaire = ['operateur_titulaire', 'relecteur_valideur', 'admin_titulaire', 'admin_aphp'];
        if (in_array(auth()->user()->role->value, $rolesTitulaire, true) && $ticket->statut === 'ouvert') {
            $ticket->update(['statut' => 'en_cours', 'assigne_a_id' => $ticket->assigne_a_id ?? auth()->id()]);
        }

        JournalAudit::tracer('message_ticket_ajoute', $ticket);

        return back()->with('succes', 'Message envoyé.');
    }

    public function changerStatut(Request $request, TicketSupport $ticket)
    {
        $validated = $request->validate([
            'statut' => ['required', 'string', 'in:ouvert,en_cours,resolu,ferme'],
        ]);

        $ticket->update([
            'statut' => $validated['statut'],
            'resolu_le' => in_array($validated['statut'], ['resolu', 'ferme']) ? now() : null,
        ]);

        JournalAudit::tracer('statut_ticket_modifie', $ticket, ['nouveau_statut' => $validated['statut']]);

        return back()->with('succes', 'Statut mis à jour.');
    }

    private function autoriserAcces(TicketSupport $ticket): void
    {
        $user = auth()->user();
        $rolesTitulaire = ['operateur_titulaire', 'relecteur_valideur', 'admin_titulaire', 'admin_aphp'];

        if (in_array($user->role->value, $rolesTitulaire, true)) {
            return;
        }

        if ($ticket->demandeur_id !== $user->id) {
            abort(403, "Vous n'avez pas accès à ce ticket.");
        }
    }
}