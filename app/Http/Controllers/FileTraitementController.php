<?php

namespace App\Http\Controllers;

use App\Models\Demande;

class FileTraitementController extends Controller
{
    public function index()
    {
        $demandes = Demande::whereNotIn('statut', ['restitue'])
            ->orderByRaw("FIELD(niveau_urgence, 'urgent', 'normal', 'economique')")
            ->orderBy('echeance_sla')
            ->with(['service', 'etablissement', 'demandeur'])
            ->paginate(25);

        return view('file-traitement.index', compact('demandes'));
    }
}