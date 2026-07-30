<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use App\Models\TypeReclamation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!in_array(auth()->user()->role, ['admin', 'gestionnaire'])) {
                abort(403, __('Accès réservé aux administrateurs et gestionnaires.'));
            }
            return $next($request);
        });
    }

    public function index()
    {
        $types = TypeReclamation::where('actif', true)->get();
        return view('reports.index', compact('types'));
    }

    public function exportPdfReclamations(Request $request)
    {
        $query = Reclamation::with(['client', 'type', 'sousType', 'assigne']);

        if (auth()->user()->role === 'agent') {
            $query->where('assigne_a', auth()->id());
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('sujet', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        if ($typeId = $request->input('type_id')) {
            $query->where('type_id', $typeId);
        }
        if ($statut = $request->input('statut')) {
            $query->where('statut', $statut);
        }
        if ($priorite = $request->input('priorite')) {
            $query->where('priorite', $priorite);
        }
        if ($assigneA = $request->input('assigne_a')) {
            $query->where('assigne_a', $assigneA);
        }
        if ($dateDebut = $request->input('date_debut')) {
            $query->whereDate('date_creation', '>=', $dateDebut);
        }
        if ($dateFin = $request->input('date_fin')) {
            $query->whereDate('date_creation', '<=', $dateFin);
        }

        $reclamations = $query->latest('date_creation')->get();

        $pdf = Pdf::loadView('reports.pdf.reclamations', compact('reclamations'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('reclamations-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportPdfReclamation(Reclamation $reclamation)
    {
        if (auth()->user()->role === 'agent' && $reclamation->assigne_a !== auth()->id()) {
            abort(403);
        }

        $reclamation->load(['client', 'type', 'sousType', 'assigne', 'historiqueStatuts.utilisateur', 'messages.expediteur', 'piecesJointes']);

        $pdf = Pdf::loadView('reports.pdf.reclamation', compact('reclamation'));

        return $pdf->download('reclamation-' . $reclamation->reference . '-' . now()->format('Y-m-d') . '.pdf');
    }

    public function exportPdfStatistiques(Request $request)
    {
        $dateDebut = $request->input('date_debut', now()->startOfMonth()->format('Y-m-d'));
        $dateFin = $request->input('date_fin', now()->format('Y-m-d'));

        $query = Reclamation::whereBetween('date_creation', [$dateDebut, $dateFin . ' 23:59:59']);

        if (auth()->user()->role === 'agent') {
            $query->where('assigne_a', auth()->id());
        }

        $total = (clone $query)->count();
        $parStatut = (clone $query)
            ->selectRaw("statut, count(*) as total")
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $parType = TypeReclamation::withCount(['reclamations' => function ($q) use ($dateDebut, $dateFin) {
            $q->whereBetween('date_creation', [$dateDebut, $dateFin . ' 23:59:59']);
        }])->whereHas('reclamations')->get()->pluck('reclamations_count', 'libelle');

        $parPriorite = (clone $query)
            ->selectRaw("priorite, count(*) as total")
            ->groupBy('priorite')
            ->pluck('total', 'priorite');

        $parAgent = (clone $query)
            ->whereNotNull('assigne_a')
            ->with('assigne')
            ->get()
            ->groupBy(fn ($r) => $r->assigne?->name ?? __('Non assigné'))
            ->map(fn ($items) => $items->count());

        $pdf = Pdf::loadView('reports.pdf.statistiques', compact(
            'total', 'parStatut', 'parType', 'parPriorite', 'parAgent', 'dateDebut', 'dateFin'
        ));

        return $pdf->download('statistiques-' . now()->format('Y-m-d') . '.pdf');
    }

    public function reclamations(Request $request)
    {
        $query = Reclamation::with(['client', 'type', 'sousType', 'assigne']);

        if (auth()->user()->role === 'agent') {
            $query->where('assigne_a', auth()->id());
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('sujet', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($cq) use ($search) {
                      $cq->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        if ($typeId = $request->input('type_id')) {
            $query->where('type_id', $typeId);
        }
        if ($statut = $request->input('statut')) {
            $query->where('statut', $statut);
        }
        if ($priorite = $request->input('priorite')) {
            $query->where('priorite', $priorite);
        }
        if ($assigneA = $request->input('assigne_a')) {
            $query->where('assigne_a', $assigneA);
        }
        if ($dateDebut = $request->input('date_debut')) {
            $query->whereDate('date_creation', '>=', $dateDebut);
        }
        if ($dateFin = $request->input('date_fin')) {
            $query->whereDate('date_creation', '<=', $dateFin);
        }

        if ($request->input('format') === 'csv') {
            $reclamations = $query->latest('date_creation')->get();
            return $this->exportCsv($reclamations);
        }

        $reclamations = $query->latest('date_creation')->paginate(15)->withQueryString();

        $types = TypeReclamation::where('actif', true)->get();
        $users = User::where('role', '!=', 'client')->where('is_active', true)->get();

        return view('reports.reclamations', compact('reclamations', 'types', 'users'));
    }

    public function reclamation(Reclamation $reclamation)
    {
        if (auth()->user()->role === 'agent' && $reclamation->assigne_a !== auth()->id()) {
            abort(403);
        }

        $reclamation->load(['client', 'type', 'sousType', 'assigne', 'historiqueStatuts.utilisateur', 'messages.expediteur', 'piecesJointes']);

        return view('reports.reclamation', compact('reclamation'));
    }

    public function statistiques(Request $request)
    {
        $dateDebut = $request->input('date_debut', now()->startOfMonth()->format('Y-m-d'));
        $dateFin = $request->input('date_fin', now()->format('Y-m-d'));

        $query = Reclamation::whereBetween('date_creation', [$dateDebut, $dateFin . ' 23:59:59']);

        if (auth()->user()->role === 'agent') {
            $query->where('assigne_a', auth()->id());
        }

        $total = (clone $query)->count();
        $parStatut = (clone $query)
            ->selectRaw("statut, count(*) as total")
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $parType = TypeReclamation::withCount(['reclamations' => function ($q) use ($dateDebut, $dateFin) {
            $q->whereBetween('date_creation', [$dateDebut, $dateFin . ' 23:59:59']);
        }])->whereHas('reclamations')->get()->pluck('reclamations_count', 'libelle');

        $parPriorite = (clone $query)
            ->selectRaw("priorite, count(*) as total")
            ->groupBy('priorite')
            ->pluck('total', 'priorite');

        $parAgent = (clone $query)
            ->whereNotNull('assigne_a')
            ->with('assigne')
            ->get()
            ->groupBy(fn ($r) => $r->assigne?->name ?? __('Non assigné'))
            ->map(fn ($items) => $items->count());

        return view('reports.statistiques', compact(
            'total', 'parStatut', 'parType', 'parPriorite', 'parAgent', 'dateDebut', 'dateFin'
        ));
    }

    private function exportCsv($reclamations)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="reclamations.csv"',
        ];

        $callback = function () use ($reclamations) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [__('Référence'), __('Date'), __('Client'), __('Type'), __('Sous-type'), __('Sujet'), __('Priorité'), __('Statut'), __('Assigné à')]);

            foreach ($reclamations as $r) {
                fputcsv($handle, [
                    $r->reference,
                    $r->date_creation->format('d/m/Y H:i'),
                    $r->client?->full_name,
                    $r->type?->libelle,
                    $r->sousType?->libelle,
                    $r->sujet,
                    $r->priorite,
                    $r->statut,
                    $r->assigne?->name,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
