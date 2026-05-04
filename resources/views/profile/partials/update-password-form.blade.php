<section>
    <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-dark); margin: 0 0 0.25rem 0;">Update Password</h2>
    <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0 0 1.5rem 0;">Use a long, random password to keep your account secure.</p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        @method('PUT')

        {{-- Current Password --}}
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-dark); margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.5px;">Current Password</label>
            <input type="password" name="current_password" autocomplete="current-password"
                style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #e2dcd3; border-radius: 10px; font-size: 0.95rem; outline: none; font-family: inherit; box-sizing: border-box; transition: border-color 0.2s;"
                onfocus="this.style.borderColor='#e67e22'" onblur="this.style.borderColor='#e2dcd3'">
            @error('current_password', 'updatePassword')
                <p style="color: #e3342f; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
            @enderror
        </div>

        {{-- New Password --}}
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-dark); margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.5px;">New Password</label>
            <input type="password" name="password" autocomplete="new-password"
                style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #e2dcd3; border-radius: 10px; font-size: 0.95rem; outline: none; font-family: inherit; box-sizing: border-box; transition: border-color 0.2s;"
                onfocus="this.style.borderColor='#e67e22'" onblur="this.style.borderColor='#e2dcd3'">
            @error('password', 'updatePassword')
                <p style="color: #e3342f; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-dark); margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.5px;">Confirm New Password</label>
            <input type="password" name="password_confirmation" autocomplete="new-password"
                style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #e2dcd3; border-radius: 10px; font-size: 0.95rem; outline: none; font-family: inherit; box-sizing: border-box; transition: border-color 0.2s;"
                onfocus="this.style.borderColor='#e67e22'" onblur="this.style.borderColor='#e2dcd3'">
            @error('password_confirmation', 'updatePassword')
                <p style="color: #e3342f; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button type="submit"
                style="background: #e67e22; color: white; border: none; padding: 0.7rem 1.75rem; border-radius: 10px; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                Update Password
            </button>
            @if(session('status') === 'password-updated')
                <p style="color: #155724; font-size: 0.85rem; font-weight: 600;">✅ Password updated!</p>
            @endif
        </div>
    </form>
</section>