@extends('layouts.app')

@section('title', __('Nouvelle réclamation'))

@section('content')
    <form method="POST" action="{{ route('reclamations.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="card">
            <div class="card-header">
                <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                <h3>{{ __('Informations client') }}</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="label">{{ __('Nom') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">{{ __('Prénom') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="prenom" value="{{ old('prenom') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">{{ __('Email') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="input">
                    </div>
                    <div>
                        <label class="label">{{ __('Téléphone') }}</label>
                        <input type="text" name="telephone" value="{{ old('telephone') }}" class="input">
                    </div>
                    <div class="md:col-span-2">
                        <label class="label">{{ __('Adresse') }}</label>
                        <input type="text" name="adresse" value="{{ old('adresse') }}" class="input">
                    </div>
                    <div>
                        <label class="label">{{ __('Type client') }}</label>
                        <select name="type_client" class="select">
                            <option value="particulier" {{ old('type_client') == 'particulier' ? 'selected' : '' }}>{{ __('Particulier') }}</option>
                            <option value="entreprise" {{ old('type_client') == 'entreprise' ? 'selected' : '' }}>{{ __('Entreprise') }}</option>
                        </select>
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
                            <option value="">{{ __('Sélectionner un type') }}</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>{{ $type->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">{{ __('Sous-type') }}</label>
                        <select name="sous_type_id" id="sous_type_id" class="select">
                            <option value="">{{ __('Sélectionner un sous-type') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">{{ __('Référence externe') }}</label>
                        <input type="text" name="reference_externe" value="{{ old('reference_externe') }}" placeholder="{{ __('N° billet, code colis...') }}" class="input">
                    </div>
                    <div>
                        <label class="label">{{ __('Priorité') }} <span class="text-red-500">*</span></label>
                        <div class="flex flex-col sm:flex-row gap-2 mt-2">
                            <label class="flex-1">
                                <input type="radio" name="priorite" value="haute" {{ old('priorite') == 'haute' ? 'checked' : '' }} class="peer sr-only">
                                <div class="cursor-pointer text-center px-4 py-2 rounded-xl border-2 border-gray-200 text-sm font-medium text-gray-600 transition-all peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 hover:border-gray-300">
                                    {{ __('Haute') }}
                                </div>
                            </label>
                            <label class="flex-1">
                                <input type="radio" name="priorite" value="moyenne" {{ old('priorite', 'moyenne') == 'moyenne' ? 'checked' : '' }} class="peer sr-only">
                                <div class="cursor-pointer text-center px-4 py-2 rounded-xl border-2 border-gray-200 text-sm font-medium text-gray-600 transition-all peer-checked:border-yellow-500 peer-checked:bg-yellow-50 peer-checked:text-yellow-700 hover:border-gray-300">
                                    {{ __('Moyenne') }}
                                </div>
                            </label>
                            <label class="flex-1">
                                <input type="radio" name="priorite" value="basse" {{ old('priorite') == 'basse' ? 'checked' : '' }} class="peer sr-only">
                                <div class="cursor-pointer text-center px-4 py-2 rounded-xl border-2 border-gray-200 text-sm font-medium text-gray-600 transition-all peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 hover:border-gray-300">
                                    {{ __('Basse') }}
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="label">{{ __('Sujet') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="sujet" value="{{ old('sujet') }}" required class="input">
                    </div>
                    <div class="md:col-span-2">
                        <label class="label">{{ __('Description') }} <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="5" required class="input">{{ old('description') }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="label">{{ __('Pièces jointes (max 5, PDF/JPG/PNG)') }}</label>
                        <input type="file" name="pieces[]" multiple accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <button type="submit" class="btn-primary w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                {{ __('Soumettre') }}
            </button>
            <a href="{{ route('reclamations.index') }}" class="btn-secondary w-full sm:w-auto">{{ __('Annuler') }}</a>
        </div>
    </form>

    @push('scripts')
    <script>
        document.getElementById('type_id').addEventListener('change', function() {
            const typeId = this.value;
            const sousTypeSelect = document.getElementById('sous_type_id');
            sousTypeSelect.innerHTML = '<option value="">{{ __("Chargement...") }}</option>';

            if (!typeId) {
                sousTypeSelect.innerHTML = '<option value="">{{ __("Sélectionner un sous-type") }}</option>';
                return;
            }

            fetch('{{ route("api.sous-types") }}?type_id=' + typeId)
                .then(r => r.json())
                .then(data => {
                    let html = '<option value="">{{ __("Sélectionner un sous-type") }}</option>';
                    data.forEach(st => {
                        html += '<option value="' + st.id + '">' + st.libelle + '</option>';
                    });
                    sousTypeSelect.innerHTML = html;
                });
        });
    </script>
    @endpush
@endsection
