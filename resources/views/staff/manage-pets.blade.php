@extends('layouts.staff')

@section('content')
<div class="max-w-7xl mx-auto" 
     x-data="{ 
        viewMode: localStorage.getItem('petViewMode') || 'grid',
        showArchiveModal: false,
        archivePetId: null,
        archivePetName: ''
     }" 
     x-init="$watch('viewMode', val => localStorage.setItem('petViewMode', val))">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-stone-800">Pet Listings</h1>
        </div>
        
        <div class="flex items-center gap-4 w-full sm:w-auto">
            <form action="{{ route('staff.pets.index') }}" method="GET" class="w-full sm:w-auto flex items-center gap-3">
                <div class="relative w-full sm:w-72" x-data="{ search: '{{ request('search') }}' }">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" x-model="search" x-ref="searchInput" placeholder="Search pets..." 
                           class="w-full bg-white border border-stone-200 text-stone-600 text-sm rounded-full py-2.5 pl-11 pr-10 focus:ring-orange-500 focus:border-orange-500 shadow-sm outline-none transition"
                           x-on:input.debounce.500ms="$event.target.form.submit()"
                           autofocus
                           onfocus="var val = this.value; this.value = ''; this.value = val;">
                    <button type="button" x-show="search.length > 0" x-cloak
                            @click="search = ''; $nextTick(() => { $refs.searchInput.form.submit() })"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center text-stone-400 hover:text-orange-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

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

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-medium text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

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
                    @elseif($pet->status === 'No Longer Available')
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-200">Archived</span>
                    @else
                        <span class="px-3 py-1 bg-white text-stone-800 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">{{ $pet->status }}</span>
                    @endif
                </div>

                <div class="absolute bottom-3 right-3 flex gap-2">
                    {{-- Edit Button --}}
                    <button @click="showModal = true; modalType = 'edit'; modalData = { ...{{ $pet->toJson() }}, image_url: '{{ $imageUrl }}' }" 
                            class="p-2.5 bg-white/90 backdrop-blur-sm rounded-xl shadow-md text-stone-500 hover:text-orange-600 hover:scale-105 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </button>

                    {{-- Archive Button --}}
                    @if($pet->status !== 'No Longer Available')
                        <button type="button"
                                @click="showArchiveModal = true; archivePetId = {{ $pet->id }}; archivePetName = '{{ addslashes($pet->name) }}'"
                                class="p-2.5 bg-white/90 backdrop-blur-sm rounded-xl shadow-md text-stone-500 hover:text-red-600 hover:scale-105 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8M10 12v4M14 12v4"/>
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="p-5 flex flex-col flex-1 bg-white">
                <p class="font-black text-stone-800 text-xl line-clamp-1">{{ $pet->name }}</p>
                <p class="text-[10px] font-bold text-orange-500 uppercase tracking-widest mb-3 mt-0.5">{{ $pet->type }} • {{ $pet->age }}</p>
                <p class="text-sm text-stone-500 line-clamp-2 flex-1">{{ $pet->description ?: 'No description provided.' }}</p>
                 <a href="{{ route('staff.pets.photos.index', $pet) }}"
                class="mt-3 flex items-center justify-center gap-1.5 w-full py-2 bg-stone-50 hover:bg-orange-50 border border-stone-200 hover:border-orange-300 text-stone-500 hover:text-orange-600 rounded-xl text-xs font-bold transition">
                    🖼️ Manage Photos
                </a>
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
                            @if($pet->status === 'Ready for Adoption')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-200">Ready</span>
                            @elseif($pet->status === 'Under Review')
                                <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-black uppercase tracking-widest border border-amber-200">Under Review</span>
                            @elseif($pet->status === 'No Longer Available')
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-200">Archived</span>
                            @else
                                <span class="px-3 py-1 bg-stone-100 text-stone-600 rounded-full text-[10px] font-black uppercase tracking-widest">{{ $pet->status }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Photos --}}
                                <a href="{{ route('staff.pets.photos.index', $pet) }}"
                                class="text-stone-400 hover:text-orange-600 transition p-2 bg-stone-50 rounded-lg hover:bg-orange-50"
                                title="Manage Photos">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </a>
                                {{-- Edit --}}
                                <button @click="showModal = true; modalType = 'edit'; modalData = { ...{{ $pet->toJson() }}, image_url: '{{ $imageUrl }}' }"
                                        class="text-stone-400 hover:text-orange-600 transition p-2 bg-stone-50 rounded-lg hover:bg-orange-50">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>

                                {{-- Archive --}}
                                @if($pet->status !== 'No Longer Available')
                                    <button type="button"
                                            @click="showArchiveModal = true; archivePetId = {{ $pet->id }}; archivePetName = '{{ addslashes($pet->name) }}'"
                                            class="text-stone-400 hover:text-red-600 transition p-2 bg-stone-50 rounded-lg hover:bg-red-50">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8M10 12v4M14 12v4"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
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

    {{-- Pagination --}}
    @if($pets->hasPages())
        <div class="mt-8 flex justify-center">
            {{ $pets->links() }}
        </div>
    @endif

    {{-- ARCHIVE CONFIRMATION MODAL --}}
    <div x-show="showArchiveModal"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         x-cloak>
        
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-stone-900/60 backdrop-blur-sm" @click="showArchiveModal = false"></div>

        {{-- Modal Box --}}
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md p-8 z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            {{-- Icon --}}
            <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12a2 2 0 002 2h8a2 2 0 002-2L19 8M10 12v4M14 12v4"/>
                </svg>
            </div>

            {{-- Text --}}
            <h3 class="text-xl font-black text-stone-800 text-center mb-2">Archive this pet?</h3>
            <p class="text-stone-500 text-sm text-center mb-1">You are about to archive:</p>
            <p class="text-orange-600 font-black text-center text-lg mb-4" x-text="archivePetName"></p>
            <p class="text-stone-400 text-xs text-center mb-8">This will mark the pet as <span class="font-bold text-stone-600">No Longer Available</span> and remove them from the adoption listings. You can undo this by editing the pet's status.</p>

            {{-- Hidden form — submitted by the confirm button --}}
            <form id="archiveForm" method="POST" :action="`/staff/pets/${archivePetId}/archive`">
                @csrf
                @method('PATCH')
            </form>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <button type="button" @click="showArchiveModal = false"
                    class="flex-1 py-3 bg-stone-100 hover:bg-stone-200 text-stone-600 rounded-2xl font-bold text-sm transition">
                    Cancel
                </button>
                <button type="submit" form="archiveForm"
                    class="flex-1 py-3 bg-red-500 hover:bg-red-600 text-white rounded-2xl font-bold text-sm transition shadow-lg shadow-red-100">
                    Yes, Archive Pet
                </button>
            </div>
        </div>
    </div>

</div>
@endsection