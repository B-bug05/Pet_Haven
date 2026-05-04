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

    <div id="petGridContainer" class="pet-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
        @include('pets._grid', ['pets' => $pets, 'favorites' => []])
    </div>

    <div style="text-align: center; margin: 2rem 0;">
        <button id="loadMoreBtn" style="background: var(--primary, #e67e22); color: white; border: none; padding: 0.8rem 2.5rem; border-radius: 50px; font-size: 1rem; font-weight: 600; cursor: pointer; display: none;">
            Load More Pets 🐾
        </button>
    </div>

    <script>
        const searchInput = document.getElementById('petSearch');
        const container = document.getElementById('petGridContainer');
        const loadMoreBtn = document.getElementById('loadMoreBtn');

        let currentPage = 1;
        let currentSearch = '';
        let currentFilter = 'all';
        let isLoading = false;

        const fetchPets = (append = false) => {
            if (isLoading) return;
            isLoading = true;

            const url = `{{ route('pets.search') }}?search=${currentSearch}&filter=${currentFilter}&page=${currentPage}`;

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (append) {
                        container.insertAdjacentHTML('beforeend', data.html);
                    } else {
                        container.innerHTML = data.html;
                    }
                    loadMoreBtn.style.display = data.hasMore ? 'inline-block' : 'none';
                    isLoading = false;
                })
                .catch(err => {
                    console.error(err);
                    isLoading = false;
                });
        };

        searchInput.addEventListener('input', () => {
            currentSearch = searchInput.value;
            currentPage = 1;
            fetchPets(false);
        });

        document.getElementById('applyFiltersBtn').addEventListener('click', () => {
            currentFilter = selections.type !== 'all' ? selections.type.toLowerCase() + 's' : 'all';
            currentPage = 1;
            fetchPets(false);
        });

        loadMoreBtn.addEventListener('click', () => {
            currentPage++;
            fetchPets(true);
        });

        @if($pets->hasMorePages())
            loadMoreBtn.style.display = 'inline-block';
        @endif
    </script>
@endsection