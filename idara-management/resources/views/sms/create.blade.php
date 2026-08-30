@extends('layouts.app')

@section('title', 'Tuma SMS')

@section('content')
    <h1 class="text-lg font-semibold mb-6">Tuma SMS - {{ $department->name }}</h1>

    <form method="POST" action="{{ route('departments.sms.store', $department) }}"
          class="bg-white border border-gray-200 rounded-lg p-6 space-y-4 max-w-2xl" x-data="{ len: 0 }">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Wapokeaji</label>
            <div class="border border-gray-200 rounded-md max-h-64 overflow-y-auto divide-y divide-gray-100">
                @forelse ($recipients as $recipient)
                    <label class="flex items-center gap-3 px-3 py-2 text-sm">
                        <input type="checkbox" name="recipient_ids[]" value="{{ $recipient->id }}"
                               {{ $recipient->phone ? '' : 'disabled' }} checked class="rounded border-gray-300">
                        <span>{{ $recipient->name }}</span>
                        <span class="text-xs text-gray-400">{{ $recipient->phone ?? 'hakuna namba ya simu' }}</span>
                    </label>
                @empty
                    <p class="px-3 py-4 text-sm text-gray-400">Hakuna wanachama kwenye idara hii bado.</p>
                @endforelse
            </div>
        </div>

        <div>
            <label for="message" class="block text-sm font-medium text-gray-700">Ujumbe</label>
            <textarea id="message" name="message" rows="4" maxlength="459" required x-on:input="len = $event.target.value.length"
                      class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">{{ old('message') }}</textarea>
            <p class="text-xs text-gray-400 mt-1"><span x-text="len">0</span>/459 herufi</p>
        </div>

        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">
            Tuma
        </button>
    </form>
@endsection
