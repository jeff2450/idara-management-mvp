@extends('layouts.app')

@section('title', $viewMode === 'admin' ? 'Admin Dashboard' : 'Department Dashboard')

@section('header_title')
    @if ($viewMode === 'admin')
        <div class="flex items-center gap-3">
            <div>
                <h1 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    ADMIN DASHBOARD <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">(Admin View)</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Karibu, Admin! Hapa ni muhtasari wa mfumo mzima.</p>
            </div>
            @if ($isAdmin)
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg text-xs font-semibold ml-4 border border-slate-200">
                    <a href="{{ route('dashboard', ['view' => 'admin']) }}" class="px-2.5 py-1 rounded-md transition {{ $viewMode === 'admin' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Admin View</a>
                    <a href="{{ route('dashboard', ['view' => 'leader']) }}" class="px-2.5 py-1 rounded-md transition {{ $viewMode === 'leader' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Leader View</a>
                </div>
            @endif
        </div>
    @else
        <div class="flex items-center gap-3">
            <div>
                <h1 class="text-base font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    DEPARTMENT DASHBOARD <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full">(Leader View - {{ $currentDepartment->name ?? 'Idara ya Vijana' }})</span>
                </h1>
                <p class="text-xs text-slate-500 mt-0.5">Karibu, Kiongozi! Hapa ni muhtasari wa Idara yako.</p>
            </div>
            @if ($isAdmin)
                <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg text-xs font-semibold ml-4 border border-slate-200">
                    <a href="{{ route('dashboard', ['view' => 'admin']) }}" class="px-2.5 py-1 rounded-md transition {{ $viewMode === 'admin' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Admin View</a>
                    <a href="{{ route('dashboard', ['view' => 'leader']) }}" class="px-2.5 py-1 rounded-md transition {{ $viewMode === 'leader' ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Leader View</a>
                </div>
            @endif
        </div>
    @endif
@endsection

@section('search_placeholder', $viewMode === 'admin' ? 'Search anything...' : 'Search members, activities...')
@section('notif_count', $viewMode === 'admin' ? '6' : '3')
@section('mail_count', $viewMode === 'admin' ? '5' : '2')

@section('content')
@php
    $firstDept = $firstDept ?? ($accessibleDepartments->first() ?? null);
