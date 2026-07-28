@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Mon profil</h1>
        <p class="text-sm text-gray-500 mt-1">Gérez vos informations personnelles et votre sécurité</p>
    </div>

    <div class="space-y-6">
        {{-- Profile Information --}}
        <div class="card">
            <div class="card-header">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Informations personnelles</h3>
                </div>
            </div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Update Password --}}
        <div class="card">
            <div class="card-header">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Mot de passe</h3>
                </div>
            </div>
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        @if(Auth::user()->role === 'admin')
        {{-- Delete Account --}}
        <div class="card border-red-100">
            <div class="card-header bg-red-50/50">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    </div>
                    <h3 class="font-semibold text-red-900">Zone dangereuse</h3>
                </div>
            </div>
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
