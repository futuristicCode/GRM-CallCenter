@extends('layouts.app')

@section('title', 'Journal d\'audit')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Journal d'audit</h1>
        <p class="text-sm text-gray-500 mt-1">Historique complet des actions du système</p>
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.audit-logs.index') }}">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label class="label">Action</label>
                        <select name="action" class="select w-full">
                            <option value="">Toutes</option>
                            @foreach($actions as $action)
                                <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Modèle</label>
                        <select name="modele" class="select w-full">
                            <option value="">Tous</option>
                            @foreach($modeles as $modele)
                                <option value="{{ $modele }}" {{ request('modele') == $modele ? 'selected' : '' }}>{{ $modele }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Date début</label>
                        <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="input w-full">
                    </div>
                    <div>
                        <label class="label">Date fin</label>
                        <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="input w-full">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary btn-sm flex-1">Filtrer</button>
                        <a href="{{ route('admin.audit-logs.index') }}" class="btn-secondary btn-sm">Réinitialiser</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900">Entrées</h3>
                <span class="badge-blue ml-auto">{{ $logs->total() }} entrée(s)</span>
            </div>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th class="w-8"></th>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Action</th>
                        <th>Modèle</th>
                        <th>ID</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $aColors = [
                            'creation' => 'badge-green',
                            'changement_statut' => 'badge-blue',
                            'assignation' => 'badge-purple',
                            'modification' => 'badge-orange',
                            'suppression' => 'badge-red',
                        ];
                    @endphp
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition-colors" x-data="{ open: false }">
                            <td>
                                @if($log->ancien_valeurs || $log->nouveau_valeurs)
                                    <button @click="open = !open" class="text-gray-400 hover:text-indigo-600 transition-colors p-1 rounded hover:bg-indigo-50">
                                        <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-90': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                @endif
                            </td>
                            <td class="whitespace-nowrap text-sm text-gray-600">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="font-medium text-gray-900">{{ $log->utilisateur->name ?? 'Système' }}</td>
                            <td><span class="{{ $aColors[$log->action] ?? 'badge-gray' }}">{{ $log->action }}</span></td>
                            <td class="text-sm text-gray-500">{{ $log->modele }}</td>
                            <td class="text-sm text-gray-500 font-mono">{{ $log->modele_id ?? '-' }}</td>
                            <td class="text-sm text-gray-400 font-mono">{{ $log->adresse_ip ?? '-' }}</td>
                        </tr>
                        @if($log->ancien_valeurs || $log->nouveau_valeurs)
                            <tr x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-cloak>
                                <td colspan="7" class="bg-gray-50 px-6 py-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @if($log->ancien_valeurs && count($log->ancien_valeurs) > 0)
                                            <div>
                                                <h4 class="text-xs font-semibold text-red-600 uppercase mb-2 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                    Avant
                                                </h4>
                                                <pre class="bg-red-50 border border-red-200 rounded-xl p-3 text-xs text-red-800 overflow-auto max-h-48 font-mono">{{ json_encode($log->ancien_valeurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        @endif
                                        @if($log->nouveau_valeurs && count($log->nouveau_valeurs) > 0)
                                            <div>
                                                <h4 class="text-xs font-semibold text-emerald-600 uppercase mb-2 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H4"/></svg>
                                                    Après
                                                </h4>
                                                <pre class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-xs text-emerald-800 overflow-auto max-h-48 font-mono">{{ json_encode($log->nouveau_valeurs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-gray-400 py-8">Aucune entrée d'audit.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="card-header border-t border-gray-100">
                {{ $logs->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
