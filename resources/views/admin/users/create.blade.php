@extends('layouts.app')

@section('title', __('Ajouter un utilisateur'))

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Ajouter un utilisateur') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('Créez un nouveau compte utilisateur') }}</p>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900">{{ __('Informations personnelles') }}</h3>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="label">{{ __('Nom complet') }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="input @error('name') !border-red-400 @enderror" placeholder="{{ __('Jean Dupont') }}">
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="label">{{ __('Adresse email') }}</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required class="input @error('email') !border-red-400 @enderror" placeholder="{{ __('jean@exemple.com') }}">
                            @error('email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="label">{{ __('Téléphone') }}</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="input" placeholder="{{ __('+243 ...') }}">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" class="label">{{ __('Rôle') }}</label>
                        <select name="role" id="role" required class="select @error('role') !border-red-400 @enderror">
                            <option value="">{{ __('Sélectionner un rôle') }}</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                            <option value="gestionnaire" {{ old('role') === 'gestionnaire' ? 'selected' : '' }}>{{ __('Gestionnaire') }}</option>
                            <option value="agent" {{ old('role') === 'agent' ? 'selected' : '' }}>{{ __('Agent') }}</option>
                            <option value="client" {{ old('role') === 'client' ? 'selected' : '' }}>{{ __('Client') }}</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">{{ __('Sécurité') }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="label">{{ __('Mot de passe') }}</label>
                                <input type="password" name="password" id="password" required class="input @error('password') !border-red-400 @enderror" placeholder="{{ __('••••••••') }}">
                                @error('password')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="label">{{ __('Confirmer le mot de passe') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required class="input" placeholder="{{ __('••••••••') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">{{ __('Annuler') }}</a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        {{ __('Créer') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
