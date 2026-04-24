@extends('layouts.app')

@section('title', 'Browse Available Pets')

@section('content')
    <header class="browse-header">
        <h1>Find Your New Best Friend</h1>
        <p>These beautiful pets are looking for their forever homes.</p>
        
        <form action="{{ route('browse') }}" method="GET" class="search-container" style="display: flex; gap: 10px; width: 100%; max-width: 600px; margin: 0 auto;">
            <input type="text" name="search" id="petSearch" class="search-input" placeholder="Search by name, species, or breed..." value="{{ request('search') }}" style="flex-grow: 1;">
            
            <button type="button" class="btn-filter" onclick="openModal('filterBackdrop')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                Filters
            </button>
            
            <button type="submit" class="btn-search">Search</button>
        </form>
    </header>

    <main class="pet-grid" id="petGridContainer">
        @include('pets._grid', ['pets' => $pets])
    </main>

    <div class="modal-backdrop" id="filterBackdrop" onclick="closeOnOutsideClick(event, 'filterBackdrop')">
        <div class="modal-box">
            <span class="close-modal" onclick="closeModal('filterBackdrop')">&times;</span>
            <h2 style="margin-bottom: 1.5rem; color: var(--text-dark);">Filter Pets</h2>
            
            <div class="filter-section">
                <h4>Animal Type</h4>
                <div class="filter-options">
                    <span class="filter-chip active" data-category="type" data-value="all">All</span>
                    <span class="filter-chip" data-category="type" data-value="Dog">Dogs</span>
                    <span class="filter-chip" data-category="type" data-value="Cat">Cats</span>
                </div>
            </div>

            <div class="filter-section">
                <h4>Age Group</h4>
                <div class="filter-options">
                    <span class="filter-chip active" data-category="age" data-value="any">Any Age</span>
                    <span class="filter-chip" data-category="age" data-value="baby">Baby</span>
                    <span class="filter-chip" data-category="age" data-value="adult">Adult</span>
                </div>
            </div>

            <button class="btn-auth" id="applyFiltersBtn" style="margin-top: 1rem; width: 100%;">Apply Filters</button>
        </div>
    </div>
    <div class="modal-backdrop" id="applicationBackdrop" onclick="closeOnOutsideClick(event, 'applicationBackdrop')">
        <div class="auth-modal" style="max-width: 500px;">
            <span class="close-modal" onclick="closeModal('applicationBackdrop')">&times;</span>
            
            <div style="text-align: center; margin-bottom: 1.5rem;">
                <h3 style="color: var(--primary); margin-bottom: 0.5rem;">Apply for Adoption</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem;">You are applying to give <strong id="apply_pet_name" style="color: var(--text-dark);">this pet</strong> a forever home!</p>
            </div>

            <form method="POST" action="{{ route('applications.store') }}">
                @csrf
                <input type="hidden" name="pet_id" id="apply_pet_id">

                <div class="input-group">
                    <label>Home Address</label>
                    <input type="text" name="adopter_address" required placeholder="123 Main St, City, State" value="{{ old('adopter_address') }}">
                </div>
                
                <div class="input-group">
                    <label>Contact Number</label>
                    <input type="text" name="contact_number" required placeholder="(555) 123-4567" value="{{ old('contact_number') }}">
                </div>

                <div class="input-group">
                    <label>Why are you a good match? (Optional)</label>
                    <textarea name="adopter_message" rows="4" placeholder="Tell us about your home, family, and experience with pets..." style="width: 100%; padding: 0.8rem; border: 1.5px solid #e2dcd3; border-radius: 10px; font-family: inherit; font-size: 0.95rem; resize: vertical;">{{ old('adopter_message') }}</textarea>
                </div>

                <button type="submit" class="btn-auth" style="margin-top: 1rem;">Submit Application</button>
            </form>
        </div>
    </div>

    <script>
    // 1. Keep track of what the user selected
    let selections = { type: 'all', age: 'any' };

    // 2. Function to open the Application Modal
    function openApplicationModal(petId, petName) {
        document.getElementById('apply_pet_id').value = petId;
        document.getElementById('apply_pet_name').innerText = petName;
        openModal('applicationBackdrop');
    }

    // 3. Handle Chip Clicks (Highlighting)
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.addEventListener('click', function() {
            this.parentElement.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            const category = this.getAttribute('data-category');
            const value = this.getAttribute('data-value');
            selections[category] = value;
        });
    });

    // 4. Function that talks to Laravel Controller
    function performSearch() {
        const searchInput = document.getElementById('petSearch');
        const search = searchInput ? searchInput.value : '';
        
        // This builds the URL for your search method
        const url = `{{ route('pets.search') }}?search=${search}&type=${selections.type}&age=${selections.age}`;

        fetch(url)
            .then(res => res.text())
            .then(html => {
                // This injects the new pets into the grid
                const container = document.getElementById('petGridContainer');
                if (container) {
                    container.innerHTML = html;
                }
                
                // Close the modal if it's open
                if (typeof closeModal === 'function') {
                    closeModal('filterBackdrop');
                }
            })
            .catch(err => console.error("Filter error:", err));
    }

    // 5. Connect the buttons to the function
    document.getElementById('applyFiltersBtn').addEventListener('click', performSearch);
    
    // Also trigger as you type
    document.getElementById('petSearch').addEventListener('input', performSearch);
</script>
@endsection