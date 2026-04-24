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
            {{-- THE NOTIFICATION BELL (Now clickable!) --}}
            <a href="{{ route($dashboardRoute) }}" style="background:none; border:none; margin-right: 1.5rem; cursor:pointer; color: var(--text-muted); position: relative; padding-top: 5px; display: inline-block;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                </svg>
                
                {{-- THE DYNAMIC RED DOT --}}
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span style="position:absolute; top: -2px; right: -6px; min-width: 18px; height: 18px; background: #e3342f; color: white; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; padding: 0 4px;">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </a>

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