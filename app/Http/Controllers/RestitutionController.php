<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Document;
use App\Models\JournalAudit;
use App\Services\GenerateurDocumentWord;
use Illuminate\Support\Facades\Storage;

class RestitutionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $rolesTitulaire = ['operateur_titulaire', 'relecteur_valideur', 'admin_titulaire'];

        $query = Demande::where('statut', 'restitue')
            ->with(['service', 'etablissement', 'documents']);

        if (! in_array($user->role->value, $rolesTitulaire, true)) {
            $query->where('service_id', $user->service_id);
        }

        $demandes = $query->latest('date_restitution')->paginate(20);

        return view('restitution.index', compact('demandes'));
    }

    public function aGenerer()
    {
        $demandes = Demande::where('statut', 'valide')
            ->where('verrouille', true)
            ->with(['service', 'etablissement'])
            ->orderBy('valide_le')
            ->paginate(20);

        return view('restitution.a-generer', compact('demandes'));
    }

    public function generer(Demande $demande, GenerateurDocumentWord $generateur)
    {
        if ($demande->statut !== 'valide' || ! $demande->verrouille) {
            abort(403, "Ce dossier doit être validé et verrouillé avant restitution.");
        }

        $transcription = $demande->documents()->where('type', 'transcription')->latest('version')->first();

        if (! $transcription) {
            abort(422, "Aucune transcription trouvée pour ce dossier.");
        }

        $texte = app(\App\Services\StockageChiffre::class)->lire($transcription->chemin_stockage);
        $chemin = $generateur->generer($demande, $texte);

        Document::create([
            'demande_id' => $demande->id,
            'type' => 'document_final',
            'nom_fichier' => basename($chemin),
            'chemin_stockage' => $chemin,
            'format' => 'docx',
            'chiffre' => true,
            'taille_octets' => Storage::disk('documents_prives')->size($chemin),
            'version' => 1,
        ]);

        $demande->update([
            'statut' => 'restitue',
            'date_restitution' => now(),
        ]);

        JournalAudit::tracer('document_restitue', $demande, [
            'fichier' => basename($chemin),
            'genere_par' => auth()->user()->name,
        ]);

        $this->notifierDemandeur($demande);

        return redirect()->route('restitution.a-generer')
            ->with('succes', "Document restitué pour le dossier {$demande->reference}.");
    }

    public function telecharger(Demande $demande, \App\Services\StockageChiffre $stockage)
    {
        $this->autoriserAcces($demande);

        $document = $demande->documents()->where('type', 'document_final')->latest('version')->first();

        if (! $document) {
            abort(404, 'Document non disponible.');
        }

        JournalAudit::tracer('telechargement_document', $demande, ['fichier' => $document->nom_fichier]);

        $contenu = $stockage->lire($document->chemin_stockage);

        return response($contenu, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="'.$document->nom_fichier.'"',
        ]);
    }

    private function autoriserAcces(Demande $demande): void
    {
        $user = auth()->user();
        $rolesTitulaire = ['operateur_titulaire', 'relecteur_valideur', 'admin_titulaire'];

        if (in_array($user->role->value, $rolesTitulaire, true)) {
            return;
        }

        if ($demande->service_id !== $user->service_id) {
            abort(403, "Vous n'avez pas accès à ce document.");
        }
    }

    private function notifierDemandeur(Demande $demande): void
    {
        \Illuminate\Support\Facades\Log::info("Notification demandeur : document restitué pour {$demande->reference}, à destination de {$demande->demandeur->email}");

        $demande->update(['notifie_le' => now()]);
    }
}