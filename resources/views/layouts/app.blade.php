<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GRM') }} – @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="min-h-full flex" x-data="{ sidebarOpen: true, mobileSidebar: false }">
        {{-- Desktop Sidebar --}}
        <aside class="hidden lg:flex lg:flex-col lg:w-72 lg:fixed lg:inset-y-0 bg-white border-r border-gray-200 z-30 transition-all duration-300"
               :class="sidebarOpen ? 'lg:w-72' : 'lg:w-20'">
            {{-- Logo --}}
            <div class="flex items-center h-16 px-5 border-b border-gray-100 flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900 tracking-tight" x-show="sidebarOpen" x-transition>GRM</span>
                </a>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 py-4 space-y-1">
                <a href="{{ route('dashboard') }}"
                   class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Tableau de bord</span>
                </a>

                <a href="{{ route('reclamations.index') }}"
                   class="sidebar-link {{ request()->routeIs('reclamations.*') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Réclamations</span>
                </a>

                @if(auth()->user()->role === 'admin')
                    <div class="pt-4 pb-2" x-show="sidebarOpen">
                        <p class="px-3 text-[10px] font-bold text-gray-300 uppercase tracking-widest">Administration</p>
                    </div>

                    <a href="{{ route('admin.users.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                        </svg>
                        <span x-show="sidebarOpen" x-transition>Utilisateurs</span>
                    </a>

                    <a href="{{ route('admin.types.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.types.*') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                        </svg>
                        <span x-show="sidebarOpen" x-transition>Types de réclamation</span>
                    </a>

                    <a href="{{ route('admin.audit-logs.index') }}"
                       class="sidebar-link {{ request()->routeIs('admin.audit-logs.*') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-show="sidebarOpen" x-transition>Journal d'audit</span>
                    </a>
                @endif

                <div class="pt-4 pb-2" x-show="sidebarOpen">
                    <p class="px-3 text-[10px] font-bold text-gray-300 uppercase tracking-widest">Autres</p>
                </div>

                <a href="{{ route('notifications.index') }}"
                   class="sidebar-link {{ request()->routeIs('notifications.*') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Notifications</span>
                </a>
            </nav>

            {{-- Sidebar toggle --}}
            <div class="border-t border-gray-100 px-3 py-3 flex-shrink-0">
                <button @click="sidebarOpen = !sidebarOpen" class="sidebar-link sidebar-link-default w-full">
                    <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5"/>
                    </svg>
                    <span x-show="sidebarOpen" x-transition>Réduire</span>
                </button>
            </div>
        </aside>

        {{-- Mobile sidebar overlay --}}
        <div x-show="mobileSidebar" x-transition.opacity class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden" @click="mobileSidebar = false" x-cloak></div>
        <aside x-show="mobileSidebar" x-transition:enter="transition-transform duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
               x-transition:leave="transition-transform duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
               class="fixed inset-y-0 left-0 w-72 bg-white border-r border-gray-200 z-50 lg:hidden flex flex-col" x-cloak>
            <div class="flex items-center h-16 px-5 border-b border-gray-100">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900">GRM</span>
                </a>
            </div>
            <nav class="flex-1 overflow-y-auto sidebar-scroll px-3 py-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                    Tableau de bord
                </a>
                <a href="{{ route('reclamations.index') }}" class="sidebar-link {{ request()->routeIs('reclamations.*') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    Réclamations
                </a>
                @if(auth()->user()->role === 'admin')
                    <div class="pt-4 pb-2"><p class="px-3 text-[10px] font-bold text-gray-300 uppercase tracking-widest">Administration</p></div>
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        Utilisateurs
                    </a>
                    <a href="{{ route('admin.types.index') }}" class="sidebar-link {{ request()->routeIs('admin.types.*') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                        Types de réclamation
                    </a>
                    <a href="{{ route('admin.audit-logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.audit-logs.*') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Journal d'audit
                    </a>
                @endif
                <div class="pt-4 pb-2"><p class="px-3 text-[10px] font-bold text-gray-300 uppercase tracking-widest">Autres</p></div>
                <a href="{{ route('notifications.index') }}" class="sidebar-link {{ request()->routeIs('notifications.*') ? 'sidebar-link-active' : 'sidebar-link-default' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                    Notifications
                </a>
            </nav>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 lg:pl-72 transition-all duration-300" :class="sidebarOpen ? '' : 'lg:pl-20'">
            {{-- Top bar --}}
            <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-xl border-b border-gray-100">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        {{-- Mobile menu button --}}
                        <button @click="mobileSidebar = true" class="lg:hidden p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                        </button>
                        <div>
                            @hasSection('title')
                                <h1 class="text-lg font-bold text-gray-900">@yield('title')</h1>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Notifications --}}
                        <a href="{{ route('notifications.index') }}" class="relative p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                            </svg>
                            <span id="notif-badge" class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 rounded-full ring-2 ring-white" style="display:none"></span>
                        </a>

                        {{-- User dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center gap-2.5 p-1.5 rounded-xl hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-semibold text-gray-900 leading-none">{{ auth()->user()->name }}</p>
                                    <p class="text-[11px] text-gray-400 mt-0.5">{{ ucfirst(auth()->user()->role) }}</p>
                                </div>
                                <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition x-cloak
                                 class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                    Mon profil
                                </a>
                                <div class="border-t border-gray-100 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                                            Se déconnecter
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="px-4 sm:px-6 lg:px-8 mt-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
                    <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="flex-1">{{ session('success') }}</span>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">&times;</button>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <ul class="list-disc list-inside flex-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Page content --}}
            <main class="p-4 sm:p-6 lg:p-8">
                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </main>
        </div>
    </div>

    @stack('scripts')

    <script>
        function fetchNotifCount() {
            fetch('{{ route("api.notifications.unread-count") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                window._notifCount = data.count;
                const badge = document.getElementById('notif-badge');
                if (badge) {
                    badge.textContent = data.count > 99 ? '99+' : (data.count || '');
                    badge.style.display = data.count > 0 ? '' : 'none';
                }
            })
            .catch(() => {});
        }
        document.addEventListener('DOMContentLoaded', fetchNotifCount);
        setInterval(fetchNotifCount, 60000);
    </script>
</body>
</html>
