@extends('layouts.app')

@section('title', 'Barua - '.$department->name)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold">Barua - {{ $department->name }}</h1>
            <a href="{{ route('departments.show', $department) }}" class="text-xs text-gray-400 hover:underline">&larr; Rudi kwenye Idara</a>
        </div>
        @can('create', [\App\Models\Letter::class, $department])
            <a href="{{ route('departments.letters.create', $department) }}"
               class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                + Zalisha Barua
            </a>
        @endcan
    </div>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($letters as $letter)
            <div class="flex items-center justify-between px-4 py-3 text-sm">
                <div>
                    <p class="font-medium">{{ $letter->template->name }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $letter->recipient_name ?? 'Bila jina maalum' }} &middot;
                        na {{ $letter->generator->name }} &middot;
                        {{ $letter->created_at->translatedFormat('d M Y') }}
                    </p>
                </div>
                <a href="{{ route('departments.letters.download', [$department, $letter]) }}"
                   class="text-indigo-600 hover:underline">Pakua PDF</a>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-gray-500">Bado hakuna barua iliyozalishwa.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $letters->links() }}</div>
@endsection