@endphp
<div class="space-y-6 max-w-7xl mx-auto">

    @if ($viewMode === 'admin')
        <!-- ============================================================== -->
        <!-- ADMIN VIEW -->
        <!-- ============================================================== -->

        <!-- 5 Top Metric KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Card 1: Total Departments -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:border-emerald-300 transition group">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition flex-shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 22h16M4 18h16M9 18V6l6-4v16M9 10h2M9 14h2M13 10h2M13 14h2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Total Departments</p>
                    <p class="text-2xl font-extrabold text-slate-900 leading-tight mt-0.5">{{ $totalDepartmentsCount }}</p>
                    <p class="text-[10px] font-medium text-slate-400">All departments</p>
                </div>
            </div>

            <!-- Card 2: Total Members -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:border-emerald-300 transition group">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition flex-shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Total Members</p>
                    <p class="text-2xl font-extrabold text-slate-900 leading-tight mt-0.5">{{ number_format($totalMembersCount) }}</p>
                    <p class="text-[10px] font-medium text-slate-400">Across all departments</p>
                </div>
            </div>

            <!-- Card 3: Department Leaders -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:border-emerald-300 transition group">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition flex-shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <polyline points="16 11 18 13 22 9"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Department Leaders</p>
                    <p class="text-2xl font-extrabold text-slate-900 leading-tight mt-0.5">{{ $totalLeadersCount }}</p>
                    <p class="text-[10px] font-medium text-slate-400">Across all departments</p>
                </div>
            </div>

            <!-- Card 4: SMS Sent (This Month) -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:border-emerald-300 transition group">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition flex-shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">SMS Sent (This Month)</p>
                    <p class="text-2xl font-extrabold text-slate-900 leading-tight mt-0.5">{{ number_format($smsThisMonthCount) }}</p>
                    <p class="text-[10px] font-medium text-slate-400">Messages sent</p>
                </div>
            </div>

            <!-- Card 5: Transactions (This Month) -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:border-emerald-300 transition group">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition flex-shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <line x1="2" x2="22" y1="10" y2="10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Transactions (This Month)</p>
                    <p class="text-lg font-extrabold text-slate-900 leading-tight mt-0.5">TZS {{ number_format($transactionsThisMonthSum) }}</p>
                    <p class="text-[10px] font-medium text-slate-400">Total amount</p>
                </div>
            </div>

        </div>

        <!-- 3-Column Content Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Column 1: Departments Overview (5 cols) -->
            <div class="lg:col-span-5 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xs font-bold text-slate-900 tracking-tight uppercase">Departments Overview</h2>
                        <a href="{{ route('departments.index') }}" class="text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full hover:bg-emerald-100 transition">
                            View All
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[11px] font-bold text-slate-400 border-b border-slate-100 pb-2">
                                    <th class="pb-2 font-semibold">Department</th>
                                    <th class="pb-2 font-semibold">Leader</th>
                                    <th class="pb-2 font-semibold">Members</th>
                                    <th class="pb-2 font-semibold">Progress</th>
                                    <th class="pb-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($departmentsOverview as $dept)
                                    <tr class="hover:bg-slate-50/80 transition group">
                                        <td class="py-3 pr-2 font-semibold text-slate-800 flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 flex-shrink-0">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16M4 18h16M9 18V6l6-4v16M9 10h2M9 14h2M13 10h2M13 14h2"/></svg>
                                            </div>
                                            <a href="{{ route('departments.show', $dept['model']) }}" class="hover:text-emerald-700 truncate max-w-[140px]" title="{{ $dept['name'] }}">
                                                {{ $dept['name'] }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-2 text-slate-600 text-[11px] whitespace-nowrap">{{ $dept['leader'] }}</td>
                                        <td class="py-3 px-2 text-slate-700 font-bold text-center">{{ $dept['members'] }}</td>
                                        <td class="py-3 px-2 min-w-[90px]">
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                    <div class="h-full rounded-full {{ $dept['progress_color'] === 'amber' ? 'bg-amber-500' : 'bg-emerald-500' }}" style="width: {{ $dept['progress'] }}%"></div>
                                                </div>
                                                <span class="text-[10px] font-bold text-slate-500">{{ $dept['progress'] }}%</span>
                                            </div>
                                        </td>
                                        <td class="py-3 pl-2 text-right">
                                            <a href="{{ route('departments.show', $dept['model']) }}" class="text-slate-400 hover:text-slate-600">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Column 2: Membership Growth Chart (4 cols) -->
            <div class="lg:col-span-4 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xs font-bold text-slate-900 tracking-tight uppercase">Membership Growth (This Year)</h2>
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <button @click="open = !open" type="button" class="text-[11px] font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-2.5 py-1 rounded-lg flex items-center gap-1 transition">
                                <span>This Year</span>
                                <svg class="w-3 h-3 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="h-60 w-full relative">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Column 3: Recent Activities (3 cols) -->
            <div class="lg:col-span-3 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xs font-bold text-slate-900 tracking-tight uppercase">Recent Activities</h2>
                        <a href="{{ $firstDept ? route('departments.progress', $firstDept) : '#' }}" class="text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full hover:bg-emerald-100 transition">
                            View All
                        </a>
                    </div>

                    <div class="space-y-3.5">
                        @foreach ($adminRecentActivities as $activity)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 flex-shrink-0 mt-0.5">
                                    @if ($activity['type'] === 'member')
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                                    @elseif ($activity['type'] === 'sms')
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    @elseif ($activity['type'] === 'transaction')
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                    @elseif ($activity['type'] === 'department')
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 22h16M4 18h16M9 18V6l6-4v16"/></svg>
                                    @else
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-800 leading-snug">{{ $activity['title'] }}</p>
                                    <p class="text-[11px] text-slate-500 leading-tight mt-0.5 truncate">{{ $activity['subtitle'] }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $activity['time'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <!-- Quick Actions Bar (Bottom Card) -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <h2 class="text-xs font-bold text-slate-900 tracking-tight uppercase mb-3">Quick Actions</h2>
            <div class="flex flex-wrap items-center gap-2.5">
                
                <a href="{{ route('departments.create') }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                    <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold">+</div>
                    <span>Add Department</span>
                </a>

                <a href="{{ route('departments.index') }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                    <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">👤</div>
                    <span>Assign Leader</span>
                </a>

                @if ($firstDept)
                    <a href="{{ route('departments.show', $firstDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                        <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">+</div>
                        <span>Add Member</span>
                    </a>

                    <a href="{{ route('departments.members.import.form', $firstDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition shadow-sm">
                        <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                        <span>Feed Excel/CSV</span>
                    </a>

                    <a href="{{ route('departments.sms.create', $firstDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                        <span class="text-emerald-600 text-sm">💬</span>
                        <span>Send SMS</span>
                    </a>

                    <a href="{{ route('departments.letters.create', $firstDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                        <span class="text-emerald-600 text-sm">📄</span>
                        <span>Generate Letter</span>
                    </a>

                    <a href="{{ route('departments.reports.index', $firstDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                        <span class="text-emerald-600 text-sm">📊</span>
                        <span>View Reports</span>
                    </a>

                    <a href="{{ route('departments.transactions.create', $firstDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                        <span class="text-emerald-600 text-sm">💳</span>
                        <span>Add Transaction</span>
                    </a>
                @endif

            </div>
        </div>

    @else
        <!-- ============================================================== -->
        <!-- LEADER VIEW (Department Scoped) -->
        <!-- ============================================================== -->
        @php
            $currDept = $leaderData['department'] ?? $currentDepartment;
        @endphp

        <!-- 5 Top Metric KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            
            <!-- Card 1: Total Members -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:border-emerald-300 transition group">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition flex-shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Total Members</p>
                    <p class="text-2xl font-extrabold text-slate-900 leading-tight mt-0.5">{{ $leaderData['totalMembers'] ?? 156 }}</p>
                    <p class="text-[10px] font-medium text-slate-400">Active members</p>
                </div>
            </div>

            <!-- Card 2: Upcoming Activities -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:border-emerald-300 transition group">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition flex-shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <line x1="16" x2="16" y1="2" y2="6"/>
                        <line x1="8" x2="8" y1="2" y2="6"/>
                        <line x1="3" x2="21" y1="10" y2="10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Upcoming Activities</p>
                    <p class="text-2xl font-extrabold text-slate-900 leading-tight mt-0.5">{{ $leaderData['upcomingActivitiesCount'] ?? 8 }}</p>
                    <p class="text-[10px] font-medium text-slate-400">This month</p>
                </div>
            </div>

            <!-- Card 3: SMS Sent (This Month) -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:border-emerald-300 transition group">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition flex-shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">SMS Sent (This Month)</p>
                    <p class="text-2xl font-extrabold text-slate-900 leading-tight mt-0.5">{{ $leaderData['smsThisMonth'] ?? 24 }}</p>
                    <p class="text-[10px] font-medium text-slate-400">Messages sent</p>
                </div>
            </div>

            <!-- Card 4: Transactions (This Month) -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:border-emerald-300 transition group">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition flex-shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <line x1="2" x2="22" y1="10" y2="10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Transactions (This Month)</p>
                    <p class="text-lg font-extrabold text-slate-900 leading-tight mt-0.5">TZS {{ number_format($leaderData['transactionsThisMonth'] ?? 450000) }}</p>
                    <p class="text-[10px] font-medium text-slate-400">Total amount</p>
                </div>
            </div>

            <!-- Card 5: Reports Generated -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex items-center gap-3.5 hover:border-emerald-300 transition group">
                <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 group-hover:scale-105 transition flex-shrink-0">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>
                </div>
                <div>
                    <p class="text-[11px] font-semibold text-slate-500">Reports Generated</p>
                    <p class="text-2xl font-extrabold text-slate-900 leading-tight mt-0.5">{{ $leaderData['reportsGeneratedCount'] ?? 3 }}</p>
                    <p class="text-[10px] font-medium text-slate-400">This year</p>
                </div>
            </div>

        </div>

        <!-- 3-Column Content Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Column 1: Members Table (5 cols) -->
            <div class="lg:col-span-5 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xs font-bold text-slate-900 tracking-tight uppercase">Members ({{ $currDept->name ?? 'Idara ya Vijana' }})</h2>
                    </div>

                    <div class="flex items-center gap-2 mb-3.5">
                        <div class="relative flex-1">
                            <input type="text" placeholder="Search member..." class="w-full pl-8 pr-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg text-slate-700 placeholder-slate-400 focus:outline-none focus:border-brand-500">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                        @if ($currDept)
                            <a href="{{ route('departments.show', $currDept) }}" class="flex items-center gap-1 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm transition">
                                <span>+ Add</span>
                            </a>
                            <a href="{{ route('departments.members.import.form', $currDept) }}" class="flex items-center gap-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 text-xs font-bold px-2.5 py-1.5 rounded-lg shadow-sm transition" title="Ingiza kwa Excel au CSV">
                                <svg class="w-3.5 h-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                                <span>Excel/CSV</span>
                            </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="text-[11px] font-bold text-slate-400 border-b border-slate-100 pb-2">
                                    <th class="pb-2 font-semibold">#</th>
                                    <th class="pb-2 font-semibold">Name</th>
                                    <th class="pb-2 font-semibold">Phone</th>
                                    <th class="pb-2 font-semibold">Joined Date</th>
                                    <th class="pb-2 font-semibold text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($leaderData['members'] as $idx => $m)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-2.5 text-slate-400 text-[11px]">{{ $m['id'] }}</td>
                                        <td class="py-2.5 font-semibold text-slate-800 text-[11px] whitespace-nowrap">{{ $m['name'] }}</td>
                                        <td class="py-2.5 text-slate-500 text-[11px]">{{ $m['phone'] }}</td>
                                        <td class="py-2.5 text-slate-500 text-[11px]">{{ $m['joined'] }}</td>
                                        <td class="py-2.5 text-right">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Active
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($currDept)
                    <div class="pt-3 border-t border-slate-100 mt-2 text-left">
                        <a href="{{ route('departments.show', $currDept) }}" class="text-xs font-bold text-emerald-700 hover:underline flex items-center gap-1">
                            <span>View all members</span>
                            <span>&gt;</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Column 2: Upcoming Activities (4 cols) -->
            <div class="lg:col-span-4 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xs font-bold text-slate-900 tracking-tight uppercase">Upcoming Activities</h2>
                        <a href="{{ $currDept ? route('schedules.index', $currDept) : '#' }}" class="text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full hover:bg-emerald-100 transition">
                            View All
                        </a>
                    </div>

                    <div class="space-y-3">
                        @foreach ($leaderData['upcomingActivities'] as $activity)
                            <div class="flex items-start gap-3 p-2.5 rounded-xl border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50/20 transition">
                                <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-200 flex flex-col items-center justify-center text-center flex-shrink-0">
                                    <span class="text-sm font-extrabold text-slate-900 leading-none">{{ $activity['day'] }}</span>
                                    <span class="text-[9px] font-bold text-slate-500 uppercase mt-0.5">{{ $activity['month'] }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-900 leading-snug">{{ $activity['title'] }}</p>
                                    <p class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                        <span>🕒</span>
                                        <span>{{ $activity['time'] }}</span>
                                    </p>
                                    <p class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                        <span>📍</span>
                                        <span>{{ $activity['location'] }}</span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Column 3: Recent Department Activities (3 cols) -->
            <div class="lg:col-span-3 bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xs font-bold text-slate-900 tracking-tight uppercase">Recent Department Activities</h2>
                        <a href="{{ $currDept ? route('departments.progress', $currDept) : '#' }}" class="text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full hover:bg-emerald-100 transition">
                            View All
                        </a>
                    </div>

                    <div class="space-y-3.5">
                        @foreach ($leaderData['recentActivities'] as $activity)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 flex-shrink-0 mt-0.5">
                                    @if ($activity['type'] === 'member')
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    @elseif ($activity['type'] === 'sms')
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    @elseif ($activity['type'] === 'transaction')
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>
                                    @elseif ($activity['type'] === 'schedule')
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                    @else
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-slate-800 leading-snug">{{ $activity['title'] }}</p>
                                    <p class="text-[11px] text-slate-500 leading-tight mt-0.5 truncate">{{ $activity['subtitle'] }}</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $activity['time'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        <!-- Quick Actions Bar (Bottom Card) -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm">
            <h2 class="text-xs font-bold text-slate-900 tracking-tight uppercase mb-3">Quick Actions</h2>
            <div class="flex flex-wrap items-center gap-2.5">
                
                @if ($currDept)
                    <a href="{{ route('departments.show', $currDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                        <div class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-bold">+</div>
                        <span>Add Member</span>
                    </a>

                    <a href="{{ route('departments.members.import.form', $currDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 border border-emerald-300 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition shadow-sm">
                        <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                        <span>Feed Excel/CSV</span>
                    </a>

                    <a href="{{ route('departments.sms.create', $currDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                        <span class="text-emerald-600 text-sm">💬</span>
                        <span>Send SMS</span>
                    </a>

                    <a href="{{ route('departments.transactions.create', $currDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                        <span class="text-emerald-600 text-sm">💳</span>
                        <span>Add Transaction</span>
                    </a>

                    <a href="{{ route('departments.letters.create', $currDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                        <span class="text-emerald-600 text-sm">📄</span>
                        <span>Create Letter</span>
                    </a>

                    <a href="{{ route('schedules.index', $currDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                        <span class="text-emerald-600 text-sm">📅</span>
                        <span>Schedule Activity</span>
                    </a>

                    <a href="{{ route('departments.reports.index', $currDept) }}" class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-slate-700 text-xs font-bold transition shadow-sm">
                        <span class="text-emerald-600 text-sm">📊</span>
                        <span>Generate Report</span>
                    </a>
                @endif

            </div>
        </div>

    @endif

</div>

@if ($viewMode === 'admin')
<!-- Chart.js initialization for Membership Growth -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('growthChart');
        if (!ctx) return;

        const months = @json($chartMonths);
        const values = @json($chartValues);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Members',
                    data: values,
                    borderColor: '#10b981',
                    backgroundColor: function(context) {
                        const chart = context.chart;
                        const {ctx, chartArea} = chart;
                        if (!chartArea) return 'rgba(16, 185, 129, 0.1)';
                        const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
                        gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');
                        return gradient;
                    },
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 8,
                        titleFont: { size: 11, weight: 'bold' },
                        bodyFont: { size: 11 },
                        cornerRadius: 8,
                        displayColors: false,
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 10, weight: '600' },
                            color: '#94a3b8'
                        }
                    },
                    y: {
                        min: 0,
                        max: 500,
                        ticks: {
                            stepSize: 100,
                            font: { size: 10, weight: '600' },
                            color: '#94a3b8'
                        },
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection
