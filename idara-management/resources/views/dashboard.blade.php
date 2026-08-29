@extends('layouts.app')

@section('title', 'Dashibodi')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold">Karibu, {{ auth()->user()->name }}</h1>
            <p class="text-sm text-gray-500">
                @if (auth()->user()->isAdmin())
                    Unaona idara zote za mfumo (Admin).
                @else
                    Idara ulizomo:
                @endif
            </p>
        </div>

        @can('create', \App\Models\Department::class)
            <a href="{{ route('departments.create') }}"
               class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                + Idara Mpya
            </a>
        @endcan
    </div>

    @if ($departments->isEmpty())
        <div class="text-center text-sm text-gray-500 border border-dashed border-gray-300 rounded-lg py-12">
            Bado hujawekwa kwenye idara yoyote. Wasiliana na Admin.
        </div>
    @else
        <div class="grid sm:grid-cols-2 gap-4">
            @foreach ($departments as $department)
                <a href="{{ route('departments.show', $department) }}"
                   class="block bg-white border border-gray-200 rounded-lg p-4 hover:border-indigo-400 transition">
                    <div class="flex items-center justify-between">
                        <h2 class="font-medium">{{ $department->name }}</h2>
                        @if (!auth()->user()->isAdmin())
                            <span class="text-xs px-2 py-0.5 rounded-full
                                {{ $department->pivot->role === 'leader' ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $department->pivot->role === 'leader' ? 'Kiongozi' : 'Mwanachama' }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $department->description }}</p>
                    <p class="text-xs text-gray-400 mt-3">{{ $department->users_count }} watu</p>
                </a>
            @endforeach
        </div>
    @endif
@endsection
