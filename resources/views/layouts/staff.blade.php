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

        <div class="p-4 border-t border-stone-100" x-data="{ openUserMenu: false }" @click.outside="openUserMenu = false">
    
            {{-- Clickable User Row --}}
            <div @click="openUserMenu = !openUserMenu"
                class="flex items-center gap-3 px-2 py-2 rounded-xl cursor-pointer hover:bg-stone-50 transition select-none">
                <div class="w-10 h-10 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center font-bold shrink-0">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-stone-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-stone-500 capitalize">{{ Auth::user()->role }}</p>
                </div>
                <svg class="w-4 h-4 text-stone-400 transition-transform duration-200 shrink-0"
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
                class="mt-2 bg-white border border-stone-200 rounded-xl shadow-lg overflow-hidden">

                <a href="{{ route('profile.edit') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm text-stone-600 hover:bg-orange-50 hover:text-orange-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="font-medium">Profile Settings</span>
                </a>

                <div class="border-t border-stone-100"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-white border-b border-stone-200 flex items-center justify-end px-8 z-10">
            
            {{-- NOTIFICATION BELL --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                
                <button @click="open = !open"
                    class="relative p-2 text-stone-400 hover:text-orange-600 transition rounded-lg hover:bg-stone-100">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    @endif
                </button>

                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                     x-cloak
                     class="absolute right-0 top-12 w-80 bg-white rounded-2xl shadow-xl border border-stone-200 z-50 overflow-hidden">

                    <div class="flex items-center justify-between px-5 py-4 border-b border-stone-100 bg-stone-50">
                        <div>
                            <p class="font-bold text-stone-800 text-sm">Notifications</p>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <p class="text-xs text-orange-500 font-semibold mt-0.5">
                                    {{ auth()->user()->unreadNotifications->count() }} unread
                                </p>
                            @else
                                <p class="text-xs text-stone-400 mt-0.5">All caught up!</p>
                            @endif
                        </div>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <form method="POST" action="{{ route('staff.notifications.read-all') }}">
                                @csrf
                                <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold transition">
                                    Mark all read
                                </button>
                            </form>
                        @endif
                    </div>

                    <ul class="divide-y divide-stone-100 max-h-80 overflow-y-auto">
                        @forelse(auth()->user()->notifications->take(8) as $notification)
                            <li class="px-5 py-4 hover:bg-orange-50/40 transition flex items-start gap-3 {{ is_null($notification->read_at) ? 'bg-orange-50/60' : '' }}">
                                <div class="mt-1.5 shrink-0">
                                    @if(is_null($notification->read_at))
                                        <span class="w-2 h-2 bg-orange-500 rounded-full block"></span>
                                    @else
                                        <span class="w-2 h-2 bg-transparent rounded-full block"></span>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-stone-700 font-medium leading-snug">
                                        {{ $notification->data['message'] ?? 'New notification' }}
                                    </p>
                                    <p class="text-xs text-stone-400 mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </li>
                        @empty
                            <li class="px-5 py-8 text-center">
                                <p class="text-stone-400 text-sm">No notifications yet.</p>
                            </li>
                        @endforelse
                    </ul>

                    @if(auth()->user()->notifications->count() > 8)
                        <div class="px-5 py-3 border-t border-stone-100 bg-stone-50 text-center">
                            <a href="{{ route('staff.applications.index') }}"
                                class="text-xs font-bold text-orange-600 hover:text-orange-800 transition">
                                View all applications →
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-stone-50 p-8">
            @yield('content')
        </main>
    </div>

    {{-- GLOBAL MODAL --}}
    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm" @click="showModal = false"></div>
        
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col max-h-[90vh]" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            
            <div class="px-8 py-4 border-b border-stone-100 flex justify-between items-center bg-white shrink-0">
                <h2 class="text-xl font-bold text-stone-800" x-text="modalType === 'add' ? 'New Pet Entry' : (modalType === 'edit' ? 'Update Pet Profile' : 'Application Review')"></h2>
                <button @click="showModal = false" class="p-2 hover:bg-stone-100 rounded-full transition text-stone-400">&times;</button>
            </div>

            <div class="flex-1 overflow-y-auto p-0">
                <div x-show="modalType === 'add'">@include('staff.modals._add-pet')</div>
                <div x-show="modalType === 'edit'">@include('staff.modals._edit-pet')</div>
                <div x-show="modalType === 'application'">@include('staff.modals._review-app')</div>
            </div>
        </div>
    </div>

</body>
</html>