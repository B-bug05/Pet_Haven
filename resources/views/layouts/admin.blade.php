<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Portal - PetHaven</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> [x-cloak] { display: none !important; } </style>
</head>
<body class="font-sans antialiased bg-slate-50 flex h-screen overflow-hidden" x-data="{ showModal: false, modalType: '', modalData: null }">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-slate-900 shadow-xl flex flex-col justify-between hidden md:flex z-20">
        <div>
            <div class="h-16 flex items-center justify-center border-b border-slate-800">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-black text-white tracking-tight">👑 PetHaven Admin</a>
            </div>
            <nav class="p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white transition' }}">
                    📊 System Overview
                </a>
                <a href="{{ route('admin.users') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.users') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white transition' }}">
                    👥 Manage Users
                </a>
                <a href="{{ route('admin.logs') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('admin.logs') ? 'bg-indigo-600 text-white font-bold' : 'text-slate-400 hover:bg-slate-800 hover:text-white transition' }}">
                    📋 Audit Logs
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-800" x-data="{ openUserMenu: false }" @click.outside="openUserMenu = false">

            {{-- Clickable User Row --}}
            <div @click="openUserMenu = !openUserMenu"
                class="flex items-center gap-3 px-2 py-2 rounded-xl cursor-pointer hover:bg-slate-800 transition select-none">
                <div class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold shrink-0">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-indigo-300 uppercase tracking-wider font-black">{{ Auth::user()->role }}</p>
                </div>
                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0"
                    :class="{ 'rotate-180': openUserMenu }"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>

            {{-- Dropdown Menu --}}
            <div x-show="openUserMenu"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                x-cloak
                class="mt-2 bg-slate-800 border border-slate-700 rounded-xl overflow-hidden">

                <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm text-slate-300 hover:bg-slate-700 hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="font-medium">Profile Settings</span>
                </a>

                <div class="border-t border-slate-700"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <div class="border-t border-slate-700"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-slate-700 hover:text-red-300 transition font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Secure Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-end px-8 z-10">
            <span class="text-sm font-bold text-slate-400 uppercase tracking-widest">System Administration</span>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto p-8">
            @yield('content')
        </main>
    </div>
</body>
</html>