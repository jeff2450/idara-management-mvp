@extends('layouts.app')

@section('title', 'Maendeleo - '.$department->name)

@section('content')
    <div class="flex items-center justify-between mb-2">
        <h1 class="text-lg font-semibold">Maendeleo ya {{ $department->name }}</h1>
        <a href="{{ route('departments.show', $department) }}" class="text-xs text-gray-400 hover:underline">&larr; Rudi kwenye Idara</a>
    </div>
    <p class="text-sm text-gray-500 mb-6">Mwaka {{ $year }}</p>

    <div class="grid sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <p class="text-xs text-gray-500">Ratiba Iliyotekelezwa</p>
            <p class="text-2xl font-semibold text-indigo-700">
                {{ $completionRate !== null ? $completionRate.'%' : '—' }}
            </p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <p class="text-xs text-gray-500">Vipengele vya Ratiba</p>
            <p class="text-2xl font-semibold">{{ $schedules->count() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <p class="text-xs text-gray-500">Ripoti ya Mwisho</p>
            @if ($latestReport)
                <a href="{{ route('departments.reports.download', [$department, $latestReport]) }}" class="text-sm text-indigo-600 hover:underline font-medium">
                    {{ $latestReport->period }} (Pakua PDF)
                </a>
            @else
                <p class="text-sm text-gray-400">Bado hakuna</p>
            @endif
        </div>
    </div>

    @can('create', [\App\Models\ActivityLog::class, $department])
        <div class="bg-white border border-gray-200 rounded-lg p-4 mb-6">
            <h2 class="font-medium text-sm mb-3">Rekodi Shughuli Iliyofanyika</h2>
            <form method="POST" action="{{ route('activity-logs.store', $department) }}" class="space-y-3">
                @csrf
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Kichwa cha Shughuli</label>
                        <input name="title" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="Mfano: Semina ya Walimu">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Tarehe Ilifanyika</label>
                        <input name="occurred_at" type="date" value="{{ now()->format('Y-m-d') }}" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Ratiba Husika (hiari - itaweka kipengele 'completed')</label>
                        <select name="annual_schedule_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                            <option value="">— Shughuli huru (isiyo kwenye ratiba) —</option>
                            @foreach ($schedules as $sched)
                                <option value="{{ $sched->id }}">{{ $sched->planned_month }}/{{ $sched->planned_year }} - {{ $sched->title }} ({{ $sched->status }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700">Maelezo (hiari)</label>
                        <input name="description" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm" placeholder="Maelezo mafupi ya utekelezaji...">
                    </div>
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                    Hifadhi Shughuli
                </button>
            </form>
        </div>
    @endcan

    <h2 class="font-medium mb-3">Shughuli za Hivi Karibuni</h2>
    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($recentActivity as $log)
            <div class="px-4 py-3 text-sm flex justify-between">
                <div>
                    <span class="font-medium">{{ $log->title }}</span>
                    @if ($log->description)
                        <p class="text-xs text-gray-500">{{ $log->description }}</p>
                    @endif
                </div>
                <span class="text-gray-400 text-xs">{{ $log->occurred_at->format('d/m/Y') }}</span>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-gray-500">Hakuna shughuli bado.</div>
        @endforelse
    </div>
@endsection
