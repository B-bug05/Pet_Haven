@extends('layouts.app')

@section('title', 'Discover Pets')

@section('content')
    <header class="browse-header" style="text-align: center; margin: 3rem 0;">
        <h1>Discover Your Match</h1>
        
        {{-- Search & Filter Bar --}}
        <div style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; background: #fff; padding: 1.5rem; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #f0ebe1;">
                
                <div style="flex: 2; min-width: 250px; position: relative;">
                    <input type="text" id="petSearch" placeholder="Search by name, breed, or type..." 
                        style="width: 100%; padding: 0.8rem 1rem; border: 2px solid #f0ebe1; border-radius: 10px; outline: none; transition: border-color 0.3s;">
                </div>

                <div style="flex: 1; min-width: 200px;">
                    <select id="petFilter" style="width: 100%; padding: 0.8rem; border: 2px solid #f0ebe1; border-radius: 10px; outline: none; background: white; cursor: pointer;">
                        <option value="all">All Pets</option>
                        <option value="recently_added">Recently Added</option>
                        <option value="my_favorites">My Favorites ❤️</option>
                        <option value="dogs">Dogs</option>
                        <option value="cats">Cats</option>
                    </select>
                </div>
            </div>
        </div>
    </header>

    {{-- The ONLY Grid Container (With the styling you like!) --}}
    <main id="petGridContainer" class="pet-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
        @include('pets._grid', ['pets' => $pets, 'favorites' => $favorites ?? []])
    </main>

    <script>
        const searchInput = document.getElementById('petSearch');
        const filterSelect = document.getElementById('petFilter');
        const container = document.getElementById('petGridContainer');

        const performSearch = () => {
            const searchValue = searchInput.value;
            const filterValue = filterSelect.value;

            fetch(`{{ route('pets.search') }}?search=${searchValue}&filter=${filterValue}`)
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                });
        };

        searchInput.addEventListener('input', performSearch);
        filterSelect.addEventListener('change', performSearch);
    </script>
@endsection