@extends('layouts.app')

@section('title', 'Templates za Barua')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold">Templates za Barua</h1>
        <a href="{{ route('letter-templates.create') }}"
           class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
            + Template Mpya
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($templates as $template)
            <div class="flex items-center justify-between px-4 py-3 text-sm">
                <div>
                    <p class="font-medium">{{ $template->name }}</p>
                    <p class="text-xs text-gray-400">{{ $template->letters_count }} barua zimezalishwa nayo</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('letter-templates.edit', $template) }}" class="text-gray-500 hover:text-indigo-700">Hariri</a>
                    <form method="POST" action="{{ route('letter-templates.destroy', $template) }}"
                          onsubmit="return confirm('Futa template hii?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:underline">Futa</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-gray-500">Hakuna template bado.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $templates->links() }}</div>
@endsection
