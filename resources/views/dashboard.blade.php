@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div style="max-width: 900px; margin: 2rem auto; padding: 0 1rem;">

    {{-- Header --}}
    <header style="margin-bottom: 2rem; border-bottom: 2px solid #f0ebe1; padding-bottom: 1.5rem;">
        <h1 style="color: var(--text-dark); font-size: 2rem; margin: 0 0 0.25rem 0;">
            Welcome home, {{ explode(' ', auth()->user()->name)[0] }}! 👋
        </h1>
        <p style="color: var(--text-muted); margin: 0;">Track your adoption journey and manage your applications here.</p>
    </header>

    {{-- ALERTS SECTION --}}
    <section style="margin-bottom: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.25rem; color: var(--text-dark); margin: 0;">🔔 Recent Alerts</h2>
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span style="background: var(--primary, #e67e22); color: white; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">
                    {{ auth()->user()->unreadNotifications->count() }} New
                </span>
            @endif
        </div>

        <div style="background: #fff; border: 1px solid #e2dcd3; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.02);">
            @forelse(auth()->user()->unreadNotifications as $notification)
                <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #f0ebe1; background: #fffaf0; display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                    <div style="display: flex; align-items: flex-start; gap: 1rem;">
                        <span style="font-size: 1.5rem; margin-top: 2px;">
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
                        <div>
                            <p style="margin: 0 0 0.25rem 0; color: var(--text-dark); font-weight: 600; font-size: 0.95rem;">
                                {{ $notification->data['message'] }}
                            </p>
                            <p style="margin: 0; color: var(--text-muted); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" style="flex-shrink: 0;">
                        @csrf
                        <button type="submit" style="background: none; border: 1.5px solid var(--primary, #e67e22); color: var(--primary, #e67e22); padding: 0.4rem 0.9rem; border-radius: 6px; cursor: pointer; font-size: 0.8rem; font-weight: bold; white-space: nowrap;">
                            Mark Read ✓
                        </button>
                    </form>
                </div>
            @empty
                <div style="padding: 2rem; text-align: center;">
                    <span style="font-size: 2rem;">✅</span>
                    <p style="color: var(--text-muted); margin-top: 0.5rem; font-weight: 500; font-size: 0.9rem;">You're all caught up! No new alerts.</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- APPLICATIONS SECTION --}}
    <section>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h2 style="font-size: 1.25rem; color: var(--text-dark); margin: 0;">📋 My Applications</h2>
            <a href="{{ route('discover') }}" style="background: var(--primary, #e67e22); color: white; text-decoration: none; padding: 0.5rem 1.1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600;">
                + Find More Pets
            </a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @forelse($applications as $app)
                @php
                    $imageUrl = 'https://placehold.co/100x100?text=No+Photo';
                    if (!empty($app->pet->image)) {
                        $imageUrl = \Illuminate\Support\Str::startsWith($app->pet->image, ['http://', 'https://'])
                            ? $app->pet->image
                            : asset('storage/' . $app->pet->image);
                    }

                    $isApproved = $app->status === 'Approved for Adoption';
                    $isDeclined = $app->status === 'Application Declined';
                    $isPending  = $app->status === 'Under Review';
                    $borderColor = $isPending ? '#ffc107' : ($isApproved ? '#28a745' : ($isDeclined ? '#dc3545' : '#e2dcd3'));

                    // Find the pending welfare check-in for this application, if any
                    $pendingCheckin = $isApproved
                        ? $app->welfareCheckins->where('status', 'pending')->first()
                        : null;
                @endphp

                <div style="background: #fff; border: 1px solid #e2dcd3; border-left: 4px solid {{ $borderColor }}; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">

                    <div style="display: flex; gap: 1.5rem; align-items: center;">

                        {{-- Pet Photo --}}
                        <img src="{{ $imageUrl }}" alt="{{ $app->pet->name }}"
                            style="width: 90px; height: 90px; object-fit: cover; border-radius: 10px; flex-shrink: 0;"
                            onerror="this.src='https://placehold.co/100x100?text=No+Photo'">

                        {{-- Info --}}
                        <div style="flex-grow: 1; min-width: 0;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
                                <h3 style="margin: 0; color: var(--text-dark); font-size: 1.1rem;">{{ $app->pet->name }}</h3>
                                <span style="font-size: 0.78rem; color: var(--text-muted);">{{ $app->pet->type }} · {{ $app->pet->age }}</span>
                            </div>
                            <p style="color: var(--text-muted); font-size: 0.82rem; margin: 0 0 0.75rem 0;">
                                Applied {{ $app->created_at->format('M d, Y') }} · {{ $app->created_at->diffForHumans() }}
                            </p>

                            @if($isPending)
                                <span style="background: #FFF3CD; color: #856404; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.82rem; border: 1px solid #ffeeba;">
                                    ⏳ Under Review
                                </span>
                            @elseif($isApproved)
                                <span style="background: #D4EDDA; color: #155724; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.82rem; border: 1px solid #c3e6cb;">
                                    🎉 Approved for Adoption!
                                </span>
                            @elseif($isDeclined)
                                <span style="background: #F8D7DA; color: #721C24; padding: 5px 12px; border-radius: 20px; font-weight: 600; font-size: 0.82rem; border: 1px solid #f5c6cb;">
                                    ❌ Application Declined
                                </span>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div style="display: flex; flex-direction: column; gap: 0.5rem; flex-shrink: 0; align-items: flex-end;">
                            <a href="{{ route('pets.show', $app->pet->id) }}"
                                style="background: none; border: 1.5px solid #e2dcd3; padding: 0.45rem 1rem; border-radius: 8px; color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 500; white-space: nowrap; text-align: center;">
                                View Pet
                            </a>
                            @if($isDeclined && $app->pet->status === 'Ready for Adoption')
                                <a href="{{ route('pets.show', $app->pet->id) }}"
                                    style="background: var(--primary, #e67e22); color: white; text-decoration: none; padding: 0.45rem 1rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; white-space: nowrap; text-align: center;">
                                    Re-apply 🐾
                                </a>
                            @endif
                            @if($isApproved)
                                <span style="font-size: 0.78rem; color: #155724; font-weight: 600; text-align: right; max-width: 120px; line-height: 1.3;">
                                    🏠 Contact the shelter to arrange pickup!
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Welfare Check-in Panel (approved apps with a pending request only) --}}
                    @if($pendingCheckin)
                        <div style="margin-top: 1.25rem; padding: 1.25rem; background: #fffbf0; border: 1.5px solid #fde68a; border-radius: 10px;">
                            <p style="margin: 0 0 0.5rem 0; font-weight: 700; color: #92400e; font-size: 0.9rem;">
                                🐾 Welfare Check-in Requested
                            </p>
                            <p style="margin: 0 0 1rem 0; color: #78350f; font-size: 0.85rem;">
                                PetHaven has requested an update on how {{ $app->pet->name }} is doing. Please share a short update below!
                            </p>

                            <form method="POST" action="{{ route('welfare.submit', $pendingCheckin->id) }}" enctype="multipart/form-data">
                                @csrf
                                <div style="margin-bottom: 0.75rem;">
                                    <textarea name="message" required rows="3" placeholder="How is {{ $app->pet->name }} settling in? Any updates on health, behaviour, or happiness..."
                                        style="width: 100%; padding: 0.75rem; border: 1.5px solid #fde68a; border-radius: 8px; font-size: 0.9rem; font-family: inherit; resize: vertical; box-sizing: border-box; background: white;"></textarea>
                                </div>
                                <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                                    <label id="photoLabel_{{ $pendingCheckin->id }}" 
                                        style="font-size: 0.82rem; color: #78350f; font-weight: 600; cursor: pointer; background: white; border: 1.5px solid #fde68a; padding: 0.5rem 1rem; border-radius: 8px;">
                                        📷 <span id="photoLabelText_{{ $pendingCheckin->id }}">Attach a photo (required)</span>
                                        <input type="file" name="photo" accept="image/*" required
                                            onchange="document.getElementById('photoLabelText_{{ $pendingCheckin->id }}').innerText = this.files[0]?.name || 'Attach a photo (required)'"
                                            style="display: none;">
                                    </label>
                                    <button type="submit"
                                        style="background: #e67e22; color: white; border: none; padding: 0.6rem 1.4rem; border-radius: 8px; font-size: 0.88rem; font-weight: 700; cursor: pointer;">
                                        Submit Update
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Past submitted check-ins --}}
                    @if($isApproved && $app->welfareCheckins->where('status', 'submitted')->count() > 0)
                        <div style="margin-top: 1rem; padding: 1rem 1.25rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;">
                            <p style="margin: 0 0 0.75rem 0; font-weight: 700; color: #166534; font-size: 0.85rem;">✅ Submitted Check-ins</p>
                            @foreach($app->welfareCheckins->where('status', 'submitted') as $checkin)
                                <div style="margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px solid #d1fae5;">
                                    <p style="margin: 0 0 0.25rem 0; font-size: 0.85rem; color: #166534;">{{ $checkin->message }}</p>
                                    @if($checkin->photo)
                                        <img src="{{ asset('storage/' . $checkin->photo) }}"
                                            style="margin-top: 0.5rem; max-height: 150px; border-radius: 8px; object-fit: cover;">
                                    @endif
                                    <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: #6b7280;">{{ $checkin->created_at->format('M d, Y') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>
            @empty
                <div style="text-align: center; padding: 4rem 2rem; background: #faf9f7; border-radius: 12px; border: 2px dashed #e2dcd3;">
                    <span style="font-size: 3rem;">🐾</span>
                    <h3 style="color: var(--text-dark); margin: 1rem 0 0.5rem 0;">No applications yet</h3>
                    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">You haven't applied for any pets yet. Find your perfect companion!</p>
                    <a href="{{ route('discover') }}" style="background: var(--primary, #e67e22); color: white; text-decoration: none; padding: 0.75rem 1.75rem; border-radius: 8px; font-weight: 600; font-size: 0.95rem;">
                        🔍 Discover Pets
                    </a>
                </div>
            @endforelse
        </div>
    </section>

</div>
@endsection