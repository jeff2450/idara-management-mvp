@extends('layouts.app')

@section('title', 'Idara')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold">Idara</h1>

        @can('create', \App\Models\Department::class)
            <a href="{{ route('departments.create') }}"
               class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                + Idara Mpya
            </a>
        @endcan
    </div>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($departments as $department)
            <div class="flex items-center justify-between px-4 py-3">
                <a href="{{ route('departments.show', $department) }}" class="hover:text-indigo-700">
                    <span class="font-medium">{{ $department->name }}</span>
                    <span class="text-xs text-gray-400 ml-2">
                        {{ $department->leaders_count }} kiongozi &middot; {{ $department->members_count }} wanachama
                    </span>
                </a>

                @can('update', $department)
                    <div class="flex items-center gap-3 text-sm">
                        <a href="{{ route('departments.edit', $department) }}" class="text-gray-500 hover:text-indigo-700">Hariri</a>
                        <form method="POST" action="{{ route('departments.destroy', $department) }}"
                              onsubmit="return confirm('Una uhakika unataka kufuta idara hii?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline">Futa</button>
                        </form>
                    </div>
                @endcan
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-gray-500">Hakuna idara bado.</div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $departments->links() }}
    </div>
@endsection
