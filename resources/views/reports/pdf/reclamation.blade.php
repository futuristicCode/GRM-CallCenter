@extends('reports.pdf.layout')

@section('title', __('Réclamation') . ' ' . $reclamation->reference)

@section('content')
<table class="info-grid">
    <tr>
        <td>
            <div class="label">{{ __('Référence') }}</div>
            <div class="value" style="font-family: monospace;">{{ $reclamation->reference }}</div>
        </td>
        <td>
            <div class="label">{{ __('Statut') }}</div>
            <div class="value">
                <span class="badge badge-{{ $reclamation->statut }}">{{ $reclamation->statut_label }}</span>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="label">{{ __('Date de création') }}</div>
            <div class="value">{{ $reclamation->date_creation->format('d/m/Y H:i') }}</div>
        </td>
        <td>
            <div class="label">{{ __('Date de clôture') }}</div>
            <div class="value">{{ $reclamation->date_cloture?->format('d/m/Y H:i') ?? '—' }}</div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="label">{{ __('Priorité') }}</div>
            <div class="value">
                <span class="badge badge-{{ $reclamation->priorite }}">{{ __(ucfirst($reclamation->priorite)) }}</span>
            </div>
        </td>
        <td>
            <div class="label">{{ __('Référence externe') }}</div>
            <div class="value">{{ $reclamation->reference_externe ?? '—' }}</div>
        </td>
    </tr>
</table>

<h2 class="section-title">{{ __('Client') }}</h2>
<table class="info-grid">
    <tr>
        <td><div class="label">{{ __('Nom') }}</div><div class="value">{{ $reclamation->client?->full_name }}</div></td>
        <td><div class="label">{{ __('Email') }}</div><div class="value">{{ $reclamation->client?->email }}</div></td>
    </tr>
    <tr>
        <td><div class="label">{{ __('Téléphone') }}</div><div class="value">{{ $reclamation->client?->telephone ?? '—' }}</div></td>
        <td><div class="label">{{ __('Adresse') }}</div><div class="value">{{ $reclamation->client?->adresse ?? '—' }}</div></td>
    </tr>
</table>

<h2 class="section-title">{{ __('Détails') }}</h2>
<table class="info-grid">
    <tr>
        <td><div class="label">{{ __('Type') }}</div><div class="value">{{ $reclamation->type?->libelle }}</div></td>
        <td><div class="label">{{ __('Sous-type') }}</div><div class="value">{{ $reclamation->sousType?->libelle ?? '—' }}</div></td>
    </tr>
    <tr>
        <td colspan="2"><div class="label">{{ __('Sujet') }}</div><div class="value">{{ $reclamation->sujet }}</div></td>
    </tr>
</table>

<div class="card">
    <h3>{{ __('Description') }}</h3>
    <div class="card-body" style="padding: 12px;">
        <p style="font-size: 9pt; line-height: 1.6;">{{ $reclamation->description }}</p>
    </div>
</div>

@if($reclamation->assigne)
<h2 class="section-title">{{ __('Assignation') }}</h2>
<table class="info-grid">
    <tr>
        <td><div class="label">{{ __('Assigné à') }}</div><div class="value">{{ $reclamation->assigne->name }}</div></td>
        <td><div class="label">{{ __('Email') }}</div><div class="value">{{ $reclamation->assigne->email }}</div></td>
    </tr>
</table>
@endif

@if($reclamation->motif_rejet)
<h2 class="section-title">{{ __('Motif de rejet') }}</h2>
<div class="card">
    <div class="card-body" style="padding: 12px;">
        <p style="font-size: 9pt; color: #991b1b;">{{ $reclamation->motif_rejet }}</p>
    </div>
</div>
@endif

@if($reclamation->historiqueStatuts->isNotEmpty())
<h2 class="section-title">{{ __('Historique des statuts') }}</h2>
<table>
    <thead>
        <tr>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Ancien statut') }}</th>
            <th>{{ __('Nouveau statut') }}</th>
            <th>{{ __('Par') }}</th>
            <th>{{ __('Commentaire') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reclamation->historiqueStatuts as $h)
        <tr>
            <td style="white-space: nowrap;">{{ $h->date_changement->format('d/m/Y H:i') }}</td>
            <td>{{ $h->ancien_statut ? __(ucfirst(str_replace('_', ' ', $h->ancien_statut))) : '—' }}</td>
            <td><span class="badge badge-{{ $h->nouveau_statut }}">{{ __(ucfirst(str_replace('_', ' ', $h->nouveau_statut))) }}</span></td>
            <td>{{ $h->utilisateur?->name ?? '—' }}</td>
            <td>{{ $h->commentaire ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection
