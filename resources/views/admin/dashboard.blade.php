@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-800">System Overview</h1>
        <p class="text-slate-500 mt-1">Monitor high-level metrics and recent system activity.</p>
    </div>

    {{-- Top Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-l-4 border-l-indigo-500">
            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Users</p>
            <p class="text-3xl font-black text-slate-800">{{ $stats['total_users'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-l-4 border-l-sky-500">
            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Active Staff</p>
            <p class="text-3xl font-black text-slate-800">{{ $stats['total_staff'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-l-4 border-l-emerald-500">
            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pets</p>
            <p class="text-3xl font-black text-slate-800">{{ $stats['total_pets'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-l-4 border-l-amber-500">
            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Successful Adoptions</p>
            <p class="text-3xl font-black text-slate-800">{{ $stats['successful_adoptions'] }}</p>
        </div>
    </div>

    {{-- Recent Activity Snapshot --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">Recent System Activity</h2>
            <a href="{{ route('admin.logs') }}" class="text-sm font-bold text-indigo-600 hover:text-indigo-800">View All Logs &rarr;</a>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                @forelse($recentLogs as $log)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-xl shrink-0">
                            {{ $log->icon ?? '📝' }}
                        </div>
                        <div>
                            <p class="text-slate-800 font-medium">{{ $log->title }}</p>
                            <p class="text-xs font-bold text-slate-400 mt-1">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500 text-sm italic">No recent activity found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection