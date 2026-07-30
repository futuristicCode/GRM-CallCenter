@extends('layouts.app')

@section('title', __('Modifier l\'utilisateur'))

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Modifier l\'utilisateur') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $user->name }} — {{ $user->email }}</p>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-xs text-gray-400">{{ __(ucfirst($user->role)) }} · {{ $user->is_active ? __('Actif') : __('Inactif') }}</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="label">{{ __('Nom complet') }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="input @error('name') !border-red-400 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="label">{{ __('Adresse email') }}</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="input @error('email') !border-red-400 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="phone" class="label">{{ __('Téléphone') }}</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="input" placeholder="{{ __('+243 ...') }}">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="role" class="label">{{ __('Rôle') }}</label>
                        <select name="role" id="role" required class="select @error('role') !border-red-400 @enderror">
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                            <option value="gestionnaire" {{ old('role', $user->role) === 'gestionnaire' ? 'selected' : '' }}>{{ __('Gestionnaire') }}</option>
                            <option value="agent" {{ old('role', $user->role) === 'agent' ? 'selected' : '' }}>{{ __('Agent') }}</option>
                            <option value="client" {{ old('role', $user->role) === 'client' ? 'selected' : '' }}>{{ __('Client') }}</option>
                        </select>
                        @error('role')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <label for="is_active" class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <div class="relative">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:bg-emerald-500 transition-colors"></div>
                                <div class="absolute left-[2px] top-[2px] w-5 h-5 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ __('Compte actif') }}</span>
                            <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}">{{ $user->is_active ? __('Actif') : __('Inactif') }}</span>
                        </label>
                        <p class="text-xs text-gray-400 mt-1.5 ml-14">{{ __('Désactivez pour empêcher la connexion de cet utilisateur') }}</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">{{ __('Annuler') }}</a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        {{ __('Mettre à jour') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
