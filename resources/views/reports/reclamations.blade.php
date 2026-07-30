@extends('layouts.app')

@section('title', __('Rapport – Liste des réclamations'))

@section('content')
<div class="print:hidden">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500">{{ __(':count réclamation(s) trouvée(s)', ['count' => $reclamations->total()]) }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.pdf.reclamations', request()->query()) }}" class="btn-primary">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                </svg>
                {{ __('PDF') }}
            </a>
            <a href="{{ route('reports.index') }}" class="btn-secondary">{{ __('Retour') }}</a>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.reclamations') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="label">{{ __('Recherche') }}</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Réf., nom, email...') }}" class="input">
                </div>
                <div>
                    <label class="label">{{ __('Type') }}</label>
                    <select name="type_id" class="select">
                        <option value="">{{ __('Tous') }}</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>{{ $type->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">{{ __('Statut') }}</label>
                    <select name="statut" class="select">
                        <option value="">{{ __('Tous') }}</option>
                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>{{ __('En attente') }}</option>
                        <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>{{ __('En cours') }}</option>
                        <option value="resolu" {{ request('statut') == 'resolu' ? 'selected' : '' }}>{{ __('Résolu') }}</option>
                        <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>{{ __('Rejeté') }}</option>
                        <option value="attente_client" {{ request('statut') == 'attente_client' ? 'selected' : '' }}>{{ __('Attente client') }}</option>
                        <option value="archive" {{ request('statut') == 'archive' ? 'selected' : '' }}>{{ __('Archivé') }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">{{ __('Priorité') }}</label>
                    <select name="priorite" class="select">
                        <option value="">{{ __('Toutes') }}</option>
                        <option value="haute" {{ request('priorite') == 'haute' ? 'selected' : '' }}>{{ __('Haute') }}</option>
                        <option value="moyenne" {{ request('priorite') == 'moyenne' ? 'selected' : '' }}>{{ __('Moyenne') }}</option>
                        <option value="basse" {{ request('priorite') == 'basse' ? 'selected' : '' }}>{{ __('Basse') }}</option>
                    </select>
                </div>
                <div>
                    <label class="label">{{ __('Assigné à') }}</label>
                    <select name="assigne_a" class="select">
                        <option value="">{{ __('Tous') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('assigne_a') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">{{ __('Date début') }}</label>
                    <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="input">
                </div>
                <div>
                    <label class="label">{{ __('Date fin') }}</label>
                    <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="input">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary">{{ __('Filtrer') }}</button>
                    <a href="{{ route('reports.reclamations') }}" class="btn-secondary">{{ __('Réinitialiser') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card print:border-0 print:shadow-none">
    <div class="card-body p-0 print:p-0">
        <div class="print-only mb-6 text-center" style="display:none">
            <h2 class="text-xl font-bold text-gray-900">{{ __('Rapport – Liste des réclamations') }}</h2>
            <p class="text-sm text-gray-500">{{ __('Généré le') }} {{ now()->format('d/m/Y') }} {{ __('à') }} {{ now()->format('H:i') }}</p>
            @if(request('date_debut') || request('date_fin'))
            <p class="text-sm text-gray-500">
                {{ __('Période : du') }} {{ request('date_debut', '...') }} {{ __('au') }} {{ request('date_fin', '...') }}
            </p>
            @endif
            <hr class="my-4">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 print:bg-gray-100">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">{{ __('Réf.') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">{{ __('Date') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">{{ __('Client') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">{{ __('Type') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">{{ __('Sujet') }}</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">{{ __('Priorité') }}</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">{{ __('Statut') }}</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">{{ __('Assigné à') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($reclamations as $r)
                    <tr class="hover:bg-gray-50 print:hover:bg-transparent">
                        <td class="px-4 py-3 font-mono text-xs text-gray-900">{{ $r->reference }}</td>
                        <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $r->date_creation->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-900">{{ $r->client?->full_name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $r->type?->libelle }}</td>
                        <td class="px-4 py-3 text-gray-900 max-w-xs truncate">{{ $r->sujet }}</td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $pClass = match($r->priorite) {
                                    'haute' => 'badge-danger',
                                    'moyenne' => 'badge-warning',
                                    'basse' => 'badge-success',
                                    default => 'badge-default',
                                };
                                $pLabel = match($r->priorite) {
                                    'haute' => __('Haute'),
                                    'moyenne' => __('Moyenne'),
                                    'basse' => __('Basse'),
                                    default => $r->priorite,
                                };
                            @endphp
                            <span class="badge {{ $pClass }}">{{ $pLabel }}</span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @php
                                $sClass = match($r->statut) {
                                    'en_attente' => 'badge-warning',
                                    'en_cours' => 'badge-info',
                                    'resolu' => 'badge-success',
                                    'rejete' => 'badge-danger',
                                    'attente_client' => 'badge-default',
                                    'archive' => 'badge-neutral',
                                    default => 'badge-default',
                                };
                                $sLabel = match($r->statut) {
                                    'en_attente' => __('En attente'),
                                    'en_cours' => __('En cours'),
                                    'resolu' => __('Résolu'),
                                    'rejete' => __('Rejeté'),
                                    'attente_client' => __('Attente client'),
                                    'archive' => __('Archivé'),
                                    default => $r->statut,
                                };
                            @endphp
                            <span class="badge {{ $sClass }}">{{ $sLabel }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $r->assigne?->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-400">{{ __('Aucune réclamation trouvée.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($reclamations->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 print:hidden">
            {{ $reclamations->links() }}
        </div>
    @endif
</div>

<div class="print-only mt-8 text-center text-xs text-gray-400">
    <p>{{ __('GRM – Gestion des Réclamations') }} | {{ __('Rapport généré le') }} {{ now()->format('d/m/Y') }} {{ __('à') }} {{ now()->format('H:i') }}</p>
</div>
@endsection

@push('scripts')
<style>
    @media print {
        body { font-size: 11px; }
        .print-only { display: block !important; }
        table { page-break-inside: auto; }
        tr { page-break-inside: avoid; }
        thead { display: table-header-group; }
    }
    .print-only { display: none; }
</style>
@endpush
