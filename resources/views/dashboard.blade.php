@extends('layouts.app')

@section('title', __('Tableau de bord'))

@section('content')
@php
    $sColors = [
        'en_attente'    => 'badge-yellow',
        'en_cours'      => 'badge-blue',
        'attente_client'=> 'badge-orange',
        'resolu'        => 'badge-green',
        'rejete'        => 'badge-red',
        'archive'       => 'badge-gray',
    ];
    $sLabels = [
        'en_attente'     => __('En attente'),
        'en_cours'       => __('En cours'),
        'attente_client' => __('Attente client'),
        'resolu'         => __('Résolu'),
        'rejete'         => __('Rejeté'),
        'archive'        => __('Archivé'),
    ];
@endphp

{{-- ── KPI Row ─────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">

    {{-- Total --}}
    <div class="kpi-card card relative overflow-hidden">
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-200/50">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total') }}</p>
                <p class="text-2xl font-extrabold text-gray-900 leading-none mt-1">{{ $totalReclamations }}</p>
                @if($evolutionTotal != 0)
                    <div class="flex items-center gap-1 mt-1.5">
                        @if($evolutionTotal > 0)
                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                        @else
                            <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 4.5l15 15m0 0V8.25m0 11.25H8.25"/></svg>
                        @endif
                        <span class="text-xs font-semibold {{ $evolutionTotal > 0 ? 'text-red-500' : 'text-emerald-500' }}">
                            {{ $evolutionTotal > 0 ? '+' : '' }}{{ $evolutionTotal }}%
                        </span>
                        <span class="text-xs text-gray-400">{{ __('vs hier') }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- En attente --}}
    <div class="kpi-card card relative overflow-hidden">
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('En attente') }}</p>
                <p class="text-2xl font-extrabold text-amber-600 leading-none mt-1">{{ $enAttente }}</p>
            </div>
        </div>
    </div>

    {{-- En cours --}}
    <div class="kpi-card card relative overflow-hidden">
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('En cours') }}</p>
                <p class="text-2xl font-extrabold text-blue-600 leading-none mt-1">{{ $enCours }}</p>
            </div>
        </div>
    </div>

    {{-- Résolu --}}
    <div class="kpi-card card relative overflow-hidden">
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Résolu') }}</p>
                <p class="text-2xl font-extrabold text-emerald-600 leading-none mt-1">{{ $resolu }}</p>
            </div>
        </div>
    </div>

    {{-- Rejeté --}}
    <div class="kpi-card card relative overflow-hidden">
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Rejeté') }}</p>
                <p class="text-2xl font-extrabold text-red-600 leading-none mt-1">{{ $rejete }}</p>
            </div>
        </div>
    </div>
</div>

{{-- ── Charts Row ─────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Répartition par type – Doughnut --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-bold text-gray-900">{{ __('Répartition par type') }}</h3>
        </div>
        <div class="card-body flex items-center justify-center min-h-[320px]">
            @if($parType->count())
                <canvas id="chartTypes" class="max-h-72"></canvas>
            @else
                <p class="text-gray-400 text-sm">{{ __('Aucune donnée disponible') }}</p>
            @endif
        </div>
    </div>

    {{-- Évolution 30 jours – Line --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-bold text-gray-900">{{ __('Évolution – 30 jours') }}</h3>
        </div>
        <div class="card-body min-h-[320px]">
            @if($evolution30Jours->count())
                <canvas id="chartEvolution" class="max-h-72"></canvas>
            @else
                <p class="text-gray-400 text-sm">{{ __('Aucune donnée disponible') }}</p>
            @endif
        </div>
    </div>
</div>

{{-- ── Bottom Section ────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Dernières réclamations (2 cols) --}}
    <div class="lg:col-span-2 card">
        <div class="card-header flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">{{ __('Dernières réclamations') }}</h3>
            <a href="{{ route('reclamations.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                {{ __('Voir tout') }} &rarr;
            </a>
        </div>
        <div class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Réf.') }}</th>
                            <th>{{ __('Client') }}</th>
                            <th>{{ __('Type') }}</th>
                            <th>{{ __('Statut') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dernieresReclamations as $r)
                            <tr>
                                <td>
                                    <a href="{{ route('reclamations.show', $r) }}" class="font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                                        {{ $r->reference }}
                                    </a>
                                </td>
                                <td>{{ $r->client->prenom ?? '' }} {{ $r->client->nom ?? '' }}</td>
                                <td>{{ $r->type->libelle ?? '' }}</td>
                                <td>
                                    <span class="{{ $sColors[$r->statut] ?? 'badge-gray' }}">
                                        {{ $sLabels[$r->statut] ?? $r->statut }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($r->date_creation ?? $r->created_at)->format('d/m/Y H:i') }}</td>
                                <td class="text-right">
                                    <a href="{{ route('reclamations.show', $r) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                                        {{ __('Voir') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-400 py-8">{{ __('Aucune réclamation enregistrée') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Alertes SLA (1 col) --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-bold text-gray-900">{{ __('Alertes SLA') }}</h3>
        </div>
        <div class="card-body">
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                    <svg class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500">{{ __('Aucune alerte SLA') }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ __('Toutes les réclamations sont dans les délais') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const Chart = window.Chart;

const typeLabels = @json($parType->keys());
const typeCounts = @json($parType->values());
const typeColors = ['#6366f1', '#8b5cf6', '#a78bfa', '#c4b5fd', '#e0e7ff'];

if (document.getElementById('chartTypes')) {
    new Chart(document.getElementById('chartTypes'), {
        type: 'doughnut',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeCounts,
                backgroundColor: typeColors,
                borderWidth: 0,
                hoverOffset: 8,
            }],
  rt(document.getElementById('chartTypes'), {
        type: 'doughnut',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeCounts,
                backgroundColor: typeColors,
                borderWidth: 0,
                hoverOffset: 8,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 12, family: 'Inter' },
                    },
                },
            },
        },
    });
}

const evoDates  = @json($evolution30Jours->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m')));
const evoTotals = @json($evolution30Jours->pluck('total'));

if (document.getElementById('chartEvolution')) {
    new Chart(document.getElementById('chartEvolution'), {
        type: 'line',
        data: {
            labels: evoDates,
            datasets: [{
                label: '{{ __('Réclamations') }}',
                data: evoTotals,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.08)',
                borderWidth: 2.5,
                pointRadius: 0,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: '#6366f1',
                tension: 0.35,
                fill: true,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, family: 'Inter' }, color: '#9ca3af', maxRotation: 0 },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: { font: { size: 11, family: 'Inter' }, color: '#9ca3af', stepSize: 1 },
                },
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e1b4b',
                    titleFont: { size: 12, family: 'Inter' },
                    bodyFont: { size: 12, family: 'Inter' },
                    padding: 10,
                    cornerRadius: 8,
                },
            },
        },
    });
}
</script>
@endpush
