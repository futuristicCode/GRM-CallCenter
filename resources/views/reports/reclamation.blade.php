@extends('layouts.app')

@section('title', __('Fiche réclamation') . ' – ' . $reclamation->reference)

@section('content')
<div class="print:hidden mb-6 flex items-center justify-between">
    <a href="{{ url()->previous() }}" class="btn-secondary">{{ __('Retour') }}</a>
    <a href="{{ route('reports.pdf.reclamation', $reclamation) }}" class="btn-primary">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
        </svg>
        {{ __('Télécharger le PDF') }}
    </a>
</div>

<div class="card print:border-0 print:shadow-none">
    <div class="card-body">
        <div class="text-center mb-6 print:block">
            <h2 class="text-xl font-bold text-gray-900">{{ __('Fiche de réclamation') }}</h2>
            <p class="text-sm text-gray-500">{{ __('Réf:') }} <strong>{{ $reclamation->reference }}</strong></p>
            <hr class="my-4">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">{{ __('Informations client') }}</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Nom') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $reclamation->client?->full_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Email') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $reclamation->client?->email }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Téléphone') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $reclamation->client?->telephone ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Type') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $reclamation->client?->type ? __(ucfirst($reclamation->client->type)) : '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">{{ __('Détails') }}</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Date de création') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $reclamation->date_creation->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Type') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $reclamation->type?->libelle }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Sous-type') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $reclamation->sousType?->libelle ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Priorité') }}</dt>
                        <dd class="text-sm font-medium">{{ __(ucfirst($reclamation->priorite)) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Statut') }}</dt>
                        <dd class="text-sm font-medium">{{ $reclamation->statut_label }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Assigné à') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $reclamation->assigne?->name ?? '—' }}</dd>
                    </div>
                    @if($reclamation->reference_externe)
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Réf. externe') }}</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $reclamation->reference_externe }}</dd>
                    </div>
                    @endif
                    @if($reclamation->motif_rejet)
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">{{ __('Motif de rejet') }}</dt>
                        <dd class="text-sm font-medium text-red-600">{{ $reclamation->motif_rejet }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="mt-6">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">{{ __('Sujet') }}</h3>
            <p class="text-sm text-gray-900 font-medium">{{ $reclamation->sujet }}</p>
        </div>

        <div class="mt-4">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">{{ __('Description') }}</h3>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $reclamation->description }}</p>
        </div>

        @if($reclamation->messages->isNotEmpty())
        <div class="mt-8">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">{{ __('Messages') }}</h3>
            <div class="space-y-3">
                @foreach($reclamation->messages as $message)
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs font-semibold text-gray-900">{{ $message->expediteur?->name }}</span>
                        <span class="text-xs text-gray-400">{{ $message->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="text-sm text-gray-700">{{ $message->contenu }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($reclamation->historiqueStatuts->isNotEmpty())
        <div class="mt-8">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">{{ __('Historique des statuts') }}</h3>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="text-left px-3 py-2 font-semibold text-gray-600 text-xs">{{ __('Date') }}</th>
                        <th class="text-left px-3 py-2 font-semibold text-gray-600 text-xs">{{ __('Ancien statut') }}</th>
                        <th class="text-left px-3 py-2 font-semibold text-gray-600 text-xs">{{ __('Nouveau statut') }}</th>
                        <th class="text-left px-3 py-2 font-semibold text-gray-600 text-xs">{{ __('Par') }}</th>
                        <th class="text-left px-3 py-2 font-semibold text-gray-600 text-xs">{{ __('Commentaire') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($reclamation->historiqueStatuts as $h)
                    <tr>
                        <td class="px-3 py-2 text-gray-600">{{ $h->date_changement->format('d/m/Y H:i') }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $h->ancien_statut ? __(ucfirst(str_replace('_', ' ', $h->ancien_statut))) : '—' }}</td>
                        <td class="px-3 py-2 font-medium">{{ __(ucfirst(str_replace('_', ' ', $h->nouveau_statut))) }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $h->utilisateur?->name }}</td>
                        <td class="px-3 py-2 text-gray-600">{{ $h->commentaire ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($reclamation->piecesJointes->isNotEmpty())
        <div class="mt-8">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">{{ __('Pièces jointes') }}</h3>
            <ul class="space-y-1">
                @foreach($reclamation->piecesJointes as $pj)
                <li class="text-sm text-gray-700">{{ $pj->nom_fichier }} ({{ number_format($pj->taille_octets / 1024, 1) }} Ko)</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

<div class="print-only mt-8 text-center text-xs text-gray-400">
    <p>{{ __('GRM – Gestion des Réclamations') }} | {{ $reclamation->reference }} | {{ __('Imprimé le') }} {{ now()->format('d/m/Y') }} {{ __('à') }} {{ now()->format('H:i') }}</p>
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
