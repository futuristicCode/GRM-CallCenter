<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'GRM') }} – @yield('title', __('Connexion'))</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    @if(app()->getLocale() === 'ar')
        <link href="https://fonts.bunny.net/css?family=tajawal:300,400,500,700,800&display=swap" rel="stylesheet" />
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    @php($rtl = app()->getLocale() === 'ar')
    <div class="min-h-full flex">
        {{-- Left panel: branding --}}
        <div class="hidden lg:flex lg:flex-1 relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-indigo-800">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDM0djItSDI0di0yaDEyem0wLTR2Mkg0VjMwaDMyem0wLTR2Mkg0di0yaDMyek00IDE4djJIMHYtMmg0ek00IDh2Mkg0VjZoNHoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-30"></div>

            <div class="relative z-10 flex flex-col justify-center px-12 xl:px-20 w-full">
                <div class="mb-12">
                    <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-6 shadow-lg shadow-indigo-900/20">
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h1 class="text-4xl font-extrabold text-white leading-tight tracking-tight">GRM</h1>
                    <p class="text-lg text-indigo-100 mt-2 font-medium">{{ __('Gestion des Réclamations') }}<br>{{ __('et des Mécontentements') }}</p>
                </div>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-sm">{{ __('Traitement rapide') }}</h3>
                            <p class="text-indigo-200 text-sm mt-0.5">{{ __('Suivez et résolvez les réclamations en temps réel') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-sm">{{ __('Tableau de bord') }}</h3>
                            <p class="text-indigo-200 text-sm mt-0.5">{{ __('Indicateurs et graphiques pour piloter la performance') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-sm">{{ __('Sécurisé') }}</h3>
                            <p class="text-indigo-200 text-sm mt-0.5">{{ __('Gestion des rôles et traçabilité complète') }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-auto pt-16">
                    <p class="text-indigo-300 text-xs">&copy; {{ date('Y') }} GRM &mdash; {{ __('Tous droits réservés') }}</p>
                </div>
            </div>
        </div>

        {{-- Right panel: form --}}
        <div class="flex-1 flex flex-col justify-center items-center px-6 py-12 bg-gray-50 lg:bg-white">
            {{-- Mobile logo --}}
            <div class="lg:hidden mb-8 text-center">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mx-auto shadow-lg shadow-indigo-200">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-gray-900 mt-3">GRM</h1>
            </div>

            <div class="w-full max-w-sm">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">@yield('title', __('Connexion'))</h2>
                    <p class="text-sm text-gray-500 mt-1.5">@yield('subtitle', __('Connectez-vous à votre espace de travail'))</p>
                </div>

                {{-- Flash messages --}}
                @if(session('status'))
                    <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm">
                        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <ul class="list-disc list-inside flex-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="label">{{ __('Adresse email') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 {{ $rtl ? 'right-0 pr-3.5' : 'left-0 pl-3.5' }} flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                                   class="input {{ $rtl ? 'pr-11' : 'pl-11' }}" placeholder="{{ __('vous@exemple.com') }}">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="label">{{ __('Mot de passe') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 {{ $rtl ? 'right-0 pr-3.5' : 'left-0 pl-3.5' }} flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            </div>
                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                   class="input {{ $rtl ? 'pr-11' : 'pl-11' }}" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                            <input id="remember_me" type="checkbox" name="remember"
                                   class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 transition">
                            <span class="text-sm text-gray-500">{{ __('Se souvenir de moi') }}</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors">
                                {{ __('Mot de passe oublié ?') }}
                            </a>
                        @endif
                    </div>

                    <button type="submit" class="btn-primary w-full py-3 text-sm">
                        {{ __('Se connecter') }}
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" {{ $rtl ? 'style="transform:scaleX(-1)"' : '' }}><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-gray-100">
                    <p class="text-xs text-gray-400 text-center">
                        {{ __('Comptes de démonstration') }}<br>
                        <span class="font-mono text-gray-500">admin@grm.com</span> · <span class="font-mono text-gray-500">password</span>
                    </p>
                </div>
            </div>

            {{-- Language switcher --}}
            <div class="mt-6 flex justify-center gap-2">
                <form method="POST" action="{{ route('locale.switch') }}">
                    @csrf
                    <input type="hidden" name="locale" value="fr">
                    <button type="submit" class="px-3 py-1.5 text-xs rounded-lg {{ app()->getLocale() === 'fr' ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100' }}">🇫🇷 FR</button>
                </form>
                <form method="POST" action="{{ route('locale.switch') }}">
                    @csrf
                    <input type="hidden" name="locale" value="en">
                    <button type="submit" class="px-3 py-1.5 text-xs rounded-lg {{ app()->getLocale() === 'en' ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100' }}">🇬🇧 EN</button>
                </form>
                <form method="POST" action="{{ route('locale.switch') }}">
                    @csrf
                    <input type="hidden" name="locale" value="ar">
                    <button type="submit" class="px-3 py-1.5 text-xs rounded-lg {{ app()->getLocale() === 'ar' ? 'bg-indigo-100 text-indigo-700 font-semibold' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100' }}">🇸🇦 AR</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
