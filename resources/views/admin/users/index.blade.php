@extends('layouts.app')

@section('title', __('Utilisateurs'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ __('Gestion des utilisateurs') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $users->total() }} {{ __('utilisateur(s) au total') }}</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            {{ __('Ajouter') }}
        </a>
    </div>

    {{-- Filtres --}}
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}">
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <div class="sm:col-span-2">
                        <label class="label">{{ __('Rechercher') }}</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Nom ou email...') }}" class="input w-full">
                    </div>
                    <div>
                        <label class="label">{{ __('Rôle') }}</label>
                        <select name="role" class="select w-full">
                            <option value="">{{ __('Tous les rôles') }}</option>
                            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                            <option value="gestionnaire" {{ request('role') === 'gestionnaire' ? 'selected' : '' }}>{{ __('Gestionnaire') }}</option>
                            <option value="agent" {{ request('role') === 'agent' ? 'selected' : '' }}>{{ __('Agent') }}</option>
                            <option value="client" {{ request('role') === 'client' ? 'selected' : '' }}>{{ __('Client') }}</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="btn-primary btn-sm flex-1">{{ __('Filtrer') }}</button>
                        <a href="{{ route('admin.users.index') }}" class="btn-secondary btn-sm">{{ __('Réinitialiser') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('Utilisateur') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Rôle') }}</th>
                        <th>{{ __('Statut') }}</th>
                        <th>{{ __('Créé le') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $roleColors = [
                            'admin' => 'badge-purple',
                            'gestionnaire' => 'badge-blue',
                            'agent' => 'badge-green',
                            'client' => 'badge-gray',
                        ];
                    @endphp
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg {{ $user->is_active ? 'bg-gradient-to-br from-indigo-500 to-purple-600' : 'bg-gray-300' }} flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm">{{ $user->name }}</p>
                                        @if($user->phone)
                                            <p class="text-xs text-gray-400">{{ $user->phone }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-sm text-gray-600">{{ $user->email }}</td>
                            <td><span class="{{ $roleColors[$user->role] ?? 'badge-gray' }}">{{ __(ucfirst($user->role)) }}</span></td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge-green">{{ __('Actif') }}</span>
                                @else
                                    <span class="badge-red">{{ __('Inactif') }}</span>
                                @endif
                            </td>
                            <td class="text-sm text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Toggle Activer/Désactiver --}}
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit"
                                                class="btn-sm {{ $user->is_active ? 'bg-amber-50 text-amber-700 hover:bg-amber-100 border-0' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-0' }}"
                                                title="{{ $user->is_active ? __('Désactiver') : __('Activer') }}">
                                                @if($user->is_active)
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Modifier --}}
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-sm bg-indigo-50 text-indigo-600 hover:bg-indigo-100 border-0" title="{{ __('Modifier') }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                    </a>

                                    {{-- Réinit MDP --}}
                                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" onsubmit="return confirm('{{ __("Réinitialiser le mot de passe à \"password\" ?") }}')">
                                        @csrf
                                        <button type="submit" class="btn-sm bg-amber-50 text-amber-600 hover:bg-amber-100 border-0" title="{{ __('Réinitialiser MDP') }}">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
                                        </button>
                                    </form>

                                    {{-- Supprimer --}}
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('{{ __("Supprimer cet utilisateur ? Cette action est irréversible.") }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-sm btn-danger" title="{{ __('Supprimer') }}">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-400 py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                </div>
                                <p class="font-medium text-gray-500">{{ __('Aucun utilisateur trouvé') }}</p>
                                <p class="text-sm text-gray-400 mt-1">{{ __('Modifiez vos filtres ou créez un nouvel utilisateur') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="card-header border-t border-gray-100">
                {{ $users->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
