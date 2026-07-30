@extends('layouts.app')

@section('title', __('Rapports'))

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="card">
        <div class="card-body flex flex-col items-center text-center gap-4 py-8">
            <div class="w-14 h-14 rounded-2xl bg-indigo-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Liste des réclamations') }}</h3>
            <p class="text-sm text-gray-500">{{ __('Générer un rapport détaillé avec filtres par date, type, statut et priorité.') }}</p>
            <a href="{{ route('reports.reclamations') }}" class="btn-primary w-full justify-center mt-2">
                {{ __('Générer le rapport') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body flex flex-col items-center text-center gap-4 py-8">
            <div class="w-14 h-14 rounded-2xl bg-emerald-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Statistiques') }}</h3>
            <p class="text-sm text-gray-500">{{ __('Rapport statistique : répartition par statut, type, priorité et agent.') }}</p>
            <a href="{{ route('reports.statistiques') }}" class="btn-primary w-full justify-center mt-2">
                {{ __('Générer le rapport') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body flex flex-col items-center text-center gap-4 py-8">
            <div class="w-14 h-14 rounded-2xl bg-amber-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">{{ __('Export CSV') }}</h3>
            <p class="text-sm text-gray-500">{{ __('Exporter la liste des réclamations au format CSV pour analyse dans Excel.') }}</p>
            <a href="{{ route('reports.reclamations', ['format' => 'csv']) }}" class="btn-primary w-full justify-center mt-2">
                {{ __('Exporter en CSV') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body flex flex-col items-center text-center gap-4 py-8">
            <div class="w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">{{ __('PDF Réclamations') }}</h3>
            <p class="text-sm text-gray-500">{{ __('Télécharger la liste complète des réclamations en PDF.') }}</p>
            <a href="{{ route('reports.pdf.reclamations') }}" class="btn-primary w-full justify-center mt-2">
                {{ __('Télécharger le PDF') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body flex flex-col items-center text-center gap-4 py-8">
            <div class="w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">{{ __('PDF Statistiques') }}</h3>
            <p class="text-sm text-gray-500">{{ __('Télécharger le rapport statistique complet en PDF.') }}</p>
            <a href="{{ route('reports.pdf.statistiques') }}" class="btn-primary w-full justify-center mt-2">
                {{ __('Télécharger le PDF') }}
            </a>
        </div>
    </div>
</div>
@endsection
