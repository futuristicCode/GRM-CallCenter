@extends('layouts.app')

@section('title', __('Rapport – Statistiques'))

@section('content')
<div class="print:hidden">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500">{{ __('Période : du') }} {{ $dateDebut }} {{ __('au') }} {{ $dateFin }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.pdf.statistiques', request()->query()) }}" class="btn-primary">
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
            <form method="GET" action="{{ route('reports.statistiques') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="label">{{ __('Date début') }}</label>
                    <input type="date" name="date_debut" value="{{ $dateDebut }}" class="input">
                </div>
                <div>
                    <label class="label">{{ __('Date fin') }}</label>
                    <input type="date" name="date_fin" value="{{ $dateFin }}" class="input">
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="btn-primary">{{ __('Actualiser') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="print-only mb-6 text-center" style="display:none">
    <h2 class="text-xl font-bold text-gray-900">{{ __('Rapport statistique') }}</h2>
    <p class="text-sm text-gray-500">{{ __('Période : du') }} {{ $dateDebut }} {{ __('au') }} {{ $dateFin }}</p>
    <hr class="my-4">
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="card">
        <div class="card-body text-center py-6">
            <p class="text-3xl font-bold text-gray-900">{{ $total }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ __('Total réclamations') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center py-6">
            <p class="text-3xl font-bold text-amber-600">{{ $parStatut['en_attente'] ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ __('En attente') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center py-6">
            <p class="text-3xl font-bold text-blue-600">{{ $parStatut['en_cours'] ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ __('En cours') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center py-6">
            <p class="text-3xl font-bold text-emerald-600">{{ $parStatut['resolu'] ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">{{ __('Résolues') }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Par statut') }}</h3>
        </div>
        <div class="card-body p-0">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase">{{ __('Statut') }}</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase">{{ __('Nombre') }}</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase">{{ __('%') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $labels = ['en_attente' => __('En attente'), 'en_cours' => __('En cours'), 'resolu' => __('Résolu'), 'rejete' => __('Rejeté'), 'attente_client' => __('Attente client'), 'archive' => __('Archivé')];
                    @endphp
                    @foreach($labels as $key => $label)
                    @if(($parStatut[$key] ?? 0) > 0)
                    <tr>
                        <td class="px-4 py-3 text-gray-900">{{ $label }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ $parStatut[$key] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $total > 0 ? round(($parStatut[$key] / $total) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Par type') }}</h3>
        </div>
        <div class="card-body p-0">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase">{{ __('Type') }}</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase">{{ __('Nombre') }}</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase">{{ __('%') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($parType as $libelle => $count)
                    <tr>
                        <td class="px-4 py-3 text-gray-900">{{ $libelle }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ $count }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $total > 0 ? round(($count / $total) * 100, 1) : 0 }}%</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">{{ __('Aucune donnée') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Par priorité') }}</h3>
        </div>
        <div class="card-body p-0">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase">{{ __('Priorité') }}</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase">{{ __('Nombre') }}</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase">{{ __('%') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $pLabels = ['haute' => __('Haute'), 'moyenne' => __('Moyenne'), 'basse' => __('Basse')];
                    @endphp
                    @foreach($pLabels as $key => $label)
                    @if(($parPriorite[$key] ?? 0) > 0)
                    <tr>
                        <td class="px-4 py-3 text-gray-900">{{ $label }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ $parPriorite[$key] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $total > 0 ? round(($parPriorite[$key] / $total) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Par agent') }}</h3>
        </div>
        <div class="card-body p-0">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase">{{ __('Agent') }}</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase">{{ __('Nombre') }}</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase">{{ __('%') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($parAgent as $name => $count)
                    <tr>
                        <td class="px-4 py-3 text-gray-900">{{ $name }}</td>
                        <td class="px-4 py-3 text-right font-medium">{{ $count }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $total > 0 ? round(($count / $total) * 100, 1) : 0 }}%</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-gray-400">{{ __('Aucune donnée') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
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
        .grid { display: block; }
        .md\:grid-cols-4 > *, .md\:grid-cols-2 > * { margin-bottom: 1rem; page-break-inside: avoid; }
    }
    .print-only { display: none; }
</style>
@endpush
