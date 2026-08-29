@extends('layouts.app')

@section('title', $department->name)

@section('content')
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold">{{ $department->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $department->description }}</p>
        </div>
        @can('update', $department)
            <a href="{{ route('departments.edit', $department) }}" class="text-sm text-gray-500 hover:text-indigo-700">Hariri</a>
        @endcan
    </div>

    <div class="grid md:grid-cols-2 gap-6">

        {{-- Viongozi --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h2 class="font-medium mb-3">Viongozi ({{ $department->leaders->count() }})</h2>
            <ul class="divide-y divide-gray-100">
                @forelse ($department->leaders as $leader)
                    <li class="py-2 flex items-center justify-between text-sm">
                        <div>
                            <p>{{ $leader->name }}</p>
                            <p class="text-xs text-gray-400">{{ $leader->email }}</p>
                        </div>
                        @can('manageMembers', $department)
                            <form method="POST" action="{{ route('departments.members.destroy', [$department, $leader]) }}"
                                  onsubmit="return confirm('Ondoa kiongozi huyu kwenye idara?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-xs">Ondoa</button>
                            </form>
                        @endcan
                    </li>
                @empty
                    <li class="py-4 text-sm text-gray-400">Hakuna kiongozi bado.</li>
                @endforelse
            </ul>
        </div>

        {{-- Wanachama --}}
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h2 class="font-medium mb-3">Wanachama ({{ $department->members->count() }})</h2>
            <ul class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                @forelse ($department->members as $member)
                    <li class="py-2 flex items-center justify-between text-sm">
                        <div>
                            <p>{{ $member->name }}</p>
                            <p class="text-xs text-gray-400">{{ $member->email }}</p>
                        </div>
                        @can('manageMembers', $department)
                            <form method="POST" action="{{ route('departments.members.destroy', [$department, $member]) }}"
                                  onsubmit="return confirm('Ondoa mwanachama huyu kwenye idara?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-xs">Ondoa</button>
                            </form>
                        @endcan
                    </li>
                @empty
                    <li class="py-4 text-sm text-gray-400">Hakuna mwanachama bado.</li>
                @endforelse
            </ul>
        </div>
    </div>

    @can('manageMembers', $department)
        <div class="bg-white border border-gray-200 rounded-lg p-6 mt-6 max-w-xl" x-data="{ mode: 'existing' }">
            <h2 class="font-medium mb-4">Ongeza Mtu kwenye Idara</h2>

            <form method="POST" action="{{ route('departments.members.store', $department) }}" class="space-y-4">
                @csrf

                <div class="flex gap-4 text-sm">
                    <label class="flex items-center gap-1">
                        <input type="radio" name="mode" value="existing" x-model="mode" checked> Mtumiaji aliyepo
                    </label>
                    <label class="flex items-center gap-1">
                        <input type="radio" name="mode" value="new" x-model="mode"> Mtumiaji mpya
                    </label>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Barua pepe</label>
                    <input id="email" name="email" type="email" required
                           class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                </div>

                <template x-if="mode === 'new'">
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Jina Kamili</label>
                            <input id="name" name="name" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Namba ya Simu (hiari)</label>
                            <input id="phone" name="phone" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Nenosiri la Awali</label>
                            <input id="password" name="password" type="password" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                        </div>
                    </div>
                </template>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Nafasi kwenye Idara</label>
                    <select id="role" name="role" class="mt-1 w-full rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="member">Mwanachama</option>
                        @if (auth()->user()->isAdmin())
                            <option value="leader">Kiongozi</option>
                        @endif
                    </select>
                    @unless (auth()->user()->isAdmin())
                        <p class="text-xs text-gray-400 mt-1">Uteuzi wa Kiongozi ni Admin pekee.</p>
                    @endunless
                </div>

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                    Ongeza
                </button>
            </form>
        </div>
    @endcan
@endsection
