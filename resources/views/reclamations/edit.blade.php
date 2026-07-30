@extends('layouts.app')

@section('title', __('Modifier') . ' ' . $reclamation->reference)

@section('content')
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('reclamations.show', $reclamation) }}" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" {{ app()->getLocale() === 'ar' ? 'style="transform:scaleX(-1)"' : '' }}><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L7.5 12l7.5-7.5"/></svg>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ __('Modifier') }} {{ $reclamation->reference }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ __('Mettez à jour les informations de la réclamation') }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('reclamations.update', $reclamation) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                <h3>{{ __('Informations client') }}</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="label">{{ __('Nom') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom', $reclamation->client->nom) }}" required class="input">
                    </div>
                    <div>
                        <label class="label">{{ __('Prénom') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="prenom" value="{{ old('prenom', $reclamation->client->prenom) }}" required class="input">
                    </div>
                    <div>
                        <label class="label">{{ __('Email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $reclamation->client->email) }}" class="input">
                    </div>
                    <div>
                        <label class="label">{{ __('Téléphone') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="telephone" value="{{ old('telephone', $reclamation->client->telephone) }}" required class="input">
                    </div>
                    <div class="md:col-span-2">
                        <label class="label">{{ __('Adresse') }}</label>
                        <input type="text" name="adresse" value="{{ old('adresse', $reclamation->client->adresse) }}" class="input">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                <h3>{{ __('Détails de la réclamation') }}</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="label">{{ __('Type') }} <span class="text-red-500">*</span></label>
                        <select name="type_id" id="type_id" required class="select">
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('type_id', $reclamation->type_id) == $type->id ? 'selected' : '' }}>{{ $type->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">{{ __('Sous-type') }} <span class="text-red-500">*</span></label>
                        <select name="sous_type_id" id="sous_type_id" required class="select">
                            <option value="">{{ __('Sélectionner un sous-type') }}</option>
                            @foreach($sousTypes as $st)
                                <option value="{{ $st->id }}" {{ old('sous_type_id', $reclamation->sous_type_id) == $st->id ? 'selected' : '' }}>{{ $st->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">{{ __('Référence externe') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="reference_externe" value="{{ old('reference_externe', $reclamation->reference_externe) }}" required class="input">
                    </div>
                    <div>
                        <label class="label">{{ __('Priorité') }} <span class="text-red-500">*</span></label>
                        <div class="flex flex-col sm:flex-row gap-2 mt-2">
                            @foreach(['haute', 'moyenne', 'basse'] as $p)
                                <label class="flex-1">
                                    <input type="radio" name="priorite" value="{{ $p }}" {{ old('priorite', $reclamation->priorite) == $p ? 'checked' : '' }} class="peer sr-only">
                                    <div class="cursor-pointer text-center px-4 py-2 rounded-xl border-2 border-gray-200 text-sm font-medium text-gray-600 transition-all hover:border-gray-300
                                        @if($p === 'haute') peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700
                                        @elseif($p === 'moyenne') peer-checked:border-yellow-500 peer-checked:bg-yellow-50 peer-checked:text-yellow-700
                                        @else peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 @endif">
                                        {{ __(ucfirst($p)) }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="label">{{ __('Sujet') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="sujet" value="{{ old('sujet', $reclamation->sujet) }}" required class="input">
                    </div>
                    <div class="md:col-span-2">
                        <label class="label">{{ __('Description') }} <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="5" required class="input">{{ old('description', $reclamation->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit" class="btn-primary w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                {{ __('Mettre à jour') }}
            </button>
            <a href="{{ route('reclamations.show', $reclamation) }}" class="btn-secondary w-full sm:w-auto">{{ __('Annuler') }}</a>
        </div>
    </form>

    @push('scripts')
    <script>
        document.getElementById('type_id').addEventListener('change', function() {
            const typeId = this.value;
            const sousTypeSelect = document.getElementById('sous_type_id');
            sousTypeSelect.innerHTML = '<option value="">{{ __("Chargement...") }}</option>';
            if (!typeId) { sousTypeSelect.innerHTML = '<option value="">{{ __("Aucun") }}</option>'; return; }
            fetch('{{ route("api.sous-types") }}?type_id=' + typeId)
                .then(r => r.json())
                .then(data => {
                    let html = '<option value="">{{ __("Aucun") }}</option>';
                    data.forEach(st => { html += '<option value="' + st.id + '">' + st.libelle + '</option>'; });
                    sousTypeSelect.innerHTML = html;
                });
        });
    </script>
    @endpush
@endsection
