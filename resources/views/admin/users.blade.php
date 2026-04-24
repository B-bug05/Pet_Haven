@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-slate-800">Manage Users</h1>
            <p class="text-slate-500 mt-1">View and adjust account access levels for all PetHaven users.</p>
        </div>
    </div>

    {{-- Session Alerts --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl font-bold flex items-center gap-2">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 text-red-700 border border-red-200 rounded-xl font-bold flex items-center gap-2">
            <span>⚠️</span> {{ session('error') }}
        </div>
    @endif

    {{-- Users Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs text-slate-500 uppercase tracking-wider font-bold">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Contact Info</th>
                        <th class="px-6 py-4">Joined Date</th>
                        <th class="px-6 py-4 text-right">System Role</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-600">
                    @foreach($users as $user)
                    <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition">
                        
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full text-white flex items-center justify-center font-bold text-lg
                                    {{ $user->role === 'admin' ? 'bg-indigo-600' : ($user->role === 'staff' ? 'bg-sky-500' : 'bg-slate-300') }}">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <span class="font-bold text-slate-800 text-base">{{ $user->name }}</span>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-slate-800 font-medium">{{ $user->email }}</div>
                        </td>

                        <td class="px-6 py-4 text-slate-500">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>

                        <td class="px-6 py-4 text-right">
                            @if($user->id === auth()->id())
                                {{-- Cannot change own role --}}
                                <span class="inline-block px-4 py-2 bg-indigo-100 text-indigo-800 rounded-lg font-black text-xs uppercase tracking-widest border border-indigo-200">
                                    Admin (You)
                                </span>
                            @else
                                {{-- Smart Auto-Submit Dropdown for Role changes --}}
                                <form method="POST" action="{{ route('admin.users.role', $user->id) }}" class="inline-block">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" onchange="this.form.submit()" class="bg-white border border-slate-300 text-slate-700 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8 outline-none cursor-pointer shadow-sm font-bold">
                                        <option value="adopter" {{ $user->role === 'adopter' ? 'selected' : '' }}>Adopter</option>
                                        <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                </form>
                            @endif
                        </td>
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection