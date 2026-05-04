<nav>
    @php
        // 🚦 Calculate the correct dashboard route based on the logged-in user's role
        $dashboardRoute = 'dashboard'; // Adopter default
        if (auth()->check()) {
            if (auth()->user()->role === 'admin') $dashboardRoute = 'admin.dashboard';
            if (auth()->user()->role === 'staff') $dashboardRoute = 'staff.dashboard';
        }
    @endphp

    <div class="nav-left">
        {{-- Make the logo smart: click it to go to your specific dashboard, or home if logged out --}}
        <a href="{{ auth()->check() ? route($dashboardRoute) : route('home') }}" class="logo">🐾 PetHaven</a>
    </div>
    
    <div class="nav-center">
        @auth
            {{-- Dynamic Dashboard Link --}}
            <a href="{{ route($dashboardRoute) }}" class="nav-link {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}">
                Dashboard
            </a>
            
            {{-- ONLY show Discover Pets to regular adopters --}}
            @if(auth()->user()->role === 'adopter')
                <a href="{{ route('discover') }}" class="nav-link {{ request()->routeIs('discover') ? 'active' : '' }}">
                    Discover Pets
                </a>
            @endif
            
        @else
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
            <a href="{{ route('browse') }}" class="nav-link {{ request()->routeIs('browse') ? 'active' : '' }}">Browse Pets</a>
        @endauth
    </div>

    <div class="nav-right">
        @auth
            {{-- NOTIFICATION BELL --}}
            <div style="position: relative; margin-right: 1.5rem;" x-data="{ open: false }" @click.outside="open = false">
                
                <button @click="open = !open"
                    style="background: none; border: none; cursor: pointer; color: var(--text-muted); position: relative; padding: 5px; display: inline-flex; align-items: center;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span style="position: absolute; top: -2px; right: -4px; min-width: 18px; height: 18px; background: #e3342f; color: white; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; padding: 0 4px;">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>

                {{-- Dropdown Panel --}}
                <div x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                    x-cloak
                    style="position: absolute; right: 0; top: 44px; width: 320px; background: white; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,0.12); border: 1px solid #f0ebe1; z-index: 999; overflow: hidden;">

                    {{-- Header --}}
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 1px solid #f0ebe1; background: #fdfaf6;">
                        <div>
                            <p style="margin: 0; font-weight: 700; color: var(--text-dark); font-size: 0.9rem;">Notifications</p>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <p style="margin: 0; font-size: 0.75rem; color: #e67e22; font-weight: 600;">
                                    {{ auth()->user()->unreadNotifications->count() }} unread
                                </p>
                            @else
                                <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted);">All caught up!</p>
                            @endif
                        </div>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                @csrf
                                <button type="submit" style="background: none; border: none; font-size: 0.75rem; font-weight: 700; color: #e67e22; cursor: pointer;">
                                    Mark all read
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Notification List --}}
                    <ul style="list-style: none; margin: 0; padding: 0; max-height: 320px; overflow-y: auto;">
                        @forelse(auth()->user()->notifications->take(8) as $notification)
                            <li style="padding: 1rem 1.25rem; border-bottom: 1px solid #f0ebe1; display: flex; gap: 0.75rem; align-items: flex-start;
                                {{ is_null($notification->read_at) ? 'background: #fffaf0;' : '' }}">
                                <span style="font-size: 1.3rem; margin-top: 2px; flex-shrink: 0;">
                                    @if(($notification->data['type'] ?? '') === 'welfare_request')
                                        🐾
                                    @elseif(str_contains($notification->data['new_status'] ?? '', 'Approved'))
                                        🎉
                                    @elseif(str_contains($notification->data['new_status'] ?? '', 'Declined'))
                                        😢
                                    @else
                                        📋
                                    @endif
                                </span>
                                <div style="flex: 1; min-width: 0;">
                                    <p style="margin: 0 0 0.2rem 0; font-size: 0.85rem; font-weight: 600; color: var(--text-dark); line-height: 1.4;">
                                        {{ $notification->data['message'] ?? 'New notification' }}
                                    </p>
                                    <p style="margin: 0; font-size: 0.75rem; color: var(--text-muted);">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                @if(is_null($notification->read_at))
                                    <span style="width: 8px; height: 8px; background: #e67e22; border-radius: 50%; flex-shrink: 0; margin-top: 6px;"></span>
                                @endif
                            </li>
                        @empty
                            <li style="padding: 2rem; text-align: center; color: var(--text-muted); font-size: 0.9rem;">
                                No notifications yet.
                            </li>
                        @endforelse
                    </ul>

                    {{-- Footer --}}
                    <div style="padding: 0.75rem 1.25rem; border-top: 1px solid #f0ebe1; background: #fdfaf6; text-align: center;">
                        <a href="{{ route('dashboard') }}" style="font-size: 0.8rem; font-weight: 700; color: #e67e22; text-decoration: none;">
                            View all on dashboard →
                        </a>
                    </div>
                </div>
            </div>

            <div class="dropdown">
                <div style="display: flex; align-items: center; gap: 10px; cursor: pointer; padding: 0.4rem; padding-right: 1rem; border-radius: 50px; background: white; border: 1.5px solid #f0ebe1; transition: border-color 0.2s;">
                    <div style="width: 35px; height: 35px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span style="font-weight: 600; color: var(--text-dark);">
                        {{ explode(' ', Auth::user()->name)[0] }} <span style="font-size: 0.7em; margin-left: 3px;">▼</span>
                    </span>
                </div>
                
                <div class="dropdown-content" style="right: 0; left: auto; min-width: 220px; border: 1px solid rgba(44,37,34,0.05);">
                    <div style="padding: 15px 20px; border-bottom: 1px solid #f0ebe1; margin-bottom: 5px; background: #fdfaf6;">
                        <p style="font-weight: 700; color: var(--text-dark); font-size: 0.95rem; line-height: 1.2;">
                            {{ Auth::user()->name }} 
                            {{-- Added a quick visual indicator of their role! --}}
                            <span style="font-size: 0.7rem; background: #e67e22; color: white; padding: 2px 6px; border-radius: 10px; margin-left: 5px; text-transform: uppercase;">
                                {{ Auth::user()->role }}
                            </span>
                        </p>
                        <p style="font-size: 0.8rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->email }}</p>
                    </div>
                    
                    <a href="{{ route('profile.edit') }}">⚙️ Profile Settings</a>
                    
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0; padding: 0;">
                        @csrf
                        <button type="submit" style="width: 100%; text-align: left; background: none; border: none; padding: 12px 20px; color: #e3342f; font-size: 1rem; font-weight: 500; cursor: pointer; font-family: inherit; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#fff5eb'" onmouseout="this.style.backgroundColor='transparent'">
                            🚪 Log Out
                        </button>
                    </form>
                </div>
            </div>

        @else
            <a href="#" class="btn-primary" style="padding: 0.7rem 1.5rem;" onclick="openModal('authBackdrop')">Log In / Sign Up</a>
        @endauth
    </div>
</nav>