<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDemandeRequest;
use App\Models\Demande;
use App\Models\Document;
use App\Models\JournalAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemandeController extends Controller
{
    /** Liste des demandes du service de l'utilisateur connecté. */
    public function index()
    {
        $user = auth()->user();

        $demandes = Demande::where('service_id', $user->service_id)
            ->with('demandeur')
            ->latest('date_depot')
            ->paginate(20);

        return view('demandes.index', compact('demandes'));
    }

    public function create()
    {
        return view('demandes.create');
    }

    public function store(StoreDemandeRequest $request)
    {
        $user = $request->user();

        if (! $user->etablissement_id || ! $user->service_id) {
            abort(422, "Votre compte n'est rattaché à aucun établissement/service. Contactez un administrateur.");
        }

        $validated = $request->validated();
        $fichier = $validated['fichier_audio'];

        $demande = DB::transaction(function () use ($validated, $fichier, $user) {

            $demande = Demande::create([
                'etablissement_id' => $user->etablissement_id,
                'service_id' => $user->service_id,
                'demandeur_id' => $user->id,
                'type_document' => $validated['type_document'],
                'nom_demandeur' => $validated['nom_demandeur'] ?? null,
                'numero_dictant' => $validated['numero_dictant'] ?? null,
                'niveau_urgence' => $validated['niveau_urgence'],
                'statut' => 'depose',
            ]);

            // Nom de fichier généré (jamais le nom d'origine tel quel : évite traversal / collisions)
            $extension = $fichier->getClientOriginalExtension();
            $nomStocke = $demande->reference.'.'.$extension;

            $chemin = $fichier->storeAs(
                'audio/'.$demande->reference,
                $nomStocke,
                'documents_prives'
            );

            Document::create([
                'demande_id' => $demande->id,
                'type' => 'audio_source',
                'nom_fichier' => $fichier->getClientOriginalName(),
                'chemin_stockage' => $chemin,
                'format' => $extension,
                'taille_octets' => $fichier->getSize(),
                'version' => 1,
                'chiffre' => false, // chiffrement au repos prévu en Phase B (hébergement HDS)
            ]);

            return $demande;
        });

        JournalAudit::tracer('depot_audio', $demande, [
            'reference' => $demande->reference,
            'type_document' => $demande->type_document,
            'niveau_urgence' => $demande->niveau_urgence,
        ]);

        return redirect()
            ->route('demandes.index')
            ->with('succes', "Demande déposée avec succès. Référence : {$demande->reference}");
    }

    public function show(Demande $demande)
    {
        $this->autoriserAccesDemande($demande);

        $demande->load('documents', 'demandeur', 'service', 'etablissement');

        return view('demandes.show', compact('demande'));
    }

    /** Vérifie que l'utilisateur a le droit de voir cette demande (même service). */
    private function autoriserAccesDemande(Demande $demande): void
    {
        $user = auth()->user();

        $rolesTitulaire = ['operateur_titulaire', 'relecteur_valideur', 'admin_titulaire'];

        if (in_array($user->role->value, $rolesTitulaire, true)) {
            return; // le titulaire voit toutes les demandes (traitement transverse)
        }

        if ($demande->service_id !== $user->service_id) {
            abort(403, "Vous n'avez pas accès à cette demande.");
        }
    }
}