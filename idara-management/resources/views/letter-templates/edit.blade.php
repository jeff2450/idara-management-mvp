@extends('layouts.app')

@section('title', 'Hariri Template')

@section('content')
    <h1 class="text-lg font-semibold mb-6">Hariri: {{ $template->name }}</h1>

    <form method="POST" action="{{ route('letter-templates.update', $template) }}"
          class="bg-white border border-gray-200 rounded-lg p-6 space-y-4 max-w-2xl">
        @csrf @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Jina la Template</label>
            <input id="name" name="name" value="{{ old('name', $template->name) }}" required
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
        </div>

        <div>
            <label for="body_template" class="block text-sm font-medium text-gray-700">Maandishi ya Barua</label>
            <textarea id="body_template" name="body_template" rows="12" required
                      class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm font-mono">{{ old('body_template', $template->body_template) }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                Sasisha
            </button>
            <a href="{{ route('letter-templates.index') }}" class="text-sm text-gray-500 hover:underline">Ghairi</a>
        </div>
    </form>
@endsection
