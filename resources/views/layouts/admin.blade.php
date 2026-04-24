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

        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="w-10 h-10 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <p class="text-sm font-bold text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-indigo-300 uppercase tracking-wider font-black">{{ Auth::user()->role }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-red-400 hover:bg-slate-800 hover:text-red-300 rounded-lg text-sm font-bold transition">
                    🚪 Secure Logout
                </button>
            </form>
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