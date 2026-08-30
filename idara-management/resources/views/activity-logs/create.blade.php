@extends('layouts.app')

@section('title', 'Rekodi Shughuli')

@section('content')
    <h1 class="text-lg font-semibold mb-6">Rekodi Shughuli - {{ $department->name }}</h1>

    <form method="POST" action="{{ route('departments.activity-logs.store', $department) }}"
          class="bg-white border border-gray-200 rounded-lg p-6 space-y-4 max-w-lg">
        @csrf

        <div>
            <label for="schedule_id" class="block text-sm font-medium text-gray-700">Ratiba Husika (hiari)</label>
            <select id="schedule_id" name="schedule_id" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                <option value="">— Shughuli ya dharura, siyo kwenye ratiba —</option>
                @foreach ($schedules as $schedule)
                    <option value="{{ $schedule->id }}">{{ $schedule->monthName() }} - {{ $schedule->title }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="occurred_at" class="block text-sm font-medium text-gray-700">Tarehe Ilifanyika</label>
            <input id="occurred_at" type="date" name="occurred_at" value="{{ old('occurred_at', now()->format('Y-m-d')) }}" required
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Maelezo ya Shughuli</label>
            <textarea id="description" name="description" rows="4" required
                      class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">Hifadhi</button>
            <a href="{{ route('departments.activity-logs.index', $department) }}" class="text-sm text-gray-500 hover:underline">Ghairi</a>
        </div>
    </form>
@endsection
