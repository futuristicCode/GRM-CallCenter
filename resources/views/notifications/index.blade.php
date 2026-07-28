@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-sm text-gray-500 mt-1">Suivez les alertes et mises à jour</p>
        </div>
        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="btn-secondary btn-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Tout marquer comme lu
            </button>
        </form>
    </div>

    <div class="space-y-3">
        @forelse($notifications as $notification)
            <div class="card {{ $notification->lu ? '' : 'border-l-4 border-l-indigo-500 bg-indigo-50/30' }} transition-all hover:shadow-md">
                <div class="card-body py-4">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $notification->type_notification === 'email' ? 'bg-blue-100 text-blue-600' : 'bg-amber-100 text-amber-600' }}">
                                @if($notification->type_notification === 'email')
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-semibold text-gray-900 text-sm">{{ $notification->sujet ?? 'Notification' }}</h3>
                                @if(!$notification->lu)
                                    <span class="badge-blue text-[10px]">Nouveau</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $notification->contenu }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            @if(!$notification->lu)
                                <form method="POST" action="{{ route('notifications.mark-read', $notification) }}">
                                    @csrf
                                    <button type="submit" class="btn-sm bg-indigo-50 text-indigo-600 hover:bg-indigo-100 border-0" title="Marquer comme lu">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <h3 class="text-gray-500 font-medium">Aucune notification</h3>
                    <p class="text-sm text-gray-400 mt-1">Vous êtes à jour !</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
