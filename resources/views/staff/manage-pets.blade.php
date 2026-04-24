@extends('layouts.staff')

@section('content')
<div class="max-w-7xl mx-auto" 
     x-data="{ viewMode: localStorage.getItem('petViewMode') || 'grid' }" 
     x-init="$watch('viewMode', val => localStorage.setItem('petViewMode', val))">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        
        {{-- UPDATED: Header changed, subtitle removed --}}
        <div>
            <h1 class="text-2xl font-bold text-stone-800">Pet Listings</h1>
        </div>
        
        <div class="flex items-center gap-4 w-full sm:w-auto">
            
            <form action="{{ route('staff.pets.index') }}" method="GET" class="w-full sm:w-auto flex items-center gap-3">
                
                {{-- UPDATED: Search Bar with 'X' Clear Button & Adopter Portal Styling (Rounded-Full) --}}
                <div class="relative w-full sm:w-72" x-data="{ search: '{{ request('search') }}' }">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    
                    <input type="text" name="search" x-model="search" x-ref="searchInput" placeholder="Search pets..." 
                           class="w-full bg-white border border-stone-200 text-stone-600 text-sm rounded-full py-2.5 pl-11 pr-10 focus:ring-orange-500 focus:border-orange-500 shadow-sm outline-none transition"
                           x-on:input.debounce.500ms="$event.target.form.submit()"
                           autofocus
                           onfocus="var val = this.value; this.value = ''; this.value = val;">
                           
                    {{-- The 'X' Button: Only shows when there is text --}}
                    <button type="button" x-show="search.length > 0" x-cloak
                            @click="search = ''; $nextTick(() => { $refs.searchInput.form.submit() })"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-stone-400 hover:text-orange-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                {{-- Filter & Sort Dropdown --}}
                <select name="sort" onchange="this.form.submit()" class="w-full sm:w-auto bg-white border border-stone-200 text-stone-600 text-sm rounded-lg py-2.5 pl-3 pr-10 outline-none cursor-pointer shadow-sm">
                    <optgroup label="Sort by Date">
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </optgroup>
                    <optgroup label="Filter by Status">
                        <option value="ready" {{ request('sort') === 'ready' ? 'selected' : '' }}>Ready for Adoption</option>
                        <option value="review" {{ request('sort') === 'review' ? 'selected' : '' }}>Under Review</option>
                        <option value="home" {{ request('sort') === 'home' ? 'selected' : '' }}>Found a Home</option>
                    </optgroup>
                </select>
            </form>

            {{-- Grid/List Toggles --}}
            <div class="bg-white border border-stone-200 flex rounded-lg shadow-sm overflow-hidden">
                <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-orange-50 text-orange-600' : 'text-stone-400 hover:bg-stone-50'" class="p-2.5 transition border-r border-stone-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                </button>
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-orange-50 text-orange-600' : 'text-stone-400 hover:bg-stone-50'" class="p-2.5 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </button>
            </div>

            <button @click="showModal = true; modalType = 'add'; modalData = null" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-semibold shadow-sm transition flex items-center gap-2 text-sm whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add New Pet
            </button>
        </div>
    </div>

    {{-- GRID VIEW --}}
    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-stretch">
        @forelse($pets as $pet)
        @php
            $imageUrl = $pet->image 
                ? (Str::startsWith($pet->image, ['http://', 'https://']) ? $pet->image : asset('storage/' . $pet->image))
                : 'https://placehold.co/400x400?text=No+Photo';
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-stone-200 overflow-hidden flex flex-col h-full hover:shadow-md transition">
            <div class="h-60 w-full relative bg-stone-100 shrink-0">
                <img src="{{ $imageUrl }}" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/400x400?text=Broken+Link'">
                
                <div class="absolute top-3 right-3 shadow-sm">
                    @if($pet->status === 'Ready for Adoption')
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-200">Ready</span>
                    @elseif($pet->status === 'Under Review')
                        <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-black uppercase tracking-widest border border-amber-200">Under Review</span>
                    @else
                        <span class="px-3 py-1 bg-white text-stone-800 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">{{ $pet->status }}</span>
                    @endif
                </div>

                <button @click="showModal = true; modalType = 'edit'; modalData = { ...{{ $pet->toJson() }}, image_url: '{{ $imageUrl }}' }" 
                        class="absolute bottom-3 right-3 p-2.5 bg-white/90 backdrop-blur-sm rounded-xl shadow-md text-stone-500 hover:text-orange-600 hover:scale-105 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </button>
            </div>
            
            <div class="p-5 flex flex-col flex-1 bg-white">
                <p class="font-black text-stone-800 text-xl line-clamp-1">{{ $pet->name }}</p>
                <p class="text-[10px] font-bold text-orange-500 uppercase tracking-widest mb-3 mt-0.5">{{ $pet->type }} • {{ $pet->age }}</p>
                <p class="text-sm text-stone-500 line-clamp-2 flex-1">{{ $pet->description ?: 'No description provided.' }}</p>
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-stone-200 border-dashed">
            <span class="text-4xl">📭</span>
            <p class="text-stone-400 font-bold mt-2">No pets found</p>
        </div>
        @endforelse
    </div>

    {{-- LIST VIEW --}}
    <div x-show="viewMode === 'list'" style="display: none;" class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <tbody class="text-sm text-stone-600">
                    @forelse($pets as $pet)
                    @php
                        $imageUrl = $pet->image ? (Str::startsWith($pet->image, ['http://', 'https://']) ? $pet->image : asset('storage/' . $pet->image)) : 'https://placehold.co/100x100?text=Pet';
                    @endphp
                    <tr class="border-b border-stone-100 hover:bg-orange-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <img src="{{ $imageUrl }}" class="w-12 h-12 rounded-lg object-cover border border-stone-200 shadow-sm" onerror="this.src='https://placehold.co/100x100?text=Error'">
                                <div>
                                    <span class="font-bold text-stone-800 block text-base">{{ $pet->name }}</span>
                                    <span class="text-[10px] font-bold text-stone-400 uppercase tracking-widest">{{ $pet->type }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-semibold text-stone-600">{{ $pet->age }}</td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-stone-100 text-stone-600 rounded-full text-[10px] font-black uppercase tracking-widest">{{ $pet->status }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button @click="showModal = true; modalType = 'edit'; modalData = { ...{{ $pet->toJson() }}, image_url: '{{ $imageUrl }}' }" class="text-stone-400 hover:text-orange-600 transition p-2 bg-stone-50 rounded-lg hover:bg-orange-50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-stone-400 font-bold border-dashed border-t border-stone-200">
                            No pets found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection