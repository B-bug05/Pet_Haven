<div class="modal-backdrop" id="authBackdrop" onclick="closeOnOutsideClick(event, 'authBackdrop')">
    <div class="auth-modal">
        <span class="close-modal" onclick="closeModal('authBackdrop')">&times;</span>
        
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <h3 style="color: var(--primary); margin-bottom: 0.5rem;">Ready to Meet Them?</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Please log in or create a free Adopter account to apply!</p>
        </div>

        <div class="auth-tabs">
            <button class="auth-tab active" id="tab-login" onclick="switchAuthTab('login')">Log In</button>
            <button class="auth-tab" id="tab-register" onclick="switchAuthTab('register')">Sign Up</button>
        </div>

        <form id="form-login" class="auth-form active" method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="redirect_to" id="login_redirect_to" value="">
            <input type="hidden" name="_tab" value="login">
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com" value="{{ old('email') }}">
                @error('email')
                    <span style="color: #e3342f; font-size: 0.85rem; margin-top: -4px;">{{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="••••••••">
                @error('password')
                    <span style="color: #e3342f; font-size: 0.85rem; margin-top: -4px;">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn-auth">Log In to Apply</button>
        </form>

        <form id="form-register" class="auth-form" method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="_tab" value="register">
            <div class="input-group">
                <label>Full Name</label>
                <input type="text" name="name" required placeholder="John Doe" value="{{ old('name') }}">
                @error('name')
                    <span style="color: #e3342f; font-size: 0.85rem; margin-top: -4px;">{{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com" value="{{ old('email') }}">
                @error('email')
                    <span style="color: #e3342f; font-size: 0.85rem; margin-top: -4px;">{{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Create a password">
            </div>
            <div class="input-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" required placeholder="Confirm your password">
                @error('password')
                    <span style="color: #e3342f; font-size: 0.85rem; margin-top: -4px;">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="btn-auth">Create Account</button>
        </form>
    </div>
</div>

<script>
    function openLoginModalForPet(redirectUrl) {
        // 1. Put the pet's URL into the hidden form input
        const redirectInput = document.getElementById('login_redirect_to');
        if (redirectInput) {
            redirectInput.value = redirectUrl;
        }
        // 2. Open your modal exactly like normal
        openModal('authBackdrop');
    }
</script>