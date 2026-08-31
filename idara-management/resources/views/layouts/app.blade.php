<!DOCTYPE html>
<html lang="sw" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'KANISA - Idara Management System') }} - @yield('title', 'Dashboard')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Chart.js for beautiful charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="h-full text-slate-800 antialiased flex flex-col bg-[#f8fafc]">

@auth
<div class="flex h-screen overflow-hidden">

    <!-- ============================================================== -->
    <!-- SIDEBAR -->
    <!-- ============================================================== -->
    <aside class="w-64 flex-shrink-0 bg-white border-r border-slate-200/80 flex flex-col justify-between z-20 select-none">
        
        <!-- Top Branding & Nav -->
        <div class="flex flex-col flex-1 overflow-y-auto">
            
            <!-- Logo Section -->
            <a href="{{ route('home') }}" class="px-6 py-5 border-b border-slate-100 flex items-center gap-3 group hover:bg-slate-50/80 transition-colors" title="Tazama Ukurasa wa Maendeleo ya Kanisa">
                <div class="w-10 h-10 rounded-xl bg-brand-50 border border-brand-200/60 flex items-center justify-center text-brand-600 shadow-sm shadow-brand-100 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v4M10 4h4"/>
                        <path d="M18 22V9l-6-5-6 5v13a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2z"/>
                        <path d="M9 22v-6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6"/>
                        <circle cx="12" cy="9" r="1"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-extrabold tracking-tight text-slate-900 leading-none flex items-center gap-1.5">
                        KANISA
                    </h1>
                    <p class="text-[11px] font-medium text-slate-400 mt-1">Idara Management System</p>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav class="p-3 space-y-1 text-sm font-medium">

                <!-- 1. Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700 font-semibold shadow-sm shadow-brand-50 border border-brand-200/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-brand-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="7" height="9" x="3" y="3" rx="1"/>
                        <rect width="7" height="5" x="14" y="3" rx="1"/>
                        <rect width="7" height="9" x="14" y="12" rx="1"/>
                        <rect width="7" height="5" x="3" y="16" rx="1"/>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Departments -->
                <a href="{{ route('departments.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('departments.index') || request()->routeIs('departments.show') ? 'bg-brand-50 text-brand-700 font-semibold shadow-sm shadow-brand-50 border border-brand-200/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('departments.*') ? 'text-brand-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 22h16M4 18h16M9 18V6l6-4v16M9 10h2M9 14h2M13 10h2M13 14h2"/>
                    </svg>
                    <span>Departments</span>
                </a>

                <!-- 3. Members -->
                <a href="{{ route('dashboard', ['view' => 'leader']) }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->query('view') === 'leader' && request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700 font-semibold shadow-sm shadow-brand-50 border border-brand-200/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span>Members</span>
                </a>

                <!-- 4. Leaders -->
                <a href="{{ route('departments.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                    <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <polyline points="16 11 18 13 22 9"/>
                    </svg>
                    <span>Leaders</span>
                </a>

                <!-- 5. SMS Messaging -->
                @php
                    $firstDept = auth()->user()->departments()->first() ?? \App\Models\Department::withoutGlobalScopes()->first();
                @endphp
                <a href="{{ $firstDept ? route('departments.sms.index', $firstDept) : '#' }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('departments.sms.*') ? 'bg-brand-50 text-brand-700 font-semibold shadow-sm shadow-brand-50 border border-brand-200/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('departments.sms.*') ? 'text-brand-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                    <span>SMS Messaging</span>
                </a>

                <!-- 6. Letters -->
                <a href="{{ $firstDept ? route('departments.letters.index', $firstDept) : '#' }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('departments.letters.*') ? 'bg-brand-50 text-brand-700 font-semibold shadow-sm shadow-brand-50 border border-brand-200/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('departments.letters.*') ? 'text-brand-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                    <span>Letters</span>
                </a>

                <!-- 7. Reports -->
                <a href="{{ $firstDept ? route('departments.reports.index', $firstDept) : '#' }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('departments.reports.*') ? 'bg-brand-50 text-brand-700 font-semibold shadow-sm shadow-brand-50 border border-brand-200/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('departments.reports.*') ? 'text-brand-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3v18h18"/>
                        <path d="m19 9-5 5-4-4-3 3"/>
                    </svg>
                    <span>Reports</span>
                </a>

                <!-- 8. Transactions -->
                <a href="{{ $firstDept ? route('departments.transactions.index', $firstDept) : '#' }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('departments.transactions.*') ? 'bg-brand-50 text-brand-700 font-semibold shadow-sm shadow-brand-50 border border-brand-200/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('departments.transactions.*') ? 'text-brand-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <line x1="2" x2="22" y1="10" y2="10"/>
                    </svg>
                    <span>Transactions</span>
                </a>

                <!-- 9. Audit Trail -->
                <a href="{{ $firstDept ? route('departments.progress', $firstDept) : '#' }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('departments.progress') ? 'bg-brand-50 text-brand-700 font-semibold shadow-sm shadow-brand-50 border border-brand-200/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="w-5 h-5 {{ request()->routeIs('departments.progress') ? 'text-brand-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                    <span>Audit Trail</span>
                </a>

                <!-- 10. Settings -->
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('letter-templates.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('letter-templates.*') ? 'bg-brand-50 text-brand-700 font-semibold shadow-sm shadow-brand-50 border border-brand-200/50' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="w-5 h-5 {{ request()->routeIs('letter-templates.*') ? 'text-brand-600' : 'text-slate-400' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <span>Settings</span>
                    </a>
                @endif
            </nav>
        </div>

        <!-- Bottom User Card -->
        <div class="p-3 border-t border-slate-200/80 bg-slate-50/50" x-data="{ open: false }">
            <div class="relative">
                <button @click="open = !open" type="button" class="w-full flex items-center justify-between p-2 rounded-xl hover:bg-white transition border border-transparent hover:border-slate-200 text-left">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-brand-100 border border-brand-300 text-brand-800 font-bold flex items-center justify-center text-sm shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white"></span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-slate-500 truncate flex items-center gap-1">
                                <span>{{ auth()->user()->isAdmin() ? 'Administrator' : 'Kiongozi wa Idara' }}</span>
                            </p>
                            <p class="text-[10px] text-emerald-600 font-medium">● Online</p>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" @click.outside="open = false" x-transition
                     class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-xl shadow-lg border border-slate-200 py-1 z-30 text-xs font-medium">
                    <div class="px-3 py-2 border-b border-slate-100 text-slate-500">
                        {{ auth()->user()->email }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-rose-600 hover:bg-rose-50 flex items-center gap-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            Toka (Logout)
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </aside>

    <!-- ============================================================== -->
    <!-- MAIN CONTENT AREA -->
    <!-- ============================================================== -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

        <!-- Top Header Bar -->
        <header class="bg-white border-b border-slate-200/80 sticky top-0 z-10 px-8 py-3.5 flex items-center justify-between gap-4">
            
            <!-- Left Page Title / Breadcrumb -->
            <div>
                @yield('header_title')
            </div>

            <!-- Right Header Actions -->
            <div class="flex items-center gap-3">
                
                <!-- Search Bar -->
                <div class="relative hidden sm:block w-72">
                    <input type="text"
                           placeholder="@yield('search_placeholder', 'Search anything...')"
                           class="w-full pl-9 pr-4 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>

                <!-- Notifications -->
                <button type="button" class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition" title="Notifications">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                    </svg>
                    <span class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white rounded-full text-[10px] font-bold flex items-center justify-center">
                        @yield('notif_count', '6')
                    </span>
                </button>

                <!-- Mail / Messages -->
                <button type="button" class="relative p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition" title="Messages">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                    </svg>
                    <span class="absolute top-1 right-1 w-4 h-4 bg-rose-500 text-white rounded-full text-[10px] font-bold flex items-center justify-center">
                        @yield('mail_count', '5')
                    </span>
                </button>

                <!-- User Avatar Pill -->
                <div class="flex items-center gap-2 pl-2 border-l border-slate-200">
                    <div class="w-8 h-8 rounded-full bg-brand-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="hidden md:block text-left text-xs">
                        <p class="font-bold text-slate-800 leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-400 leading-tight">{{ auth()->user()->isAdmin() ? 'Administrator' : 'Kiongozi wa Idara' }}</p>
                    </div>
                </div>

                <!-- Date Widget (Admin view pill) -->
                <div class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-sm">
                    <svg class="w-3.5 h-3.5 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    <span>Today: {{ now()->format('d M Y') }}</span>
                    <svg class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </div>

            </div>
        </header>

        <!-- Main Body -->
        <main class="p-8 flex-1">
            @if (session('status'))
                <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm shadow-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

    </div>

</div>
@else
    <main class="min-h-screen flex items-center justify-center p-4">
        @yield('content')
    </main>
@endauth

</body>
</html>
