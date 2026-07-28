<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use App\Models\TypeReclamation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $isAgent = $user->role === 'agent';
        $scope = fn ($q) => $isAgent ? $q->where('assigne_a', $user->id) : $q;

        $totalReclamations = $scope(Reclamation::query())->count();
        $enAttente = $scope(Reclamation::where('statut', 'en_attente'))->count();
        $enCours = $scope(Reclamation::where('statut', 'en_cours'))->count();
        $resolu = $scope(Reclamation::where('statut', 'resolu'))->count();
        $rejete = $scope(Reclamation::where('statut', 'rejete'))->count();
        $attenteClient = $scope(Reclamation::where('statut', 'attente_client'))->count();

        $totalHier = $scope(Reclamation::whereDate('created_at', now()->subDay()))->count();
        $evolutionTotal = $totalHier > 0 ? round((($totalReclamations - $totalHier) / $totalHier) * 100, 1) : 0;

        $parType = TypeReclamation::withCount('reclamations')
            ->whereHas('reclamations', $scope)
            ->get()
            ->pluck('reclamations_count', 'libelle');

        $evolution30Jours = $scope(
            Reclamation::selectRaw('date(created_at) as date, count(*) as total')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
        )->get();

        $dernieresReclamations = $scope(
            Reclamation::with(['client', 'type', 'assigne'])
                ->latest('date_creation')
        )->limit(5)->get();

        return view('dashboard', compact(
            'totalReclamations', 'enAttente', 'enCours', 'resolu', 'rejete', 'attenteClient',
            'evolutionTotal', 'parType', 'evolution30Jours', 'dernieresReclamations'
        ));
    }
}
