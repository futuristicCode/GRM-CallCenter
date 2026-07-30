@extends('reports.pdf.layout')

@section('title', __('Liste des réclamations'))
@section('period')
    @if(request('date_debut') || request('date_fin'))
        {{ __('du') }} {{ request('date_debut', '...') }} {{ __('au') }} {{ request('date_fin', '...') }}
    @else
        {{ __('Toutes les dates') }}
    @endif
@endsection

@section('content')
<table>
    <thead>
        <tr>
            <th>{{ __('Réf.') }}</th>
            <th>{{ __('Date') }}</th>
            <th>{{ __('Client') }}</th>
            <th>{{ __('Type') }}</th>
            <th>{{ __('Sujet') }}</th>
            <th>{{ __('Priorité') }}</th>
            <th>{{ __('Statut') }}</th>
            <th>{{ __('Assigné à') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reclamations as $r)
        <tr>
            <td style="font-family: monospace; font-size: 8pt;">{{ $r->reference }}</td>
            <td style="white-space: nowrap;">{{ $r->date_creation->format('d/m/Y') }}</td>
            <td>{{ $r->client?->full_name }}</td>
            <td>{{ $r->type?->libelle }}</td>
            <td>{{ $r->sujet }}</td>
            <td>
                <span class="badge badge-{{ $r->priorite }}">
                    {{ __(ucfirst($r->priorite)) }}
                </span>
            </td>
            <td>
                <span class="badge badge-{{ $r->statut }}">
                    {{ $r->statut_label }}
                </span>
            </td>
            <td>{{ $r->assigne?->name ?? '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align: center; color: #9ca3af; padding: 30px;">{{ __('Aucune réclamation trouvée.') }}</td></tr>
        @endforelse
    </tbody>
</table>

<p style="text-align: right; font-size: 8pt; color: #6b7280;">
    {{ __('Total') }} : {{ $reclamations->count() }} {{ __('réclamation(s)') }}
</p>
@endsection
