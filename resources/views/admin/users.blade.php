@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-black text-slate-800">Manage Users</h1>
        <p class="text-slate-500 mt-1">View and manage roles for all registered accounts.</p>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="mb-6 px-5 py-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl font-medium text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 px-5 py-4 bg-red-50 border border-red-200 text-red-800 rounded-xl font-medium text-sm">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    {{-- Role Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-l-4 border-l-indigo-500">
            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Admins</p>
            <p class="text-3xl font-black text-slate-800">{{ $users->where('role', 'admin')->count() }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-l-4 border-l-sky-500">
            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Staff</p>
            <p class="text-3xl font-black text-slate-800">{{ $users->where('role', 'staff')->count() }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 border-l-4 border-l-emerald-500">
            <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Adopters</p>
            <p class="text-3xl font-black text-slate-800">{{ $users->where('role', 'adopter')->count() }}</p>
        </div>
    </div>

    {{-- Search Bar --}}
    <div class="mb-4">
        <input type="text" id="userSearch" placeholder="Search by name or email..."
            class="w-full sm:w-80 bg-white border border-slate-200 text-slate-600 text-sm rounded-xl py-2.5 pl-4 pr-4 focus:ring-indigo-500 focus:border-indigo-500 outline-none shadow-sm">
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap" id="usersTable">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs text-slate-500 uppercase tracking-wider font-bold">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Joined</th>
                        <th class="px-6 py-4">Current Role</th>
                        <th class="px-6 py-4">Verification</th>
                        <th class="px-6 py-4 text-right">Change Role</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-600">
                    @forelse($users as $user)
                    <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition user-row">

                        {{-- Avatar + Name + Email --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white text-sm shrink-0
                                    @if($user->role === 'admin') bg-indigo-500
                                    @elseif($user->role === 'staff') bg-sky-500
                                    @else bg-emerald-500 @endif">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 user-name">{{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="ml-1 text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-black uppercase tracking-widest">You</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-slate-400 user-email">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Joined Date --}}
                        <td class="px-6 py-4">
                            <span class="text-slate-600">{{ $user->created_at->format('M d, Y') }}</span>
                            <span class="block text-xs text-slate-400">{{ $user->created_at->diffForHumans() }}</span>
                        </td>

                        {{-- Role Badge --}}
                        <td class="px-6 py-4">
                            @if($user->role === 'admin')
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs font-black uppercase tracking-widest border border-indigo-200">Admin</span>
                            @elseif($user->role === 'staff')
                                <span class="px-3 py-1 bg-sky-100 text-sky-800 rounded-full text-xs font-black uppercase tracking-widest border border-sky-200">Staff</span>
                            @else
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-black uppercase tracking-widest border border-emerald-200">Adopter</span>
                            @endif
                        </td>

                        {{-- ADD THIS RIGHT HERE --}}
                        <td class="px-6 py-4">
                            @if($user->role === 'adopter')
                                @if($user->verification_status === 'verified')
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-black border border-emerald-200">✅ Verified</span>
                                @elseif($user->verification_status === 'pending')
                                    <div class="space-y-2">
                                        <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-black border border-amber-200">⏳ Pending</span>
                                        @if($user->id_document)
                                            <div class="flex items-center gap-2 mt-1">
                                                <a href="{{ asset('storage/' . $user->id_document) }}" target="_blank"
                                                class="text-xs font-bold text-orange-600 hover:text-orange-800">View ID →</a>
                                                <form method="POST" action="{{ route('admin.users.verify', $user) }}" class="inline flex gap-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button name="action" value="verify"
                                                        class="px-2 py-1 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg transition">✅</button>
                                                    <button name="action" value="reject"
                                                        class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition">❌</button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($user->verification_status === 'rejected')
                                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-black border border-red-200">❌ Rejected</span>
                                @else
                                    <span class="px-3 py-1 bg-stone-100 text-stone-500 rounded-full text-xs font-black border border-stone-200">○ Unverified</span>
                                @endif
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>

                        {{-- Role Change Form --}}
                        <td class="px-6 py-4 text-right">
                            @if($user->id === auth()->id())
                                <span class="text-xs text-slate-400 italic">Cannot change own role</span>
                            @else
                                <form method="POST" action="{{ route('admin.users.role', $user) }}"
                                    onsubmit="return confirm('Change {{ $user->name }}\'s role to ' + this.role.value + '?')">
                                    @csrf
                                    @method('PATCH')
                                    <div class="flex items-center justify-end gap-2">
                                        <select name="role"
                                            class="bg-white border border-slate-200 text-slate-600 text-xs rounded-lg py-2 pl-3 pr-8 outline-none cursor-pointer shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="adopter" {{ $user->role === 'adopter' ? 'selected' : '' }}>Adopter</option>
                                            <option value="staff"   {{ $user->role === 'staff'   ? 'selected' : '' }}>Staff</option>
                                            <option value="admin"   {{ $user->role === 'admin'   ? 'selected' : '' }}>Admin</option>
                                        </select>
                                        <button type="submit"
                                            class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition shadow-sm">
                                            Save
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <span class="text-4xl block mb-3">👥</span>
                            <p class="text-slate-500 font-medium">No users found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Live Search Script --}}
<script>
    document.getElementById('userSearch').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.user-row').forEach(row => {
            const name  = row.querySelector('.user-name').textContent.toLowerCase();
            const email = row.querySelector('.user-email').textContent.toLowerCase();
            row.style.display = (name.includes(query) || email.includes(query)) ? '' : 'none';
        });
    });
</script>
@endsection