@extends('layouts.app')

@section('title', $department->name)

@section('header_title')
    <div class="flex items-center gap-2">
        <a href="{{ route('departments.index') }}" class="text-slate-400 hover:text-slate-600 text-xs font-semibold">&larr; Idara</a>
        <span class="text-slate-300">/</span>
        <h1 class="text-base font-extrabold text-slate-900 tracking-tight">{{ strtoupper($department->name) }}</h1>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Department Header Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-sm">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16M4 18h16M9 18V6l6-4v16M9 10h2M9 14h2M13 10h2M13 14h2"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $department->name }}</h2>
                <p class="text-xs text-slate-500 mt-1">{{ $department->description }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @can('update', $department)
                <a href="{{ route('departments.edit', $department) }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-xl transition">
                    Hariri Idara
                </a>
            @endcan
        </div>
    </div>

    <!-- Sub-navigation Pills -->
    <nav class="flex flex-wrap gap-2 text-xs font-bold">
        <a href="{{ route('departments.sms.index', $department) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:text-emerald-700 transition shadow-sm flex items-center gap-1.5">
            <span>💬</span><span>SMS</span>
        </a>
        <a href="{{ route('departments.letters.index', $department) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:text-emerald-700 transition shadow-sm flex items-center gap-1.5">
            <span>📄</span><span>Barua</span>
        </a>
        @can('viewAny', [\App\Models\DepartmentTransaction::class, $department])
            <a href="{{ route('departments.transactions.index', $department) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:text-emerald-700 transition shadow-sm flex items-center gap-1.5">
                <span>💳</span><span>Miamala</span>
            </a>
        @endcan
        <a href="{{ route('schedules.index', $department) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:text-emerald-700 transition shadow-sm flex items-center gap-1.5">
            <span>📅</span><span>Ratiba ya Mwaka</span>
        </a>
        <a href="{{ route('departments.progress', $department) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:text-emerald-700 transition shadow-sm flex items-center gap-1.5">
            <span>📈</span><span>Maendeleo</span>
        </a>
        <a href="{{ route('departments.reports.index', $department) }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:text-emerald-700 transition shadow-sm flex items-center gap-1.5">
            <span>📊</span><span>Ripoti</span>
        </a>
        @can('manageMembers', $department)
            <a href="{{ route('departments.members.import.form', $department) }}" class="px-4 py-2 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 hover:bg-emerald-100 transition shadow-sm flex items-center gap-1.5 ml-auto">
                <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="12" y1="18" x2="12" y2="12"/>
                    <line x1="9" y1="15" x2="15" y2="15"/>
                </svg>
                <span>Ingiza kwa Excel/CSV</span>
            </a>
        @endcan
    </nav>

    <!-- Grid of Leaders & Members -->
    <div class="grid md:grid-cols-2 gap-6">

        <!-- Leaders Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-tight flex items-center gap-2">
                    <span>Viongozi wa Idara</span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                        {{ $department->leaders->count() }}
                    </span>
                </h3>
            </div>
            <ul class="divide-y divide-slate-100 text-xs">
                @forelse ($department->leaders as $leader)
                    <li class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-xs">
                                {{ strtoupper(substr($leader->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $leader->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $leader->email }}</p>
                            </div>
                        </div>
                        @can('manageMembers', $department)
                            <form method="POST" action="{{ route('departments.members.destroy', [$department, $leader]) }}"
                                  onsubmit="return confirm('Ondoa kiongozi huyu kwenye idara?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-lg text-[11px] font-bold transition">Ondoa</button>
                            </form>
                        @endcan
                    </li>
                @empty
                    <li class="py-6 text-center text-slate-400">Hakuna kiongozi bado.</li>
                @endforelse
            </ul>
        </div>

        <!-- Members Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-tight flex items-center gap-2">
                    <span>Wanachama</span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold border border-emerald-200">
                        {{ $department->members->count() }}
                    </span>
                </h3>
            </div>
            <ul class="divide-y divide-slate-100 text-xs max-h-80 overflow-y-auto">
                @forelse ($department->members as $member)
                    <li class="py-2.5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-[10px]">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $member->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $member->email }}</p>
                            </div>
                        </div>
                        @can('manageMembers', $department)
                            <form method="POST" action="{{ route('departments.members.destroy', [$department, $member]) }}"
                                  onsubmit="return confirm('Ondoa mwanachama huyu kwenye idara?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-2.5 py-1 rounded-lg text-[11px] font-bold transition">Ondoa</button>
                            </form>
                        @endcan
                    </li>
                @empty
                    <li class="py-6 text-center text-slate-400">Hakuna mwanachama bado.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Add Member / Leader Card Form -->
    @can('manageMembers', $department)
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm max-w-xl" x-data="{ mode: 'existing' }">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-tight mb-4">Ongeza Mtu kwenye Idara</h3>

            <form method="POST" action="{{ route('departments.members.store', $department) }}" class="space-y-4">
                @csrf

                <div class="flex gap-4 text-xs font-bold">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="mode" value="existing" x-model="mode" class="text-emerald-600 focus:ring-emerald-500"> Mtumiaji aliyepo
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="mode" value="new" x-model="mode" class="text-emerald-600 focus:ring-emerald-500"> Mtumiaji mpya
                    </label>
                </div>

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700">Barua pepe (Email)</label>
                    <input id="email" name="email" type="email" required
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:outline-none focus:border-brand-500 bg-slate-50">
                </div>

                <template x-if="mode === 'new'">
                    <div class="space-y-3">
                        <div>
                            <label for="name" class="block text-xs font-bold text-slate-700">Jina Kamili</label>
                            <input id="name" name="name" class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:outline-none focus:border-brand-500 bg-slate-50">
                        </div>
                        <div>
                            <label for="phone" class="block text-xs font-bold text-slate-700">Namba ya Simu (hiari)</label>
                            <input id="phone" name="phone" class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:outline-none focus:border-brand-500 bg-slate-50">
                        </div>
                        <div>
                            <label for="password" class="block text-xs font-bold text-slate-700">Nenosiri la Awali</label>
                            <input id="password" name="password" type="password" class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:outline-none focus:border-brand-500 bg-slate-50">
                        </div>
                    </div>
                </template>

                <div>
                    <label for="role" class="block text-xs font-bold text-slate-700">Nafasi kwenye Idara</label>
                    <select id="role" name="role" class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:outline-none focus:border-brand-500 bg-slate-50">
                        <option value="member">Mwanachama</option>
                        @if (auth()->user()->isAdmin())
                            <option value="leader">Kiongozi</option>
                        @endif
                    </select>
                    @unless (auth()->user()->isAdmin())
                        <p class="text-[10px] text-slate-400 mt-1">Uteuzi wa Kiongozi ni Admin pekee.</p>
                    @endunless
                </div>

                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm transition">
                    Ongeza Mwanachama
                </button>
            </form>
        </div>
    @endcan

</div>
@endsection
