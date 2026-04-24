<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Portal - PetHaven</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> [x-cloak] { display: none !important; } </style>
</head>
<body class="font-sans antialiased bg-stone-50 flex h-screen overflow-hidden" 
      x-data="{ showModal: false, modalType: '', modalData: null }">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-white shadow-xl flex flex-col justify-between hidden md:flex z-20 border-r border-stone-200">
        <div>
            <div class="h-16 flex items-center justify-center border-b border-stone-100">
                <a href="{{ route('staff.dashboard') }}" class="text-xl font-bold text-orange-600 tracking-tight">🐾 PetHaven Staff</a>
            </div>
            <nav class="p-4 space-y-2">
                <a href="{{ route('staff.dashboard') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('staff.dashboard') ? 'bg-orange-50 text-orange-700 font-bold' : 'text-stone-600 hover:bg-stone-50 hover:text-orange-600 transition' }}">
                    📊 Dashboard
                </a>
                <a href="{{ route('staff.pets.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('staff.pets.*') ? 'bg-orange-50 text-orange-700 font-bold' : 'text-stone-600 hover:bg-stone-50 hover:text-orange-600 transition' }}">
                    🐕 Manage Pets
                </a>
                <a href="{{ route('staff.applications.index') }}" class="block px-4 py-3 rounded-lg {{ request()->routeIs('staff.applications.*') ? 'bg-orange-50 text-orange-700 font-bold' : 'text-stone-600 hover:bg-stone-50 hover:text-orange-600 transition' }}">
                    📄 Applications
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-stone-100">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                    <p class="text-sm font-bold text-stone-800">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-stone-500 capitalize">{{ Auth::user()->role }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg text-sm font-bold transition">
                    🚪 Log Out
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-white border-b border-stone-200 flex items-center justify-end px-8 z-10">
            <button class="relative p-2 text-stone-400 hover:text-orange-600 transition">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                
                {{-- DYNAMIC RED DOT: Only shows if the staff has unread notifications! --}}
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                @endif
                
            </button>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-stone-50 p-8">
            @yield('content')
        </main>
    </div>

    {{-- IMPROVED GLOBAL MODAL --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm" @click="showModal = false"></div>
        
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[90vh]" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            {{-- Header --}}
            <div class="px-8 py-4 border-b border-stone-100 flex justify-between items-center bg-white shrink-0">
                <h2 class="text-xl font-bold text-stone-800" x-text="modalType === 'add' ? 'New Pet Entry' : (modalType === 'edit' ? 'Update Pet Profile' : 'Application Review')"></h2>
                <button @click="showModal = false" class="p-2 hover:bg-stone-100 rounded-full transition text-stone-400">&times;</button>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto p-0">
                <div x-show="modalType === 'add'">@include('staff.modals._add-pet')</div>
                <div x-show="modalType === 'edit'">@include('staff.modals._edit-pet')</div>
                <div x-show="modalType === 'application'">@include('staff.modals._review-app')</div>
            </div>
        </div>
    </div>
</body>
</html>