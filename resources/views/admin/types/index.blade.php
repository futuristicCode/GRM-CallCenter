@extends('layouts.app')

@section('title', 'Types de réclamation')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Types de réclamation</h1>
        <p class="text-sm text-gray-500 mt-1">Gérez les types et sous-types de réclamations</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Ajouter un type</h3>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.types.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                            <div>
                                <label class="label">Code</label>
                                <input type="text" name="code" required placeholder="Ex: BILL" class="input w-full">
                            </div>
                            <div>
                                <label class="label">Libellé</label>
                                <input type="text" name="libelle" required placeholder="Ex: Billeterie" class="input w-full">
                            </div>
                            <div>
                                <label class="label">SLA (heures)</label>
                                <input type="number" name="delai_traitement_sla" value="72" min="1" required class="input w-full">
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="btn-primary w-full">Créer</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Types existants</h3>
                        <span class="badge-blue ml-auto">{{ $types->count() }} type(s)</span>
                    </div>
                </div>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Libellé</th>
                                <th>SLA (h)</th>
                                <th>Statut</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($types as $type)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td><code class="font-mono text-sm font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $type->code }}</code></td>
                                    <td class="font-medium text-gray-900">{{ $type->libelle }}</td>
                                    <td class="text-gray-500">{{ $type->delai_traitement_sla }}h</td>
                                    <td>
                                        @if($type->actif)
                                            <span class="badge-green">Actif</span>
                                        @else
                                            <span class="badge-red">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.types.update', $type) }}">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="libelle" value="{{ $type->libelle }}">
                                                <input type="hidden" name="delai_traitement_sla" value="{{ $type->delai_traitement_sla }}">
                                                <input type="hidden" name="actif" value="{{ $type->actif ? 0 : 1 }}">
                                                <button type="submit" class="btn-sm {{ $type->actif ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} border-0">
                                                    {{ $type->actif ? 'Désactiver' : 'Activer' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.types.destroy', $type) }}" onsubmit="return confirm('Supprimer ce type ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-sm btn-danger">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-400 py-8">Aucun type trouvé.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </div>
                        <h3 class="font-semibold text-gray-900">Sous-types</h3>
                    </div>
                </div>
                <div class="card-body max-h-[600px] overflow-y-auto space-y-4">
                    @forelse($types as $type)
                        <div class="border border-gray-200 rounded-xl p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <code class="font-mono text-xs font-bold text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded">{{ $type->code }}</code>
                                <span class="text-sm font-medium text-gray-700">{{ $type->libelle }}</span>
                            </div>

                            <form method="POST" action="{{ route('admin.types.sous-types.store') }}" class="flex gap-2 mb-3">
                                @csrf
                                <input type="hidden" name="type_id" value="{{ $type->id }}">
                                <input type="text" name="libelle" required placeholder="Nouveau sous-type..." class="input flex-1 text-sm">
                                <button type="submit" class="btn-sm btn-success">+</button>
                            </form>

                            @if($type->sousTypes->count())
                                <div class="space-y-1">
                                    @foreach($type->sousTypes as $st)
                                        <div class="flex items-center justify-between py-1.5 px-2 rounded-lg hover:bg-gray-50">
                                            <span class="text-sm text-gray-700">{{ $st->libelle }}</span>
                                            @if($st->actif)
                                                <form method="POST" action="{{ route('admin.types.sous-types.destroy', $st) }}" onsubmit="return confirm('Supprimer ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="badge-gray text-[10px]">Inactif</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-400 italic">Aucun sous-type</p>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 text-sm">Aucun type trouvé.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
