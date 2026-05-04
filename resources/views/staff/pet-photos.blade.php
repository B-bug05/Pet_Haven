@extends('layouts.staff')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('staff.pets.index') }}" class="text-stone-400 hover:text-orange-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-stone-800">Photo Gallery — {{ $pet->name }}</h1>
            <p class="text-sm text-stone-500 mt-0.5">{{ $pet->type }} · {{ $pet->age }} · {{ $pet->status }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-medium text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Upload Form --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 mb-6">
        <h2 class="text-sm font-bold text-stone-700 uppercase tracking-widest mb-4">Upload New Photos</h2>
        <form action="{{ route('staff.pets.photos.store', $pet) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="border-2 border-dashed border-stone-200 rounded-xl p-8 text-center hover:border-orange-400 hover:bg-orange-50/30 transition cursor-pointer"
                 onclick="document.getElementById('photoInput').click()">
                <span class="text-4xl block mb-3">🖼️</span>
                <p class="text-stone-500 font-medium mb-1">Click to select photos</p>
                <p class="text-xs text-stone-400">JPG, PNG, WEBP · Max 2MB each · Multiple allowed</p>
                <input type="file" id="photoInput" name="photos[]" multiple
                       accept="image/jpeg,image/png,image/jpg,image/webp"
                       class="hidden"
                       onchange="updateFileLabel(this)">
                <p id="fileLabel" class="mt-3 text-xs text-orange-600 font-bold"></p>
            </div>
            <button type="submit"
                    class="mt-4 w-full py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-bold transition shadow-md shadow-orange-100">
                Upload Photos
            </button>
        </form>
    </div>

    {{-- Gallery Grid --}}
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6">
        <h2 class="text-sm font-bold text-stone-700 uppercase tracking-widest mb-4">
            Gallery ({{ $pet->photos->count() }} photo{{ $pet->photos->count() !== 1 ? 's' : '' }})
        </h2>

        @if($pet->photos->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @foreach($pet->photos as $photo)
                    <div class="relative group aspect-square rounded-xl overflow-hidden border border-stone-200 shadow-sm">
                        <img src="{{ asset('storage/' . $photo->image) }}"
                             class="w-full h-full object-cover"
                             onerror="this.src='https://placehold.co/200x200?text=Error'">
                        <div class="absolute inset-0 bg-stone-900/60 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                            <form method="POST" action="{{ route('staff.pets.photos.destroy', [$pet, $photo]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Remove this photo?')"
                                        class="bg-red-500 hover:bg-red-600 text-white rounded-lg px-4 py-2 text-xs font-bold transition">
                                    🗑 Remove
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-stone-400">
                <span class="text-4xl block mb-3">📷</span>
                <p class="font-medium">No gallery photos yet.</p>
                <p class="text-sm mt-1">Upload photos above to build this pet's gallery.</p>
            </div>
        @endif
    </div>
</div>

<script>
    function updateFileLabel(input) {
        const label = document.getElementById('fileLabel');
        if (input.files.length > 0) {
            label.textContent = input.files.length + ' file(s) selected';
        } else {
            label.textContent = '';
        }
    }
</script>
@endsection