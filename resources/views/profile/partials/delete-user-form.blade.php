<section>
    <h2 style="font-size: 1.1rem; font-weight: 700; color: #991b1b; margin: 0 0 0.25rem 0;">⚠️ Delete Account</h2>
    <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0 0 1.5rem 0;">
        Once your account is deleted, all data will be permanently removed. This cannot be undone.
    </p>

    <button type="button" onclick="document.getElementById('deleteAccountDialog').showModal()"
        style="background: #dc2626; color: white; border: none; padding: 0.7rem 1.75rem; border-radius: 10px; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
        Delete My Account
    </button>

    {{-- Native Dialog for confirmation --}}
    <dialog id="deleteAccountDialog" style="border: none; border-radius: 16px; padding: 2rem; max-width: 420px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,0.15);">
        <h3 style="color: #991b1b; font-size: 1.1rem; font-weight: 700; margin: 0 0 0.5rem 0;">Are you absolutely sure?</h3>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0 0 1.5rem 0;">
            This will permanently delete your account and all your applications. Please enter your password to confirm.
        </p>

        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-dark); margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.5px;">Password</label>
                <input type="password" name="password" placeholder="Enter your password to confirm"
                    style="width: 100%; padding: 0.75rem 1rem; border: 1.5px solid #fecaca; border-radius: 10px; font-size: 0.95rem; outline: none; font-family: inherit; box-sizing: border-box;">
                @error('password', 'userDeletion')
                    <p style="color: #e3342f; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('deleteAccountDialog').close()"
                    style="background: #f5f5f5; color: #555; border: none; padding: 0.65rem 1.5rem; border-radius: 10px; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit"
                    style="background: #dc2626; color: white; border: none; padding: 0.65rem 1.5rem; border-radius: 10px; font-size: 0.9rem; font-weight: 600; cursor: pointer;">
                    Yes, Delete Account
                </button>
            </div>
        </form>
    </dialog>
</section>