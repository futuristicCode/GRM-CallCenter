@extends('reports.pdf.layout')

@section('title', __('Rapport statistique'))
@section('period', __('du') . " {$dateDebut} " . __('au') . " {$dateFin}")

@section('content')
<table class="stat-grid">
    <tr>
        <td style="border-color: #2563eb;">
            <span class="num" style="color: #2563eb;">{{ $total }}</span>
            <span class="lbl">{{ __('Total') }}</span>
        </td>
        <td style="border-color: #f59e0b;">
            <span class="num" style="color: #f59e0b;">{{ $parStatut['en_attente'] ?? 0 }}</span>
            <span class="lbl">{{ __('En attente') }}</span>
        </td>
        <td style="border-color: #3b82f6;">
            <span class="num" style="color: #3b82f6;">{{ $parStatut['en_cours'] ?? 0 }}</span>
            <span class="lbl">{{ __('En cours') }}</span>
        </td>
        <td style="border-color: #10b981;">
            <span class="num" style="color: #10b981;">{{ $parStatut['resolu'] ?? 0 }}</span>
            <span class="lbl">{{ __('Résolues') }}</span>
        </td>
    </tr>
</table>

<div class="card">
    <h3>{{ __('Répartition par statut') }}</h3>
    <div class="card-body" style="padding: 12px;">
        @php
            $statuts = ['en_attente' => __('En attente'), 'en_cours' => __('En cours'), 'resolu' => __('Résolu'), 'rejete' => __('Rejeté'), 'attente_client' => __('Attente client'), 'archive' => __('Archivé')];
            $colors = ['en_attente' => 'bar-amber', 'en_cours' => 'bar-blue', 'resolu' => 'bar-emerald', 'rejete' => 'bar-red', 'attente_client' => '', 'archive' => ''];
        @endphp
        @foreach($statuts as $key => $label)
            @if(($parStatut[$key] ?? 0) > 0)
                @php $pct = $total > 0 ? round(($parStatut[$key] / $total) * 100, 1) : 0; @endphp
                <div style="margin-bottom: 8px;">
                    <div style="display: flex; justify-content: space-between; font-size: 8pt; margin-bottom: 2px;">
                        <span>{{ $label }}</span>
                        <span>{{ $parStatut[$key] }} ({{ $pct }}%)</span>
                    </div>
                    <div class="bar-container">
                        <div class="bar {{ $colors[$key] ?? 'bar-blue' }}" style="width: {{ $pct }}%;"></div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>

<div class="card">
    <h3>{{ __('Répartition par type') }}</h3>
    <div class="card-body" style="padding: 0;">
        <table>
            <thead>
                <tr><th>{{ __('Type') }}</th><th>{{ __('Nombre') }}</th><th>{{ __('%') }}</th></tr>
            </thead>
            <tbody>
                @forelse($parType as $libelle => $count)
                @php $pct = $total > 0 ? round(($count / $total) * 100, 1) : 0; @endphp
                <tr>
                    <td>{{ $libelle }}</td>
                    <td>{{ $count }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="bar-container" style="flex: 1; height: 10px;">
                                <div class="bar bar-blue" style="width: {{ $pct }}%; height: 10px;"></div>
                            </div>
                            <span>{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align: center; color: #9ca3af;">{{ __('Aucune donnée') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h3>{{ __('Répartition par priorité') }}</h3>
    <div class="card-body" style="padding: 0;">
        <table>
            <thead>
                <tr><th>{{ __('Priorité') }}</th><th>{{ __('Nombre') }}</th><th>{{ __('%') }}</th></tr>
            </thead>
            <tbody>
                @php $pLabels = ['haute' => __('Haute'), 'moyenne' => __('Moyenne'), 'basse' => __('Basse')]; @endphp
                @foreach($pLabels as $key => $label)
                    @if(($parPriorite[$key] ?? 0) > 0)
                    @php $pct = $total > 0 ? round(($parPriorite[$key] / $total) * 100, 1) : 0; @endphp
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $parPriorite[$key] }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div class="bar-container" style="flex: 1; height: 10px;">
                                    <div class="bar bar-amber" style="width: {{ $pct }}%; height: 10px;"></div>
                                </div>
                                <span>{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <h3>{{ __('Répartition par agent') }}</h3>
    <div class="card-body" style="padding: 0;">
        <table>
            <thead>
                <tr><th>{{ __('Agent') }}</th><th>{{ __('Nombre') }}</th><th>{{ __('%') }}</th></tr>
            </thead>
            <tbody>
                @forelse($parAgent as $name => $count)
                @php $pct = $total > 0 ? round(($count / $total) * 100, 1) : 0; @endphp
                <tr>
                    <td>{{ $name }}</td>
                    <td>{{ $count }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="bar-container" style="flex: 1; height: 10px;">
                                <div class="bar bar-emerald" style="width: {{ $pct }}%; height: 10px;"></div>
                            </div>
                            <span>{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="text-align: center; color: #9ca3af;">{{ __('Aucune donnée') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
