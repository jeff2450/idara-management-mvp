@extends('layouts.app')

@section('title', 'Maendeleo - '.$department->name)

@section('content')
    <h1 class="text-lg font-semibold mb-1">Maendeleo ya {{ $department->name }}</h1>
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
                <p class="text-sm text-indigo-600">{{ $latestReport->period }}</p>
            @else
                <p class="text-sm text-gray-400">Bado hakuna</p>
            @endif
        </div>
    </div>

    <h2 class="font-medium mb-3">Shughuli za Hivi Karibuni</h2>
    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($recentActivity as $log)
            <div class="px-4 py-3 text-sm flex justify-between">
                <span>{{ $log->title }}</span>
                <span class="text-gray-400 text-xs">{{ $log->occurred_at->format('d/m/Y') }}</span>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-gray-500">Hakuna shughuli bado.</div>
        @endforelse
    </div>
@endsection
