@extends('layouts.app')

@section('title', 'Idara (Departments)')

@section('header_title')
    <div>
        <h1 class="text-base font-extrabold text-slate-900 tracking-tight">IDARA (DEPARTMENTS)</h1>
        <p class="text-xs text-slate-500 mt-0.5">Orodha ya idara zote za kanisa na usimamizi wake.</p>
    </div>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-tight">Orodha ya Idara</h2>

        @can('create', \App\Models\Department::class)
            <a href="{{ route('departments.create') }}"
               class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl shadow-sm transition flex items-center gap-1.5">
                <span class="text-sm">+</span>
                <span>Idara Mpya</span>
            </a>
        @endcan
    </div>

    <div class="bg-white border border-slate-200/80 rounded-2xl divide-y divide-slate-100 shadow-sm overflow-hidden">
        @forelse ($departments as $department)
            <div class="flex items-center justify-between px-5 py-4 hover:bg-slate-50/80 transition group">
                <a href="{{ route('departments.show', $department) }}" class="flex items-center gap-3.5 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 flex-shrink-0 group-hover:scale-105 transition">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16M4 18h16M9 18V6l6-4v16M9 10h2M9 14h2M13 10h2M13 14h2"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="font-bold text-slate-800 text-sm group-hover:text-emerald-700 transition truncate">{{ $department->name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $department->leaders_count }} kiongozi &middot; {{ $department->members_count }} wanachama
                        </p>
                    </div>
                </a>

                @can('update', $department)
                    <div class="flex items-center gap-3 text-xs font-semibold">
                        <a href="{{ route('departments.edit', $department) }}" class="text-slate-500 hover:text-emerald-700 bg-slate-100 hover:bg-emerald-50 px-3 py-1.5 rounded-lg transition">Hariri</a>
                        <form method="POST" action="{{ route('departments.destroy', $department) }}"
                              onsubmit="return confirm('Una uhakika unataka kufuta idara hii?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-3 py-1.5 rounded-lg transition">Futa</button>
                        </form>
                    </div>
                @endcan
            </div>
        @empty
            <div class="px-5 py-12 text-center text-sm text-slate-400">Hakuna idara bado.</div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $departments->links() }}
    </div>
</div>
@endsection
