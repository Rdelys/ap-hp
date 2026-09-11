<?php

namespace App\Http\Controllers;

use App\Models\JournalAudit;
use App\Models\User;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $query = JournalAudit::with('user')->latest('created_at');

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('utilisateur_id')) {
            $query->where('user_id', $request->input('utilisateur_id'));
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->input('date_debut'));
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->input('date_fin'));
        }

        if ($request->filled('reference')) {
            // recherche par référence de dossier (objet_id résolu via la table demandes)
            $refDemande = \App\Models\Demande::where('reference', 'like', '%'.$request->input('reference').'%')->pluck('id');
            $query->where('objet_type', 'App\\Models\\Demande')->whereIn('objet_id', $refDemande);
        }

        $entrees = $query->paginate(30)->withQueryString();

        $actionsDisponibles = JournalAudit::select('action')->distinct()->orderBy('action')->pluck('action');
        $utilisateurs = User::orderBy('name')->get(['id', 'name']);

        return view('audit.index', compact('entrees', 'actionsDisponibles', 'utilisateurs'));
    }
}