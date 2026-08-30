<!DOCTYPE html>
<html lang="sw" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingia - {{ config('app.name', 'KANISA - Idara Management System') }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full min-h-screen flex items-center justify-center p-4 bg-[#f8fafc]">

    <div class="w-full max-w-md space-y-6">
        
        <!-- Branding -->
        <div class="text-center">
            <div class="inline-flex w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200 items-center justify-center text-emerald-600 shadow-sm shadow-emerald-100 mb-3">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v4M10 4h4"/>
                    <path d="M18 22V9l-6-5-6 5v13a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2z"/>
                    <path d="M9 22v-6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6"/>
                    <circle cx="12" cy="9" r="1"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">KANISA</h1>
            <p class="text-xs font-semibold text-slate-400 mt-0.5">Idara Management System</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-7 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-1">Ingia kwenye akaunti yako</h2>
            <p class="text-xs text-slate-500 mb-5">Weka taarifa zako ili kuendelea</p>

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-xs shadow-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700">Barua pepe</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="admin@idara.test"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs focus:outline-none focus:border-emerald-500 bg-slate-50">
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700">Nenosiri</label>
                    <input id="password" type="password" name="password" required placeholder="••••••••"
                           class="mt-1 w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs focus:outline-none focus:border-emerald-500 bg-slate-50">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span>Nikumbuke</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-3 rounded-xl shadow-sm transition">
                    Ingia (Sign In)
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                <p class="text-[11px] text-slate-400">
                    Akaunti za demo: <span class="font-semibold text-slate-600">admin@idara.test</span> au <span class="font-semibold text-slate-600">david@idara.test</span> (password: <code class="bg-slate-100 px-1 py-0.5 rounded text-slate-700 font-mono">password</code>)
                </p>
            </div>
        </div>

    </div>

</body>
</html>
