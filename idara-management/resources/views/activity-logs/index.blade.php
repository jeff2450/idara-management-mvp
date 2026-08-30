@extends('layouts.app')

@section('title', 'Shughuli Zilizofanyika - '.$department->name)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold">Shughuli Zilizofanyika - {{ $department->name }}</h1>
            <a href="{{ route('departments.show', $department) }}" class="text-xs text-gray-400 hover:underline">&larr; Rudi kwenye Idara</a>
        </div>
        @can('create', [\App\Models\ActivityLog::class, $department])
            <a href="{{ route('departments.activity-logs.create', $department) }}"
               class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                + Rekodi Shughuli
            </a>
        @endcan
    </div>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($logs as $log)
            <div class="px-4 py-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="font-medium">{{ $log->occurred_at->translatedFormat('d M Y') }}</span>
                    @if ($log->schedule)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700">{{ $log->schedule->title }}</span>
                    @endif
                </div>
                <p class="text-gray-600 mt-1">{{ $log->description }}</p>
                <p class="text-xs text-gray-400 mt-1">Imerekodiwa na {{ $log->recorder->name }}</p>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-gray-500">Bado hakuna shughuli iliyorekodiwa.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
@endsection
