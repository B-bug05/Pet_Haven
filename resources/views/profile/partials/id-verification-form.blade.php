<section>
    <h2 style="font-size: 1.1rem; font-weight: 700; color: var(--text-dark); margin: 0 0 0.25rem 0;">🪪 Identity Verification</h2>
    <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0 0 1.5rem 0;">
        Upload a valid government-issued ID to get your account verified. Verified accounts are prioritized by staff during application review.
    </p>

    {{-- Current Status Badge --}}
    <div style="margin-bottom: 1.5rem;">
        @if(auth()->user()->verification_status === 'verified')
            <span style="background: #D4EDDA; color: #155724; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; border: 1px solid #c3e6cb;">
                ✅ Verified
            </span>
        @elseif(auth()->user()->verification_status === 'pending')
            <span style="background: #FFF3CD; color: #856404; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; border: 1px solid #ffeeba;">
                ⏳ Pending Review
            </span>
            <p style="color: var(--text-muted); font-size: 0.82rem; margin-top: 0.5rem;">Your ID has been submitted and is being reviewed by staff.</p>
        @elseif(auth()->user()->verification_status === 'rejected')
            <span style="background: #F8D7DA; color: #721C24; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; border: 1px solid #f5c6cb;">
                ❌ Rejected
            </span>
            <p style="color: var(--text-muted); font-size: 0.82rem; margin-top: 0.5rem;">Your ID was rejected. Please upload a clearer or valid document.</p>
        @else
            <span style="background: #e2dcd3; color: #666; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 0.85rem;">
                ○ Unverified
            </span>
        @endif
    </div>

    @if(auth()->user()->verification_status !== 'verified')
        <form method="POST" action="{{ route('profile.upload-id') }}" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-dark); margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.5px;">
                    Upload Valid ID
                </label>
                <input type="file" name="id_document" accept=".jpg,.jpeg,.png,.pdf" required
                    style="width: 100%; padding: 0.75rem; border: 1.5px solid #e2dcd3; border-radius: 10px; font-size: 0.9rem; font-family: inherit; box-sizing: border-box; background: white;">
                <p style="color: var(--text-muted); font-size: 0.78rem; margin-top: 0.4rem;">
                    Accepted: JPG, PNG, PDF · Max 5MB · Government-issued ID only (passport, driver's license, national ID)
                </p>
                @error('id_document')
                    <p style="color: #e3342f; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                style="background: #e67e22; color: white; border: none; padding: 0.7rem 1.75rem; border-radius: 10px; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                Submit for Verification
            </button>

            @if(session('success'))
                <p style="color: #155724; font-size: 0.85rem; font-weight: 600; margin-top: 0.75rem;">✅ {{ session('success') }}</p>
            @endif
        </form>
    @else
        <p style="color: var(--text-muted); font-size: 0.85rem;">Your identity has been confirmed. No further action needed.</p>
    @endif
</section>