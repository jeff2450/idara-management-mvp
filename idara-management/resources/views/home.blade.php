<!DOCTYPE html>
<html lang="sw" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maendeleo ya Kanisa {{ $year }} — Idara Management System</title>
    <meta name="description" content="Ukurasa wa wazi wa maendeleo ya kanisa, idara na shughuli zilizofanyika mwaka {{ $year }}.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 9999px; }
        .progress-fill { transition: width 0.8s cubic-bezier(0.4,0,0.2,1); }
    </style>
</head>
<body class="bg-gray-50 text-gray-900" x-data="{ selectedDept: null, mobileOpen: false }">

{{-- ================================================================ --}}
{{-- NAVIGATION --}}
{{-- ================================================================ --}}
<header class="sticky top-0 z-40 bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-green-800 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v4M10 4h4"/>
                        <path d="M18 22V9l-6-5-6 5v13a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2z"/>
                        <circle cx="12" cy="9" r="1.2"/>
                    </svg>
                </div>
                <div>
                    <span class="text-sm font-bold text-gray-900">KANISA</span>
                    <span class="hidden sm:inline text-xs text-gray-400 ml-1.5">Idara Management System</span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-0.5 text-sm text-gray-600">
                <a href="#muhtasari" class="px-3 py-1.5 rounded-md hover:bg-gray-100 hover:text-gray-900 transition-colors">Muhtasari</a>
                <a href="#idara" class="px-3 py-1.5 rounded-md hover:bg-gray-100 hover:text-gray-900 transition-colors">Idara</a>
                <a href="#robo" class="px-3 py-1.5 rounded-md hover:bg-gray-100 hover:text-gray-900 transition-colors">Robo za Mwaka</a>
                <a href="#matukio" class="px-3 py-1.5 rounded-md hover:bg-gray-100 hover:text-gray-900 transition-colors">Matukio</a>
                <a href="#shughuli" class="px-3 py-1.5 rounded-md hover:bg-gray-100 hover:text-gray-900 transition-colors">Shughuli</a>
                <a href="#ibada" class="px-3 py-1.5 rounded-md hover:bg-gray-100 hover:text-gray-900 transition-colors">Ibada</a>
            </nav>

            <div class="flex items-center gap-2">
                {{-- Year switcher --}}
                <form method="GET" action="{{ route('home') }}" class="hidden sm:flex items-center">
                    <select name="year" onchange="this.form.submit()"
                            class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 bg-white text-gray-700 focus:outline-none focus:ring-1 focus:ring-green-600 focus:border-green-600">
                        @for($y = now()->year + 1; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $y === $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium text-white bg-green-800 hover:bg-green-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Dashibodi
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-sm font-medium text-white bg-green-800 hover:bg-green-700 transition-colors">
                        Ingia Mfumo
                    </a>
                @endauth

                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-1.5 rounded-md text-gray-500 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div x-show="mobileOpen" x-transition class="md:hidden border-t border-gray-100 py-2 space-y-0.5 text-sm text-gray-700">
            <a @click="mobileOpen=false" href="#muhtasari" class="block px-3 py-2 rounded-md hover:bg-gray-100">Muhtasari</a>
            <a @click="mobileOpen=false" href="#idara" class="block px-3 py-2 rounded-md hover:bg-gray-100">Idara</a>
            <a @click="mobileOpen=false" href="#robo" class="block px-3 py-2 rounded-md hover:bg-gray-100">Robo za Mwaka</a>
            <a @click="mobileOpen=false" href="#matukio" class="block px-3 py-2 rounded-md hover:bg-gray-100">Matukio</a>
            <a @click="mobileOpen=false" href="#shughuli" class="block px-3 py-2 rounded-md hover:bg-gray-100">Shughuli</a>
            <a @click="mobileOpen=false" href="#ibada" class="block px-3 py-2 rounded-md hover:bg-gray-100">Ibada za Wiki</a>
        </div>
    </div>
</header>

{{-- ================================================================ --}}
{{-- PAGE TITLE BANNER --}}
{{-- ================================================================ --}}
<div class="bg-white border-b border-gray-200" id="muhtasari">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div>
                <p class="text-xs font-semibold text-green-700 uppercase tracking-wider mb-1.5">Tathmini ya Maendeleo — Mwaka {{ $year }}</p>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Maendeleo ya Kanisa na Idara Zake</h1>
                <p class="mt-2 text-sm text-gray-500 max-w-2xl">
                    Ukurasa huu unaonyesha maendeleo halisi ya kila idara, ratiba za matukio, na takwimu za kanisa kwa mwaka {{ $year }}.
                    Data inasasishwa moja kwa moja kutoka kwenye mfumo wa usimamizi.
                </p>
            </div>

            {{-- Overall Progress Card --}}
            <div class="flex-shrink-0 border border-gray-200 rounded-xl p-5 bg-gray-50 w-full lg:w-56">
                <p class="text-xs font-medium text-gray-500 mb-1">Maendeleo Makuu ({{ $year }})</p>
                <div class="flex items-baseline gap-2 mb-3">
                    <span class="text-3xl font-bold text-gray-900">{{ $overallProgressPercent }}%</span>
                    <span class="text-xs text-gray-400">ya malengo {{ $overallTotalGoals }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                    <div class="bg-green-700 h-1.5 rounded-full progress-fill" style="width: {{ $overallProgressPercent }}%"></div>
                </div>
                <p class="text-xs text-gray-400">{{ $overallCompletedGoals }} kati ya {{ $overallTotalGoals }} malengo yamekamilika</p>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================ --}}
{{-- KPI STATS ROW --}}
{{-- ================================================================ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-2">Idara Zinazofanya Kazi</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalDeptsCount }}</p>
            <p class="text-xs text-gray-400 mt-0.5">idara hai</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-2">Wanachama Wote</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalMembersCount) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">waliorekodiwa</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-2">Viongozi wa Idara</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalLeadersCount }}</p>
            <p class="text-xs text-gray-400 mt-0.5">wanaosimamia</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-2">Malengo Yakamilishwa</p>
            <p class="text-2xl font-bold text-gray-900">{{ $overallCompletedGoals }}</p>
            <p class="text-xs text-gray-400 mt-0.5">kati ya {{ $overallTotalGoals }}</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-2">Shughuli Zilizofanyika</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalRecordedActivities) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">zilizorekodiwa</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs text-gray-500 mb-2">SMS Zilizotumwa {{ $year }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($totalSmsThisYear) }}</p>
            <p class="text-xs text-gray-400 mt-0.5">wapokezi</p>
        </div>

    </div>
</section>

{{-- ================================================================ --}}
{{-- QUARTERLY BREAKDOWN --}}
{{-- ================================================================ --}}
<section id="robo" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="mb-5">
        <h2 class="text-base font-semibold text-gray-900">Utekelezaji wa Robo za Mwaka {{ $year }}</h2>
        <p class="text-xs text-gray-500 mt-0.5">Hesabu zinatokana na ratiba halisi zilizoingiwa kwenye mfumo.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($quarterlyProgress as $key => $q)
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-700">{{ $q['label'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $q['period'] }}</p>
                    </div>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md
                        @if($q['status_color'] === 'emerald') bg-green-50 text-green-700 border border-green-200
                        @elseif($q['status_color'] === 'indigo') bg-blue-50 text-blue-700 border border-blue-200
                        @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                        {{ $q['status'] }}
                    </span>
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-gray-500">{{ $q['completed'] }} / {{ $q['total'] }} malengo</span>
                        <span class="font-semibold text-gray-900">{{ $q['percent'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full progress-fill
                            @if($q['status_color'] === 'emerald') bg-green-600
                            @elseif($q['status_color'] === 'indigo') bg-blue-600
                            @else bg-amber-500 @endif"
                            style="width: {{ $q['percent'] }}%"></div>
                    </div>
                </div>

                @if($q['total'] === 0)
                    <p class="text-xs text-gray-400 italic">Hakuna malengo yaliyopangwa kwa robo hii.</p>
                @else
                    <div class="space-y-1.5 border-t border-gray-100 pt-3">
                        <p class="text-xs font-medium text-gray-500 mb-1">Malengo ya Mwaka:</p>
                        @foreach($q['milestones'] as $milestone)
                            <div class="flex items-start gap-1.5 text-xs text-gray-600">
                                <svg class="w-3.5 h-3.5 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span class="line-clamp-1">{{ $milestone }}</span>
                            </div>
                        @endforeach
                        @if(empty($q['milestones']))
                            <p class="text-xs text-gray-400 italic">Malengo yataonekana hapa.</p>
                        @endif
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</section>

{{-- ================================================================ --}}
{{-- DEPARTMENT PROGRESS CARDS --}}
{{-- ================================================================ --}}
<section id="idara" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="mb-5 flex items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Maendeleo ya Kila Idara — {{ $year }}</h2>
            <p class="text-xs text-gray-500 mt-0.5">Data inatokana na ratiba za mwaka zilizoingiwa na viongozi wa idara.</p>
        </div>
        <a href="#matukio" class="hidden sm:inline-flex items-center gap-1 text-xs font-medium text-green-700 hover:underline">
            Tazama matukio yanayokuja
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
    </div>

    @if($departmentsData->isEmpty())
        <div class="bg-white border border-gray-200 rounded-xl p-10 text-center">
            <p class="text-sm text-gray-500">Hakuna idara zilizoanzishwa bado kwenye mfumo.</p>
            @auth
                <a href="{{ route('departments.index') }}" class="mt-3 inline-block text-xs font-medium text-green-700 hover:underline">Unda Idara →</a>
            @endauth
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($departmentsData as $dept)
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:border-gray-300 hover:shadow-sm transition-all duration-200 flex flex-col">

                    {{-- Header --}}
                    <div class="px-5 pt-5 pb-4 border-b border-gray-100">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <h3 class="text-sm font-semibold text-gray-900 leading-snug">{{ $dept['name'] }}</h3>
                            @if($dept['total_schedules'] > 0)
                                <span class="flex-shrink-0 text-xs font-semibold px-2 py-0.5 rounded-md
                                    @if($dept['progress_percent'] >= 80) bg-green-50 text-green-700 border border-green-200
                                    @elseif($dept['progress_percent'] >= 50) bg-blue-50 text-blue-700 border border-blue-200
                                    @else bg-gray-100 text-gray-600 border border-gray-200 @endif">
                                    {{ $dept['progress_percent'] }}%
                                </span>
                            @else
                                <span class="flex-shrink-0 text-xs px-2 py-0.5 rounded-md bg-gray-100 text-gray-400 border border-gray-200">Haina data</span>
                            @endif
                        </div>

                        @if($dept['description'])
                            <p class="text-xs text-gray-500 leading-relaxed mb-4 line-clamp-2">{{ $dept['description'] }}</p>
                        @endif

                        {{-- Progress bar --}}
                        <div>
                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                                <span>Utekelezaji wa Ratiba</span>
                                <span class="font-medium text-gray-700">{{ $dept['completed_schedules'] }}/{{ $dept['total_schedules'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5">
                                <div class="bg-green-700 h-1.5 rounded-full progress-fill" style="width: {{ $dept['progress_percent'] }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Meta --}}
                    <div class="px-5 py-3 flex items-center gap-5 text-xs text-gray-500 flex-grow">
                        <span>
                            <span class="text-gray-400">Kiongozi:</span>
                            <span class="font-medium text-gray-700 ml-1">{{ $dept['leader_name'] }}</span>
                        </span>
                        <span>
                            <span class="font-medium text-gray-700">{{ $dept['members_count'] }}</span>
                            <span class="text-gray-400 ml-1">wanachama</span>
                        </span>
                    </div>

                    {{-- Footer --}}
                    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50">
                        <button type="button"
                                @click="selectedDept = {{ Js::from($dept) }}"
                                class="w-full flex items-center justify-center gap-1.5 text-xs font-medium text-green-700 hover:text-green-800 py-0.5 transition-colors">
                            <span>Ratiba na maelezo kamili</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>

{{-- ================================================================ --}}
{{-- UPCOMING EVENTS TABLE --}}
{{-- ================================================================ --}}
<section id="matukio" class="border-t border-gray-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-5">
            <h2 class="text-base font-semibold text-gray-900">Matukio Yanayokuja — {{ $year }}</h2>
            <p class="text-xs text-gray-500 mt-0.5">Ratiba za shughuli zilizopangwa ambazo bado hazijafanyika (hali ya "inasubiri").</p>
        </div>

        @if($upcomingEvents->isEmpty())
            <div class="border border-gray-200 rounded-xl p-10 text-center">
                <p class="text-sm text-gray-500">Hakuna matukio yanayokuja yaliyopangwa kwa mwaka {{ $year }}.</p>
                @auth
                    <a href="{{ route('schedules.redirect') }}" class="mt-3 inline-block text-xs font-medium text-green-700 hover:underline">Panga Ratiba →</a>
                @endauth
            </div>
        @else
            <div class="overflow-hidden rounded-xl border border-gray-200">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider w-24">Mwezi</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Lengo / Tukio</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Idara</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Hali</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($upcomingEvents as $evt)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3.5">
                                    <span class="inline-block bg-gray-100 text-gray-700 text-xs font-semibold px-2.5 py-1 rounded-md border border-gray-200">
                                        {{ $evt['month_short'] }}
                                    </span>
                                    <p class="text-xs text-gray-400 mt-1">{{ $evt['month_name'] }}</p>
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="font-medium text-gray-900 text-sm leading-snug">{{ $evt['title'] }}</p>
                                    @if($evt['description'])
                                        <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $evt['description'] }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 hidden md:table-cell">
                                    <span class="text-xs text-gray-600">{{ $evt['department_name'] }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Inasubiri
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>

{{-- ================================================================ --}}
{{-- COMPLETED MILESTONES --}}
{{-- ================================================================ --}}
@if($completedMilestones->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="mb-5">
        <h2 class="text-base font-semibold text-gray-900">Malengo Yaliyokamilika — {{ $year }}</h2>
        <p class="text-xs text-gray-500 mt-0.5">Shughuli za ratiba zilizothibitishwa kuwa zimefanyika.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($completedMilestones as $m)
            <div class="bg-white border border-gray-200 rounded-xl px-4 py-3.5 flex items-center gap-3">
                <div class="w-7 h-7 rounded-full bg-green-100 border border-green-200 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $m['title'] }}</p>
                    <p class="text-xs text-gray-400">{{ $m['department_name'] }} · {{ $m['month_name'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- ================================================================ --}}
{{-- RECENT ACTIVITIES FEED --}}
{{-- ================================================================ --}}
<section id="shughuli" class="border-t border-gray-200 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="mb-5">
            <h2 class="text-base font-semibold text-gray-900">Shughuli Zilizofanyika Hivi Karibuni</h2>
            <p class="text-xs text-gray-500 mt-0.5">Kumbukumbu halisi za shughuli zilizorekodiwa na viongozi kwenye mfumo.</p>
        </div>

        @if($recentActivities->isEmpty())
            <div class="border border-gray-200 rounded-xl p-10 text-center">
                <p class="text-sm text-gray-500">Hakuna shughuli zilizorekodiwa bado.</p>
                @auth
                    <a href="{{ route('progress.redirect') }}" class="mt-3 inline-block text-xs font-medium text-green-700 hover:underline">Rekodi Shughuli →</a>
                @endauth
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recentActivities as $act)
                    <div class="border border-gray-200 rounded-xl p-4 hover:border-gray-300 hover:shadow-sm transition-all">
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="text-xs text-gray-500 truncate">{{ $act['department_name'] }}</span>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $act['occurred_at'] }}</span>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-1.5 leading-snug">{{ $act['title'] }}</h3>
                        @if($act['description'])
                            <p class="text-xs text-gray-500 leading-relaxed mb-3 line-clamp-2">{{ $act['description'] }}</p>
                        @endif
                        <div class="flex items-center gap-1.5 text-xs text-gray-400 border-t border-gray-100 pt-2.5 mt-auto">
                            <svg class="w-3.5 h-3.5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Imeripotiwa na <span class="font-medium text-gray-600 ml-0.5">{{ $act['recorded_by'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ================================================================ --}}
{{-- WEEKLY WORSHIP SCHEDULE --}}
{{-- ================================================================ --}}
<section id="ibada" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-5">
        <h2 class="text-base font-semibold text-gray-900">Ratiba ya Ibada za Kila Wiki</h2>
        <p class="text-xs text-gray-500 mt-0.5">Wote wanakaribishwa kushiriki katika ibada na mikutano ya ushirika.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($weeklySchedule as $sched)
            <div class="bg-white border border-gray-200 rounded-xl p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 pb-3 border-b border-gray-100">{{ $sched['day'] }}</h3>
                <div class="space-y-4">
                    @foreach($sched['services'] as $srv)
                        <div>
                            <p class="text-xs font-medium text-gray-700">{{ $srv['name'] }}</p>
                            <p class="text-xs text-green-700 font-medium mt-0.5">{{ $srv['time'] }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $srv['venue'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- ================================================================ --}}
{{-- STAFF LOGIN CTA --}}
{{-- ================================================================ --}}
<div class="bg-gray-900 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">
            <div>
                <h2 class="text-sm font-semibold text-white">Je, wewe ni Kiongozi au Msimamizi wa Idara?</h2>
                <p class="text-xs text-gray-400 mt-1">Ingia kwenye mfumo ili kusimamia ratiba, kurekodi shughuli, kutuma SMS na kuzalisha ripoti za PDF.</p>
            </div>
            @auth
                <a href="{{ route('dashboard') }}"
                   class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-white bg-green-700 hover:bg-green-600 transition-colors border border-green-600">
                    Nenda Dashibodi
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium text-white bg-green-700 hover:bg-green-600 transition-colors border border-green-600">
                    Ingia Mfumo (Staff Login)
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            @endauth
        </div>
    </div>
</div>

{{-- ================================================================ --}}
{{-- FOOTER --}}
{{-- ================================================================ --}}
<footer class="bg-gray-900 border-t border-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-md bg-green-800 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v4M10 4h4"/>
                        <path d="M18 22V9l-6-5-6 5v13a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-white">KANISA</span>
                <span class="text-gray-600 text-xs">Idara Management System</span>
            </div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                <a href="#muhtasari" class="hover:text-gray-300 transition-colors">Muhtasari</a>
                <a href="#idara" class="hover:text-gray-300 transition-colors">Idara</a>
                <a href="#robo" class="hover:text-gray-300 transition-colors">Robo za Mwaka</a>
                <a href="#matukio" class="hover:text-gray-300 transition-colors">Matukio</a>
                <a href="#ibada" class="hover:text-gray-300 transition-colors">Ibada</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-gray-300 transition-colors">Dashibodi</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-gray-300 transition-colors">Ingia Mfumo</a>
                @endauth
            </div>
            <p class="text-xs text-gray-600">&copy; {{ date('Y') }} Kanisa. Haki zote zimehifadhiwa.</p>
        </div>
    </div>
</footer>

{{-- ================================================================ --}}
{{-- DEPARTMENT DETAIL MODAL --}}
{{-- ================================================================ --}}
<div x-show="selectedDept !== null"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto bg-black/40 flex items-center justify-center p-4"
     style="display: none;">

    <div @click.away="selectedDept = null"
         class="bg-white border border-gray-200 rounded-2xl max-w-2xl w-full shadow-xl overflow-hidden">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-semibold text-gray-900" x-text="selectedDept?.name"></h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    Kiongozi:
                    <span class="font-medium text-gray-700" x-text="selectedDept?.leader_name"></span>
                    <span class="text-gray-300 mx-1.5">·</span>
                    <span x-text="selectedDept?.members_count"></span> Wanachama
                </p>
            </div>
            <button @click="selectedDept = null"
                    class="w-7 h-7 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors text-base leading-none font-medium">
                ×
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="px-6 py-5 overflow-y-auto max-h-[70vh] space-y-5">

            {{-- Description --}}
            <p class="text-xs text-gray-600 leading-relaxed" x-text="selectedDept?.description || 'Hakuna maelezo.'"></p>

            {{-- Progress --}}
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between text-xs mb-2">
                    <span class="text-gray-500 font-medium">Utekelezaji wa Ratiba ya Mwaka {{ $year }}</span>
                    <span class="font-bold text-gray-900" x-text="(selectedDept?.progress_percent ?? 0) + '%'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 mb-1.5">
                    <div class="bg-green-700 h-1.5 rounded-full" :style="'width: ' + (selectedDept?.progress_percent ?? 0) + '%'"></div>
                </div>
                <p class="text-xs text-gray-400">
                    <span x-text="selectedDept?.completed_schedules"></span> kati ya
                    <span x-text="selectedDept?.total_schedules"></span> malengo yamekamilika
                </p>
            </div>

            {{-- Annual Schedules --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Ratiba ya Mwaka {{ $year }}</h4>
                <template x-if="(selectedDept?.schedules || []).length === 0">
                    <p class="text-xs text-gray-400 italic">Hakuna ratiba iliyoingiwa kwa idara hii kwa mwaka {{ $year }}.</p>
                </template>
                <div class="space-y-2">
                    <template x-for="s in (selectedDept?.schedules || [])" :key="s.id">
                        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 bg-gray-50 gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-xs font-semibold text-gray-400 w-8 flex-shrink-0" x-text="s.month_name?.substring(0,3).toUpperCase()"></span>
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-800 truncate" x-text="s.title"></p>
                                    <p class="text-xs text-gray-400 truncate" x-text="s.description || s.month_name"></p>
                                </div>
                            </div>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-md flex-shrink-0"
                                  :class="s.status === 'completed'
                                      ? 'bg-green-50 text-green-700 border border-green-200'
                                      : s.status === 'skipped'
                                          ? 'bg-gray-100 text-gray-400 border border-gray-200'
                                          : 'bg-amber-50 text-amber-700 border border-amber-200'"
                                  x-text="s.status === 'completed' ? 'Imekamilika' : s.status === 'skipped' ? 'Imeachwa' : 'Inasubiri'">
                            </span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Recent Activities --}}
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Shughuli Zilizorekodiwa</h4>
                <template x-if="(selectedDept?.activities || []).length === 0">
                    <p class="text-xs text-gray-400 italic">Hakuna shughuli zilizorekodiwa bado kwa idara hii.</p>
                </template>
                <div class="space-y-2">
                    <template x-for="a in (selectedDept?.activities || [])" :key="a.id">
                        <div class="p-3 rounded-lg border border-gray-100 bg-gray-50">
                            <div class="flex items-center justify-between mb-1 gap-2">
                                <p class="text-xs font-medium text-gray-800 truncate" x-text="a.title"></p>
                                <span class="text-xs text-gray-400 flex-shrink-0" x-text="a.occurred_at"></span>
                            </div>
                            <p class="text-xs text-gray-500 line-clamp-1" x-text="a.description"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50">
            <button @click="selectedDept = null"
                    class="px-4 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                Funga
            </button>
            @auth
                <a :href="'{{ url('/idara') }}/' + selectedDept?.id + '/maendeleo'"
                   class="px-4 py-2 rounded-lg text-xs font-medium text-white bg-green-700 hover:bg-green-600 transition-colors">
                    Fungua Dashibodi ya Idara
                </a>
            @endauth
        </div>
    </div>
</div>

</body>
</html>
