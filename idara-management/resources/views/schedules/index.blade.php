@extends('layouts.app')

@section('title', 'Ratiba ya Mwaka - '.$department->name)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold">Ratiba ya Mwaka: {{ $department->name }}</h1>
        <a href="{{ route('departments.progress', $department) }}" class="text-sm text-indigo-600 hover:underline">
            Angalia Maendeleo &rarr;
        </a>
    </div>

    @can('create', [\App\Models\AnnualSchedule::class, $department])
        <form method="POST" action="{{ route('schedules.store', $department) }}"
              class="bg-white border border-gray-200 rounded-lg p-4 mb-6 grid sm:grid-cols-4 gap-3 items-end">
            @csrf
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-700">Kichwa cha Shughuli</label>
                <input name="title" required class="mt-1 w-full rounded-md border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700">Mwaka</label>
                <input name="planned_year" type="number" value="{{ now()->year }}" required
                       class="mt-1 w-full rounded-md border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700">Mwezi (1-12)</label>
                <input name="planned_month" type="number" min="1" max="12" required
                       class="mt-1 w-full rounded-md border-gray-300 text-sm">
            </div>
            <div class="sm:col-span-4">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                    Ongeza kwenye Ratiba
                </button>
            </div>
        </form>
    @endcan

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($schedules as $schedule)
            <div class="flex items-center justify-between px-4 py-3 text-sm">
                <div>
                    <p class="font-medium">{{ $schedule->title }}</p>
                    <p class="text-xs text-gray-400">{{ $schedule->planned_month }}/{{ $schedule->planned_year }}</p>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full
                    {{ $schedule->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $schedule->status }}
                </span>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-gray-500">Hakuna ratiba bado.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $schedules->links() }}</div>
@endsection
