@extends('layouts.staff')

@section('content')
<div class="max-w-7xl mx-auto">
    
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-stone-800">Dashboard Overview</h1>
        {{-- The global modal trigger --}}
        <button @click="showModal = true; modalType = 'add'" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2.5 rounded-lg font-semibold shadow-sm transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New Pet
        </button>
    </div>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-stone-200">
            <p class="text-xs font-bold text-stone-400 tracking-wider uppercase mb-1">Total Pets</p>
            <p class="text-3xl font-black text-stone-700">{{ $stats['total_pets'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-stone-200">
            <p class="text-xs font-bold text-stone-400 tracking-wider uppercase mb-1">Ready for Adoption</p>
            <p class="text-3xl font-black text-emerald-600">{{ $stats['ready_pets'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-stone-200">
            <p class="text-xs font-bold text-stone-400 tracking-wider uppercase mb-1">Pending Applications</p>
            <p class="text-3xl font-black text-orange-500">{{ $stats['pending_apps'] }}</p>
        </div>
    </div>

    {{-- RECENT ACTIVITIES FEED --}}
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-stone-100 bg-white">
            <h2 class="font-bold text-stone-800">Recent Activities</h2>
        </div>
        <div class="p-0">
            <ul class="divide-y divide-stone-100">
                @forelse($recentActivities as $activity)
                <li class="p-6 hover:bg-orange-50/30 transition flex items-start gap-4">
                    {{-- Icon --}}
                    <div class="w-10 h-10 rounded-full bg-stone-50 border border-stone-100 flex items-center justify-center text-lg shrink-0 shadow-inner">
                        {{ $activity->icon }}
                    </div>
                    
                    {{-- Details --}}
                    <div class="flex-1">
                        <p class="text-stone-800 font-bold text-sm">{{ $activity->title }}</p>
                        <p class="text-xs text-stone-400 mt-1">
                            {{ \Carbon\Carbon::parse($activity->date)->diffForHumans() }}
                        </p>
                    </div>
                    
                    {{-- Status Badge --}}
                    <div>
                        @if($activity->status === 'Ready for Adoption' || $activity->status === 'Approved for Adoption')
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-200">{{ $activity->status }}</span>
                        @elseif($activity->status === 'Under Review')
                            <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-[10px] font-black uppercase tracking-widest border border-amber-200">{{ $activity->status }}</span>
                        @else
                            <span class="px-3 py-1 bg-stone-100 text-stone-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-stone-200">{{ $activity->status }}</span>
                        @endif
                    </div>
                </li>
                @empty
                <li class="p-8 text-center text-stone-400">
                    No recent activities found in the system.
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection