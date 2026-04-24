@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-800">Audit Logs</h1>
        <p class="text-slate-500 mt-1">A complete historical record of all system activities and changes.</p>
    </div>

    {{-- System Logs Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs text-slate-500 uppercase tracking-wider font-bold">
                        <th class="px-6 py-4">Timestamp</th>
                        <th class="px-6 py-4">Module</th>
                        <th class="px-6 py-4 w-full">Activity / Action</th>
                        <th class="px-6 py-4 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-600">
                    @forelse($logs as $log)
                    <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition">
                        
                        {{-- Date & Time --}}
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-700 block">{{ $log->created_at->format('M d, Y') }}</span>
                            <span class="text-xs text-slate-400">{{ $log->created_at->format('g:i:s A') }}</span>
                        </td>

                        {{-- Module / Type --}}
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-black uppercase tracking-widest border border-slate-200">
                                {{ $log->type }}
                            </span>
                        </td>

                        {{-- The Action --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">{{ $log->icon ?? '📝' }}</span>
                                <span class="text-slate-800 font-medium whitespace-normal line-clamp-2">
                                    {{ $log->title }}
                                </span>
                            </div>
                        </td>

                        {{-- Resulting Status --}}
                        <td class="px-6 py-4 text-right">
                            <span class="text-xs font-bold text-slate-500 bg-white border border-slate-200 px-2 py-1 rounded shadow-sm">
                                {{ $log->status ?? 'System' }}
                            </span>
                        </td>
                        
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center">
                            <span class="text-4xl block mb-3">📭</span>
                            <p class="text-slate-500 font-medium">No system logs found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination Controls --}}
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection