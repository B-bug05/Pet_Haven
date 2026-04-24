@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div style="max-width: 900px; margin: 2rem auto; padding: 0 1rem;">
    
    <header style="margin-bottom: 2rem; border-bottom: 1px solid #eee; padding-bottom: 1.5rem;">
        <h1 style="color: var(--text-dark); font-size: 2rem;">Welcome home, {{ auth()->user()->name }}!</h1>
        <p style="color: var(--text-muted);">Track your adoption journey and manage your profile here.</p>
    </header>

    {{-- NEW: ALERTS SECTION (Styled to match your site) --}}
    <section style="margin-bottom: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.25rem; color: var(--text-dark); margin: 0;">Recent Alerts</h2>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span style="background: var(--primary, #e67e22); color: white; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">
                    {{ auth()->user()->unreadNotifications->count() }} New
                </span>
            @endif
        </div>

        <div style="background: #fff; border: 1px solid #e2dcd3; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            @forelse(auth()->user()->unreadNotifications as $notification)
                <div style="padding: 1.5rem; border-bottom: 1px solid #e2dcd3; background: #fffaf0; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="margin: 0 0 0.5rem 0; color: var(--text-dark); font-weight: 600;">{{ $notification->data['message'] }}</p>
                        <p style="margin: 0; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                        @csrf
                        <button type="submit" style="background: none; border: 1.5px solid var(--primary, #e67e22); color: var(--primary, #e67e22); padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: bold; transition: all 0.2s;">
                            Mark as Read ✓
                        </button>
                    </form>
                </div>
            @empty
                <div style="padding: 2.5rem; text-align: center;">
                    <span style="font-size: 2.5rem;">🔔</span>
                    <p style="color: var(--text-muted); margin-top: 0.75rem; font-weight: 500;">You're all caught up! No new alerts.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- YOUR ORIGINAL APPLICATIONS SECTION --}}
    <section>
        <h2 style="font-size: 1.25rem; color: var(--text-dark); margin-bottom: 1rem;">My Applications</h2>
        
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse ($applications as $app)
                @php
                    $imageUrl = 'https://images.unsplash.com/photo-1543852786-1cf6624b9987?q=80&w=400&auto=format&fit=crop';
                    if (!empty($app->pet->image)) {
                        $imageUrl = \Illuminate\Support\Str::startsWith($app->pet->image, ['http://', 'https://']) ? $app->pet->image : asset('storage/' . $app->pet->image);
                    } elseif (!empty($app->pet->image_url)) {
                        $imageUrl = $app->pet->image_url;
                    }
                @endphp

                <div style="background: #fff; border: 1px solid #e2dcd3; border-radius: 12px; padding: 1.5rem; display: flex; gap: 1.5rem; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
                    
                    <img src="{{ $imageUrl }}" alt="{{ $app->pet->name }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;" onerror="this.src='https://placehold.co/100x100'">
                    
                    <div style="flex-grow: 1;">
                        <h3 style="margin-bottom: 0.25rem; color: var(--text-dark); font-size: 1.2rem;">{{ $app->pet->name }}</h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.75rem;">Applied on {{ $app->created_at->format('M d, Y') }}</p>
                        
                        @if($app->status === 'Under Review')
                            <span style="background: #FFF3CD; color: #856404; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem;">⏳ Under Review</span>
                        @elseif($app->status === 'Approved for Adoption')
                            <span style="background: #D4EDDA; color: #155724; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem;">🎉 Approved for Adoption</span>
                        @else
                            <span style="background: #F8D7DA; color: #721C24; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.85rem;">Application Declined</span>
                        @endif
                    </div>

                    <div>
                        <a href="{{ route('pets.show', $app->pet->id) }}" style="background: none; border: 1px solid #e2dcd3; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; color: var(--text-muted); text-decoration: none;">View Pet</a>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 4rem 2rem; background: #faf9f7; border-radius: 12px; border: 1px dashed #d1ccc5;">
                    <h3 style="color: var(--text-dark); margin-bottom: 0.5rem;">No applications yet</h3>
                    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">You haven't submitted any adoption applications.</p>
                    <a href="{{ route('discover') }}" style="background: var(--primary); color: white; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 500;">Find a Pet</a>
                </div>
            @endforelse
        </div>
    </section>

</div>
@endsection