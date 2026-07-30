<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use App\Models\TypeReclamation;
use App\Models\SousType;
use App\Models\PieceJointe;
use App\Models\User;
use App\Services\ReclamationService;
use Illuminate\Http\Request;

class ReclamationController extends Controller
{
    public function __construct(private ReclamationService $service)
    {
    }

    public function index(Request $request)
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

        $reclamations = $query->latest('date_creation')->paginate($request->input('per_page', 15));
        $types = TypeReclamation::where('actif', true)->get();
        $users = User::where('role', '!=', 'client')->where('is_active', true)->get();

        return view('reclamations.index', compact('reclamations', 'types', 'users'));
    }

    public function create()
    {
        if (!in_array(auth()->user()->role, ['admin', 'gestionnaire'])) {
            abort(403, __('Seuls les admins et gestionnaires peuvent créer des réclamations.'));
        }
        $types = TypeReclamation::where('actif', true)->get();
        return view('reclamations.create', compact('types'));
    }

    public function store(Request $request)
    {
        if (!in_array(auth()->user()->role, ['admin', 'gestionnaire'])) {
            abort(403, __('Seuls les admins et gestionnaires peuvent créer des réclamations.'));
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'required|string|max:50',
            'adresse' => 'nullable|string|max:500',
            'type_client' => 'nullable|string|in:particulier,entreprise',
            'type_id' => 'required|exists:types_reclamation,id',
            'sous_type_id' => 'required|exists:sous_types,id',
            'sujet' => 'required|string|max:255',
            'description' => 'required|string',
            'priorite' => 'required|string|in:haute,moyenne,basse',
            'reference_externe' => 'required|string|max:100',
            'pieces.*' => 'nullable|file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        $reclamation = $this->service->creerReclamation($validated, $request->user()->id);

        if ($request->hasFile('pieces')) {
            foreach ($request->file('pieces') as $file) {
                $path = $file->store('pieces-jointes', 'public');
                PieceJointe::create([
                    'reclamation_id' => $reclamation->id,
                    'nom_fichier' => $file->getClientOriginalName(),
                    'chemin_stockage' => $path,
                    'taille_octets' => $file->getSize(),
                    'type_mime' => $file->getMimeType(),
                ]);
            }
        }

        return redirect()->route('reclamations.show', $reclamation)
            ->with('success', __('Réclamation créée avec succès.') . ' ' . __('Référence') . ': ' . $reclamation->reference);
    }

    public function show(Reclamation $reclamation)
    {
        if (auth()->user()->role === 'agent' && $reclamation->assigne_a !== auth()->id()) {
            abort(403, __('Accès non autorisé.'));
        }

        $reclamation->load(['client', 'type', 'sousType', 'assigne', 'historiqueStatuts.utilisateur', 'messages.expediteur', 'piecesJointes']);
        $transitions = $this->service->getTransitionsValides($reclamation->statut);
        $agents = User::where('role', 'agent')->where('is_active', true)->get();

        return view('reclamations.show', compact('reclamation', 'transitions', 'agents'));
    }

    public function edit(Reclamation $reclamation)
    {
        if (!in_array(auth()->user()->role, ['admin', 'gestionnaire'])) {
            abort(403, __('Seuls les admins et gestionnaires peuvent modifier les réclamations.'));
        }

        $reclamation->load(['client', 'type', 'sousType']);
        $types = TypeReclamation::where('actif', true)->get();
        $sousTypes = SousType::where('type_id', $reclamation->type_id)->where('actif', true)->get();

        return view('reclamations.edit', compact('reclamation', 'types', 'sousTypes'));
    }

    public function update(Request $request, Reclamation $reclamation)
    {
        if (!in_array(auth()->user()->role, ['admin', 'gestionnaire'])) {
            abort(403, __('Seuls les admins et gestionnaires peuvent modifier les réclamations.'));
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telephone' => 'required|string|max:50',
            'adresse' => 'nullable|string|max:500',
            'type_id' => 'required|exists:types_reclamation,id',
            'sous_type_id' => 'required|exists:sous_types,id',
            'sujet' => 'required|string|max:255',
            'description' => 'required|string',
            'priorite' => 'required|string|in:haute,moyenne,basse',
            'reference_externe' => 'required|string|max:100',
        ]);

        $reclamation->update([
            'type_id' => $validated['type_id'],
            'sous_type_id' => $validated['sous_type_id'],
            'sujet' => $validated['sujet'],
            'description' => $validated['description'],
            'priorite' => $validated['priorite'],
            'reference_externe' => $validated['reference_externe'],
            'date_derniere_modification' => now(),
        ]);

        $reclamation->client->update([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?? null,
            'adresse' => $validated['adresse'] ?? null,
        ]);

        return redirect()->route('reclamations.show', $reclamation)
            ->with('success', __('Réclamation mise à jour avec succès.'));
    }

    public function destroy(Reclamation $reclamation)
    {
        if (!in_array(auth()->user()->role, ['admin', 'gestionnaire'])) {
            abort(403, __('Seuls les admins et gestionnaires peuvent supprimer des réclamations.'));
        }

        $reclamation->delete();
        return redirect()->route('reclamations.index')
            ->with('success', __('Réclamation supprimée avec succès.'));
    }

    public function prendreEnCharge(Reclamation $reclamation, Request $request)
    {
        $this->service->assigner($reclamation, $request->user()->id, $request->user()->id);
        $this->service->changerStatut($reclamation, 'en_cours', $request->user()->id, __('Prise en charge'));

        return redirect()->route('reclamations.show', $reclamation)
            ->with('success', __('Réclamation prise en charge.'));
    }

    public function changerStatut(Request $request, Reclamation $reclamation)
    {
        $validated = $request->validate([
            'statut' => 'required|string|in:en_cours,resolu,rejete,attente_client,archive',
            'commentaire' => 'nullable|string',
            'motif_rejet' => 'nullable|string',
        ]);

        if ($validated['statut'] === 'rejete' && !empty($validated['motif_rejet'])) {
            $reclamation->update(['motif_rejet' => $validated['motif_rejet']]);
        }

        $success = $this->service->changerStatut(
            $reclamation,
            $validated['statut'],
            $request->user()->id,
            $validated['commentaire'] ?? null
        );

        if (!$success) {
            return back()->withErrors(['statut' => __('Transition de statut non autorisée.')]);
        }

        return redirect()->route('reclamations.show', $reclamation)
            ->with('success', __('Statut mis à jour avec succès.'));
    }

    public function assigner(Request $request, Reclamation $reclamation)
    {
        if (!in_array($request->user()->role, ['admin', 'gestionnaire'])) {
            abort(403, __('Seuls les admins et gestionnaires peuvent assigner des réclamations.'));
        }

        $validated = $request->validate([
            'assigne_a' => 'required|exists:users,id',
        ]);

        $this->service->assigner($reclamation, $request->user()->id, $validated['assigne_a']);

        return redirect()->route('reclamations.show', $reclamation)
            ->with('success', __('Réclamation réassignée avec succès.'));
    }

    public function sousTypes(Request $request)
    {
        $sousTypes = SousType::where('type_id', $request->input('type_id'))
            ->where('actif', true)
            ->get(['id', 'libelle']);

        return response()->json($sousTypes);
    }
}
