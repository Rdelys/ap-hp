<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DecisionGouvernanceIA;
use App\Models\JournalAudit;
use App\Models\RestrictionIAParType;
use App\Models\Service;
use Illuminate\Http\Request;

class GouvernanceIAController extends Controller
{
    private array $typesDocuments = ['courrier', 'cr_hospitalisation', 'cr_operatoire', 'cr_consultation', 'autre'];

    public function index()
    {
        $services = Service::with('etablissement')->orderBy('nom')->get();

        $restrictions = RestrictionIAParType::all()->groupBy('service_id');

        $historique = DecisionGouvernanceIA::with(['service', 'decidePar'])
            ->latest()
            ->limit(30)
            ->get();

        return view('admin.gouvernance-ia.index', [
            'services' => $services,
            'restrictions' => $restrictions,
            'typesDocuments' => $this->typesDocuments,
            'historique' => $historique,
        ]);
    }

    /** Active/désactive l'IA globalement pour un service. */
    public function basculerService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'motif' => ['nullable', 'string', 'max:1000'],
        ]);

        $nouvelleValeur = ! $service->ia_autorisee;
        $service->update(['ia_autorisee' => $nouvelleValeur]);

        DecisionGouvernanceIA::create([
            'service_id' => $service->id,
            'type_document' => null,
            'ia_autorisee' => $nouvelleValeur,
            'motif' => $validated['motif'] ?? null,
            'decide_par_id' => auth()->id(),
        ]);

        JournalAudit::tracer('decision_gouvernance_ia', $service, [
            'perimetre' => 'service entier',
            'ia_autorisee' => $nouvelleValeur,
            'motif' => $validated['motif'] ?? null,
        ]);

        return back()->with('succes', "IA ".($nouvelleValeur ? 'autorisée' : 'désactivée')." pour {$service->nom}.");
    }

    /** Active/désactive l'IA pour un type de document précis, au sein d'un service. */
    public function basculerType(Request $request, Service $service)
    {
        $validated = $request->validate([
            'type_document' => ['required', 'string', 'in:'.implode(',', $this->typesDocuments)],
            'motif' => ['nullable', 'string', 'max:1000'],
        ]);

        $restriction = RestrictionIAParType::firstOrCreate(
            ['service_id' => $service->id, 'type_document' => $validated['type_document']],
            ['ia_autorisee' => true]
        );

        $restriction->update(['ia_autorisee' => ! $restriction->ia_autorisee]);

        DecisionGouvernanceIA::create([
            'service_id' => $service->id,
            'type_document' => $validated['type_document'],
            'ia_autorisee' => $restriction->ia_autorisee,
            'motif' => $validated['motif'] ?? null,
            'decide_par_id' => auth()->id(),
        ]);

        JournalAudit::tracer('decision_gouvernance_ia', $service, [
            'perimetre' => $validated['type_document'],
            'ia_autorisee' => $restriction->ia_autorisee,
            'motif' => $validated['motif'] ?? null,
        ]);

        return back()->with('succes', 'Restriction mise à jour.');
    }
}