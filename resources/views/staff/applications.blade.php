@extends('layouts.staff')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-stone-800">Adoption Applications</h1>
            <p class="text-sm text-stone-500 mt-1">Review and manage incoming requests from potential adopters.</p>
        </div>
        
        {{-- Filtering Options --}}
        <div class="flex items-center gap-3">
            <select class="bg-white border border-stone-200 text-stone-600 text-sm rounded-lg focus:ring-orange-500 focus:border-orange-500 py-2.5 pl-3 pr-10 outline-none cursor-pointer shadow-sm">
                <option value="all">All Statuses</option>
                <option value="pending">Pending Review</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    {{-- Applications Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-200 text-xs text-stone-500 uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Date Applied</th>
                        <th class="px-6 py-4 font-semibold">Applicant</th>
                        <th class="px-6 py-4 font-semibold">Pet Requested</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-stone-600">
                    @forelse($applications as $app)
                    <tr class="border-b border-stone-100 hover:bg-orange-50/50 transition">
                        <td class="px-6 py-4 text-stone-500">
                            {{ $app->created_at->format('M d, Y') }}
                            <div class="text-xs text-stone-400">{{ $app->created_at->format('g:i A') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-stone-800">{{ $app->user->name }}</div>
                            <div class="text-xs text-stone-500">{{ $app->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @php
                                    // SMART IMAGE DETECTOR FOR THE TABLE
                                    $imgUrl = 'https://placehold.co/100x100?text=No+Photo';
                                    if (!empty($app->pet->image)) {
                                        $imgUrl = \Illuminate\Support\Str::startsWith($app->pet->image, ['http://', 'https://']) 
                                            ? $app->pet->image 
                                            : asset('storage/' . $app->pet->image);
                                    }
                                @endphp
                                <img src="{{ $imgUrl }}" 
                                     alt="{{ $app->pet->name }}" 
                                     class="w-8 h-8 rounded border border-stone-200 object-cover"
                                     onerror="this.src='https://placehold.co/100x100?text=Error'">
                                <span class="font-semibold text-orange-600">{{ $app->pet->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($app->status === 'Pending' || $app->status === 'Under Review')
                                <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-semibold border border-amber-200">Needs Review</span>
                            @elseif($app->status === 'Approved' || $app->status === 'Approved for Adoption')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-semibold border border-emerald-200">Approved</span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold border border-red-200">{{ $app->status }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button @click="showModal = true; modalType = 'application'; modalData = @js($app)" 
                                    class="inline-flex items-center gap-1 bg-white border border-stone-200 hover:border-orange-500 hover:text-orange-600 px-4 py-2 rounded-lg font-medium transition shadow-sm">
                                Review
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-stone-400">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-12 h-12 text-stone-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p>No applications found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection