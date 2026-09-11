<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Etablissement;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RapportController extends Controller
{
    public function index(Request $request)
    {
        [$dateDebut, $dateFin] = $this->resoudrePeriode($request);
        $query = $this->requeteBase($request, $dateDebut, $dateFin);

        $indicateurs = $this->calculerIndicateurs((clone $query));
        $parService = $this->calculerParService((clone $query));

        $etablissements = auth()->user()->role->value === 'referent_service'
            ? collect()
            : Etablissement::orderBy('nom')->get();

        $services = auth()->user()->role->value === 'referent_service'
            ? collect()
            : Service::orderBy('nom')->get();

        return view('rapports.index', compact('indicateurs', 'parService', 'dateDebut', 'dateFin', 'etablissements', 'services'));
    }

    public function exporterCsv(Request $request)
    {
        [$dateDebut, $dateFin] = $this->resoudrePeriode($request);
        $query = $this->requeteBase($request, $dateDebut, $dateFin);
        $parService = $this->calculerParService((clone $query));

        $nomFichier = 'suivi_lot3_'.$dateDebut->format('Ymd').'_'.$dateFin->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($parService) {
            $sortie = fopen('php://output', 'w');
            fputcsv($sortie, ['Référence marché', '26.093'], ';');
            fputcsv($sortie, [], ';');
            fputcsv($sortie, ['Établissement', 'Service', 'Total dossiers', 'Restitués', 'Hors délai', 'Taux respect SLA (%)', 'Délai moyen (h)'], ';');

            foreach ($parService as $ligne) {
                fputcsv($sortie, [
                    $ligne->etablissement_nom,
                    $ligne->service_nom,
                    $ligne->total,
                    $ligne->restitues,
                    $ligne->hors_delai,
                    $ligne->taux_sla,
                    $ligne->delai_moyen_heures,
                ], ';');
            }

            fclose($sortie);
        }, $nomFichier, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function resoudrePeriode(Request $request): array
    {
        $dateDebut = $request->filled('date_debut')
            ? \Carbon\Carbon::parse($request->input('date_debut'))->startOfDay()
            : now()->startOfMonth();

        $dateFin = $request->filled('date_fin')
            ? \Carbon\Carbon::parse($request->input('date_fin'))->endOfDay()
            : now()->endOfDay();

        return [$dateDebut, $dateFin];
    }

    private function requeteBase(Request $request, $dateDebut, $dateFin)
    {
        $user = auth()->user();

        $query = Demande::query()->whereBetween('demandes.date_depot', [$dateDebut, $dateFin]);

        if ($user->role->value === 'referent_service') {
            $query->where('demandes.service_id', $user->service_id);
        } else {
            if ($request->filled('etablissement_id')) {
                $query->where('demandes.etablissement_id', $request->input('etablissement_id'));
            }
            if ($request->filled('service_id')) {
                $query->where('demandes.service_id', $request->input('service_id'));
            }
        }

        return $query;
    }

    private function calculerIndicateurs($query): array
    {
        $total = (clone $query)->count();
        $restitues = (clone $query)->where('statut', 'restitue')->count();
        $horsDelai = (clone $query)->where('statut', 'hors_delai')->count();
        $urgents = (clone $query)->where('niveau_urgence', 'urgent')->count();

        $traites = $restitues + $horsDelai;
        $tauxSla = $traites > 0 ? round(($restitues / $traites) * 100, 1) : null;

        $delaiMoyenHeures = (clone $query)
            ->where('statut', 'restitue')
            ->whereNotNull('date_restitution')
            ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, date_depot, date_restitution)'));

        return [
            'total' => $total,
            'restitues' => $restitues,
            'hors_delai' => $horsDelai,
            'urgents' => $urgents,
            'taux_sla' => $tauxSla,
            'delai_moyen_heures' => $delaiMoyenHeures ? round($delaiMoyenHeures / 60, 1) : null,
        ];
    }

    private function calculerParService($query)
    {
        return (clone $query)
            ->join('services', 'services.id', '=', 'demandes.service_id')
            ->join('etablissements', 'etablissements.id', '=', 'demandes.etablissement_id')
            ->select(
                'etablissements.nom as etablissement_nom',
                'services.nom as service_nom',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN demandes.statut = 'restitue' THEN 1 ELSE 0 END) as restitues"),
                DB::raw("SUM(CASE WHEN demandes.statut = 'hors_delai' THEN 1 ELSE 0 END) as hors_delai"),
                DB::raw('ROUND(AVG(CASE WHEN demandes.statut = "restitue" THEN TIMESTAMPDIFF(MINUTE, demandes.date_depot, demandes.date_restitution) END) / 60, 1) as delai_moyen_heures')
            )
            ->groupBy('etablissements.nom', 'services.nom')
            ->orderBy('etablissements.nom')
            ->get()
            ->map(function ($ligne) {
                $traites = $ligne->restitues + $ligne->hors_delai;
                $ligne->taux_sla = $traites > 0 ? round(($ligne->restitues / $traites) * 100, 1) : null;
                return $ligne;
            });
    }
}