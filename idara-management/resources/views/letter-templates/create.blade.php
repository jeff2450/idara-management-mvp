@extends('layouts.app')

@section('title', 'Template Mpya')

@section('content')
    <h1 class="text-lg font-semibold mb-2">Template Mpya ya Barua</h1>
    <p class="text-sm text-gray-500 mb-6">
        Tumia <code class="bg-gray-100 px-1 rounded">&#123;&#123; jina_mwanachama &#125;&#125;</code>,
        <code class="bg-gray-100 px-1 rounded">&#123;&#123; idara &#125;&#125;</code>,
        <code class="bg-gray-100 px-1 rounded">&#123;&#123; tarehe &#125;&#125;</code> - au placeholder yoyote
        mpya utakayotaka - zitajazwa kiongozi anapozalisha barua.
    </p>

    <form method="POST" action="{{ route('letter-templates.store') }}"
          class="bg-white border border-gray-200 rounded-lg p-6 space-y-4 max-w-2xl">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Jina la Template</label>
            <input id="name" name="name" value="{{ old('name') }}" required
                   class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
        </div>

        <div>
            <label for="body_template" class="block text-sm font-medium text-gray-700">Maandishi ya Barua</label>
            <textarea id="body_template" name="body_template" rows="12" required
                      class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm font-mono">{{ old('body_template') }}</textarea>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                Hifadhi
            </button>
            <a href="{{ route('letter-templates.index') }}" class="text-sm text-gray-500 hover:underline">Ghairi</a>
        </div>
    </form>
@endsection
