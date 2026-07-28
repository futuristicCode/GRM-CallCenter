@extends('layouts.guest')

@section('title', 'Confirmer votre mot de passe')
@section('subtitle', 'Veuillez confirmer votre mot de passe pour continuer')

@section('content')
<div class="mb-4 flex items-center gap-3 px-4 py-3 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl text-sm">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
    <span>Ceci est une zone sécurisée. Veuillez confirmer votre mot de passe.</span>
</div>

<form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
    @csrf

    <div>
        <label for="password" class="label">Mot de passe</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="input pl-11" placeholder="••••••••">
        </div>
        @error('password')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit" class="btn-primary w-full py-3 text-sm">
        Confirmer
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
    </button>
</form>
@endsection
