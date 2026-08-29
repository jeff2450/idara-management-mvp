<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Idara Management System') }} - @yield('title', 'Dashibodi')</title>
    {{-- Tailwind kupitia CDN kwa MVP - haihitaji hatua ya `npm run build`.
         Kwa production, badilisha na pipeline ya Vite/Tailwind iliyoainishwa
         kwenye stacks.md §3. --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">

@auth
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 flex items-center justify-between h-14">
            <a href="{{ route('dashboard') }}" class="font-semibold text-indigo-700">
                Idara Management System
            </a>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-700">Dashibodi</a>
                <a href="{{ route('departments.index') }}" class="hover:text-indigo-700">Idara</a>
                <span class="text-gray-400">|</span>
                <span class="text-gray-600">{{ auth()->user()->name }}
                    <span class="text-xs text-gray-400">({{ auth()->user()->getRoleNames()->first() ?? 'member' }})</span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-600 hover:underline">Toka</button>
                </form>
            </div>
        </div>
    </nav>
@endauth

<main class="max-w-5xl mx-auto px-4 py-8">

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

</body>
</html>
