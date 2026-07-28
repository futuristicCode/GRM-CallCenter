<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeReclamation;
use App\Models\SousType;
use Illuminate\Http\Request;

class TypeReclamationController extends Controller
{
    public function index()
    {
        $types = TypeReclamation::with('sousTypes')->latest()->get();
        return view('admin.types.index', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:types_reclamation,code',
            'libelle' => 'required|string|max:255',
            'delai_traitement_sla' => 'required|integer|min:1',
        ]);

        TypeReclamation::create($validated);

        return redirect()->route('admin.types.index')
            ->with('success', 'Type de réclamation créé avec succès.');
    }

    public function update(Request $request, TypeReclamation $type)
    {
        $validated = $request->validate([
            'libelle' => 'required|string|max:255',
            'delai_traitement_sla' => 'required|integer|min:1',
            'actif' => 'nullable|boolean',
        ]);

        $type->update($validated);

        return redirect()->route('admin.types.index')
            ->with('success', 'Type mis à jour avec succès.');
    }

    public function destroy(TypeReclamation $type)
    {
        $type->update(['actif' => false]);
        return redirect()->route('admin.types.index')
            ->with('success', 'Type désactivé avec succès.');
    }

    public function storeSousType(Request $request)
    {
        $validated = $request->validate([
            'type_id' => 'required|exists:types_reclamation,id',
            'libelle' => 'required|string|max:255',
        ]);

        SousType::create($validated);

        return redirect()->route('admin.types.index')
            ->with('success', 'Sous-type créé avec succès.');
    }

    public function destroySousType(SousType $sousType)
    {
        $sousType->update(['actif' => false]);
        return redirect()->route('admin.types.index')
            ->with('success', 'Sous-type désactivé avec succès.');
    }
}
