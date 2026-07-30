@extends('layouts.guest')

@section('title', __('Vérification de l\'email'))
@section('subtitle', __('Vérifiez votre boîte de réception pour confirmer votre compte'))

@section('content')
@if (session('status') == 'verification-link-sent')
    <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>{{ __('Un nouveau lien de vérification a été envoyé à votre adresse email.') }}</span>
    </div>
@endif

<p class="text-sm text-gray-600 mb-6">
    {{ __('Merci de vous être inscrit ! Avant de continuer, veuillez vérifier votre adresse email en cliquant sur le lien que nous venons de vous envoyer.') }}
</p>

<div class="space-y-3">
    <form method="POST" action="{{ route('verification.send') }}" class="space-y-0">
        @csrf
        <button type="submit" class="btn-primary w-full py-3 text-sm">
            {{ __('Renvoyer l\'email de vérification') }}
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/></svg>
        </button>
    </form>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full py-2.5 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors rounded-xl hover:bg-gray-100">
            {{ __('Déconnexion') }}
        </button>
    </form>
</div>
@endsection
