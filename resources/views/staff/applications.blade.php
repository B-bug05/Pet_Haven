@extends('layouts.staff')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-stone-800">Adoption Applications</h1>
            <p class="text-sm text-stone-500 mt-1">Review and manage incoming requests from potential adopters.</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('staff.applications.index') }}" method="GET">
                <select name="status" onchange="this.form.submit()"
                    class="bg-white border border-stone-200 text-stone-600 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 py-2.5 pl-3 pr-10 outline-none cursor-pointer shadow-sm">
                    <option value="" {{ !$status ? 'selected' : '' }}>All Statuses</option>
                    <option value="Under Review"          {{ $status === 'Under Review'          ? 'selected' : '' }}>Pending Review</option>
                    <option value="Approved for Adoption" {{ $status === 'Approved for Adoption' ? 'selected' : '' }}>Approved</option>
                    <option value="Application Declined"  {{ $status === 'Application Declined'  ? 'selected' : '' }}>Declined</option>
                </select>
            </form>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-medium text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    {{-- Group applications by pet --}}
    @php
        $grouped = $applications->groupBy('pet_id');
    @endphp

    @if($grouped->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-12 text-center">
            <svg class="w-12 h-12 text-stone-300 mb-3 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-stone-400 font-medium">No applications found.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($grouped as $petId => $apps)
                @php
                    $pet = $apps->first()->pet;
                    $pendingCount  = $apps->where('status', 'Under Review')->count();
                    $approvedCount = $apps->where('status', 'Approved for Adoption')->count();
                    $declinedCount = $apps->where('status', 'Application Declined')->count();
                    $imgUrl = !empty($pet->image)
                        ? (\Illuminate\Support\Str::startsWith($pet->image, ['http://', 'https://'])
                            ? $pet->image
                            : asset('storage/' . $pet->image))
                        : 'https://placehold.co/100x100?text=No+Photo';
                @endphp

                {{-- Pet Group Card --}}
                <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden"
                     x-data="{ open: {{ $pendingCount > 0 ? 'true' : 'false' }} }">

                    {{-- Pet Group Header (click to collapse) --}}
                    <button type="button" @click="open = !open"
                        class="w-full flex items-center gap-4 px-6 py-4 bg-stone-50 hover:bg-orange-50/50 border-b border-stone-200 transition text-left">

                        {{-- Pet Thumbnail --}}
                        <img src="{{ $imgUrl }}" alt="{{ $pet->name }}"
                            class="w-12 h-12 rounded-lg object-cover border border-stone-200 shrink-0"
                            onerror="this.src='https://placehold.co/100x100?text=Error'">

                        {{-- Pet Name + Status --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-stone-800 text-base">{{ $pet->name }}</span>
                                <span class="text-xs text-stone-400 font-medium">{{ $pet->type }} · {{ $pet->age }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                @if($pendingCount > 0)
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-[11px] font-bold border border-amber-200">
                                        {{ $pendingCount }} Pending
                                    </span>
                                @endif
                                @if($approvedCount > 0)
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[11px] font-bold border border-emerald-200">
                                        {{ $approvedCount }} Approved
                                    </span>
                                @endif
                                @if($declinedCount > 0)
                                    <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-[11px] font-bold border border-red-200">
                                        {{ $declinedCount }} Declined
                                    </span>
                                @endif
                                <span class="text-xs text-stone-400">
                                    · {{ $apps->count() }} total application{{ $apps->count() > 1 ? 's' : '' }}
                                </span>
                            </div>
                        </div>

                        {{-- Chevron --}}
                        <svg class="w-5 h-5 text-stone-400 shrink-0 transition-transform duration-200"
                             :class="{ 'rotate-180': open }"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    {{-- Applications Table for this Pet --}}
                    <div x-show="open" x-collapse>
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="text-xs text-stone-400 uppercase tracking-wider border-b border-stone-100 bg-white">
                                    <th class="px-6 py-3 font-semibold">Applicant</th>
                                    <th class="px-6 py-3 font-semibold">Contact</th>
                                    <th class="px-6 py-3 font-semibold">Date Applied</th>
                                    <th class="px-6 py-3 font-semibold">Status</th>
                                    <th class="px-6 py-3 font-semibold text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm text-stone-600">
                                @foreach($apps as $app)
                                <tr class="border-b border-stone-100 hover:bg-orange-50/50 transition last:border-0">

                                    {{-- Applicant --}}
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($app->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-stone-800">{{ $app->user->name }}</div>
                                                <div class="text-xs text-stone-400">{{ $app->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Contact --}}
                                    <td class="px-6 py-4">
                                        <div class="text-stone-600">{{ $app->contact_number }}</div>
                                        <div class="text-xs text-stone-400 max-w-[180px] truncate">{{ $app->adopter_address }}</div>
                                    </td>

                                    {{-- Date --}}
                                    <td class="px-6 py-4 text-stone-500">
                                        {{ $app->created_at->format('M d, Y') }}
                                        <div class="text-xs text-stone-400">{{ $app->created_at->format('g:i A') }}</div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4">
                                        @if($app->status === 'Under Review')
                                            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-semibold border border-amber-200">Needs Review</span>
                                        @elseif($app->status === 'Approved for Adoption')
                                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-semibold border border-emerald-200">Approved</span>
                                        @else
                                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold border border-red-200">Declined</span>
                                        @endif
                                    </td>

                                    {{-- Action --}}
                                    <td class="px-6 py-4 text-right">
                                        @if($app->status === 'Under Review' || $app->status === 'Approved for Adoption')
                                            <button @click="showModal = true; modalType = 'application'; modalData = @js($app)"
                                                class="inline-flex items-center gap-1 bg-white border border-stone-200 hover:border-orange-500 hover:text-orange-600 px-4 py-2 rounded-lg font-medium transition shadow-sm text-sm">
                                                {{ $app->status === 'Approved for Adoption' ? 'View' : 'Review' }}
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                        @else
                                            <span class="text-xs text-stone-400 italic">No action needed</span>
                                        @endif
                                    </td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection