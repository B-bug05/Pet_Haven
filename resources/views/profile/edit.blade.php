@php
    $layout = 'layouts.app';
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') $layout = 'layouts.admin';
        if (auth()->user()->role === 'staff') $layout = 'layouts.staff';
    }
@endphp

@extends($layout)

@section('content')
<div style="max-width: 700px; margin: 2rem auto; padding: 0 1rem;">

    <header style="margin-bottom: 2rem; border-bottom: 2px solid #f0ebe1; padding-bottom: 1.5rem;">
        <h1 style="color: var(--text-dark, #333); font-size: 2rem; margin: 0 0 0.25rem 0;">⚙️ Profile Settings</h1>
        <p style="color: var(--text-muted, #888); margin: 0;">Manage your account information and security settings.</p>
    </header>

    {{-- Profile Info --}}
    <div style="background: #fff; border: 1px solid #e2dcd3; border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- Password --}}
    <div style="background: #fff; border: 1px solid #e2dcd3; border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
        @include('profile.partials.update-password-form')
    </div>

    {{-- ID Verification --}}
    <div style="background: #fff; border: 1px solid #e2dcd3; border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
        @include('profile.partials.id-verification-form')
    </div>
    
    {{-- Delete Account --}}
    <div style="background: #fff; border: 1px solid #fecaca; border-radius: 16px; padding: 2rem; margin-bottom: 1.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.03);">
        @include('profile.partials.delete-user-form')
    </div>

</div>
@endsection