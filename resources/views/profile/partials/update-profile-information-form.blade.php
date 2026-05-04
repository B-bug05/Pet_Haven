<section>
    <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-dark); margin: 0 0 0.25rem 0;">Profile Information</h2>
    <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0 0 1.5rem 0;">Update your name and email address.</p>

    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')

        {{-- Name --}}
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-dark); margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.5px;">Full Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus
                style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #e2dcd3; border-radius: 10px; font-size: 0.95rem; outline: none; font-family: inherit; box-sizing: border-box; transition: border-color 0.2s;"
                onfocus="this.style.borderColor='#e67e22'" onblur="this.style.borderColor='#e2dcd3'">
            @error('name')
                <p style="color: #e3342f; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-dark); margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.5px;">Email Address</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #e2dcd3; border-radius: 10px; font-size: 0.95rem; outline: none; font-family: inherit; box-sizing: border-box; transition: border-color 0.2s;"
                onfocus="this.style.borderColor='#e67e22'" onblur="this.style.borderColor='#e2dcd3'">
            @error('email')
                <p style="color: #e3342f; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div style="margin-top: 0.75rem; padding: 0.75rem 1rem; background: #FFF3CD; border: 1px solid #ffeeba; border-radius: 8px;">
                    <p style="font-size: 0.85rem; color: #856404; margin: 0 0 0.5rem 0;">Your email address is unverified.</p>
                    <button form="send-verification"
                        style="background: none; border: none; color: #e67e22; font-size: 0.85rem; font-weight: 600; cursor: pointer; padding: 0; text-decoration: underline;">
                        Click here to resend the verification email.
                    </button>
                    @if(session('status') === 'verification-link-sent')
                        <p style="color: #155724; font-size: 0.8rem; margin: 0.5rem 0 0 0; font-weight: 600;">✅ Verification link sent!</p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Submit --}}
        <div style="display: flex; align-items: center; gap: 1rem;">
            <button type="submit"
                style="background: #e67e22; color: white; border: none; padding: 0.7rem 1.75rem; border-radius: 10px; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                Save Changes
            </button>
            @if(session('status') === 'profile-updated')
                <p style="color: #155724; font-size: 0.85rem; font-weight: 600;">✅ Profile updated!</p>
            @endif
        </div>
    </form>
</section>