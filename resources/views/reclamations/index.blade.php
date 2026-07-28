@extends('layouts.app')

@section('title', 'Réclamations')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500 mt-1">{{ $reclamations->total() }} réclamation(s) au total</p>
        </div>
        @if(in_array(auth()->user()->role, ['admin', 'gestionnaire']))
        <a href="{{ route('reclamations.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Nouvelle réclamation
        </a>
        @endif
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('reclamations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="label">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Réf., nom, email..." class="input">
                </div>
                <div>
                    <label class="label">Type</label>
                    <select name="type_id" class="select">
                        <option value="">Tous les types</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>{{ $type->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Statut</label>
                    <select name="statut" class="select">
                        <option value="">Tous</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                        <option value="attente_client" {{ request('statut') == 'attente_client' ? 'selected' : '' }}>Attente client</option>
                        <option value="resolu" {{ request('statut') == 'resolu' ? 'selected' : '' }}>Résolu</option>
                        <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                        <option value="archive" {{ request('statut') == 'archive' ? 'selected' : '' }}>Archivé</option>
                    </select>
                </div>
                <div>
                    <label class="label">Priorité</label>
                    <select name="priorite" class="select">
                        <option value="">Toutes</option>
                        <option value="haute" {{ request('priorite') == 'haute' ? 'selected' : '' }}>Haute</option>
                        <option value="moyenne" {{ request('priorite') == 'moyenne' ? 'selected' : '' }}>Moyenne</option>
                        <option value="basse" {{ request('priorite') == 'basse' ? 'selected' : '' }}>Basse</option>
                    </select>
                </div>
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        Filtrer
                    </button>
                    <a href="{{ route('reclamations.index') }}" class="btn-secondary btn-sm">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    @php
        $sColors = ['en_attente'=>'badge-yellow','en_cours'=>'badge-blue','attente_client'=>'badge-orange','resolu'=>'badge-green','rejete'=>'badge-red','archive'=>'badge-gray'];
        $sLabels = ['en_attente'=>'En attente','en_cours'=>'En cours','attente_client'=>'Attente client','resolu'=>'Résolu','rejete'=>'Rejeté','archive'=>'Archivé'];
        $pColors = ['haute'=>'badge-red','moyenne'=>'badge-yellow','basse'=>'badge-green'];
    @endphp

    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Sujet</th>
                        <th>Priorité</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reclamations as $r)
                        <tr>
                            <td>
                                <a href="{{ route('reclamations.show', $r) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                    {{ $r->reference }}
                                </a>
                            </td>
                            <td>{{ $r->client->prenom ?? '' }} {{ $r->client->nom ?? '' }}</td>
                            <td class="text-gray-500">{{ $r->type->libelle ?? '' }}</td>
                            <td class="max-w-xs truncate text-gray-700">{{ $r->sujet }}</td>
                            <td><span class="{{ $pColors[$r->priorite] ?? 'badge-gray' }}">{{ ucfirst($r->priorite) }}</span></td>
                            <td><span class="{{ $sColors[$r->statut] ?? 'badge-gray' }}">{{ $sLabels[$r->statut] ?? $r->statut }}</span></td>
                            <td class="text-gray-500">{{ ($r->date_creation ?? $r->created_at)->format('d/m/Y') }}</td>
                            <td class="text-right">
                                <a href="{{ route('reclamations.show', $r) }}" class="btn-secondary btn-sm">Voir</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                Aucune réclamation trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($reclamations->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $reclamations->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
