@extends('layouts.app')

@section('title', 'Idara Mpya')

@section('content')
    <h1 class="text-lg font-semibold mb-6">Unda Idara Mpya</h1>

    <form method="POST" action="{{ route('departments.store') }}"
          class="bg-white border border-gray-200 rounded-lg p-6 space-y-4 max-w-lg">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Jina la Idara</label>
            <input id="name" name="name" value="{{ old('name') }}" required
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Maelezo (hiari)</label>
            <textarea id="description" name="description" rows="3"
                      class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                Hifadhi
            </button>
            <a href="{{ route('departments.index') }}" class="text-sm text-gray-500 hover:underline">Ghairi</a>
        </div>
    </form>
@endsection
