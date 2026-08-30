@extends('layouts.app')

@section('title', 'Ingiza Wanachama kwa Excel/CSV - ' . $department->name)

@section('header_title')
    <div class="flex items-center gap-2">
        <a href="{{ route('departments.show', $department) }}" class="text-slate-400 hover:text-slate-600 text-xs font-semibold">&larr; {{ $department->name }}</a>
        <span class="text-slate-300">/</span>
        <h1 class="text-base font-extrabold text-slate-900 tracking-tight">INGIZA WANACHAMA KWA EXCEL / CSV</h1>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-600 shadow-sm flex-shrink-0">
                <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="8" y1="13" x2="16" y2="13"/>
                    <line x1="8" y1="17" x2="16" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Ingiza Orodha ya Wanachama kwa Mara Moja</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Idara inayolengwa: <span class="font-bold text-slate-700">{{ $department->name }}</span>. Pakia faili la Excel (.xlsx) au CSV (.csv) lenye majina na namba za simu.
                </p>
            </div>
        </div>

        <div>
            <a href="{{ route('departments.members.template', $department) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold border border-emerald-200 shadow-sm transition">
                <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                <span>Pakua Mfano wa Excel (Template)</span>
            </a>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="bg-gradient-to-r from-emerald-50/60 to-slate-50 border border-emerald-100 rounded-2xl p-5 shadow-sm">
        <h3 class="text-xs font-bold text-emerald-900 uppercase tracking-wider mb-2 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Muundo wa Safuwima (Columns) za Faili la Excel / CSV
        </h3>
        <p class="text-xs text-slate-600 mb-3">
            Hakikisha faili lako la Excel au CSV lina safuwima (headers) zifuatazo kwenye mstari wa kwanza:
        </p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-xs">
            <div class="bg-white p-3 rounded-xl border border-slate-200">
                <span class="font-bold text-slate-900 block mb-0.5">1. Jina Kamili (Name)</span>
                <span class="text-emerald-700 font-semibold text-[11px] block">Inahitajika (Required)</span>
                <span class="text-slate-400 text-[10px]">Mfano: Michael John, Neema Paul</span>
            </div>
            <div class="bg-white p-3 rounded-xl border border-slate-200">
                <span class="font-bold text-slate-900 block mb-0.5">2. Namba ya Simu (Phone)</span>
                <span class="text-slate-500 font-semibold text-[11px] block">Hiari (Optional)</span>
                <span class="text-slate-400 text-[10px]">Mfano: 0712345678, 255767123456</span>
            </div>
            <div class="bg-white p-3 rounded-xl border border-slate-200">
                <span class="font-bold text-slate-900 block mb-0.5">3. Barua Pepe (Email)</span>
                <span class="text-slate-500 font-semibold text-[11px] block">Hiari (Optional)</span>
                <span class="text-slate-400 text-[10px]">Isipowekwa, mfumo utatengeneza yenyewe</span>
            </div>
        </div>
    </div>

    <!-- Upload Form Card -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm">
        <form method="POST" action="{{ route('departments.members.import', $department) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- File Upload Area -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Chagua Faili la Excel (.xlsx) au CSV (.csv)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-2xl hover:border-emerald-500 bg-slate-50/50 transition"
                     x-data="{ fileName: '' }">
                    <div class="space-y-2 text-center">
                        <div class="w-12 h-12 mx-auto rounded-full bg-emerald-100/70 text-emerald-700 flex items-center justify-center">
                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                        </div>
                        <div class="flex text-xs text-slate-600 justify-center items-center gap-1">
                            <label for="file" class="relative cursor-pointer bg-white rounded-lg px-3 py-1 font-bold text-emerald-600 hover:text-emerald-700 border border-slate-200 shadow-sm focus-within:outline-none">
                                <span>Tafuta faili kwenye kifaa</span>
                                <input id="file" name="file" type="file" accept=".xlsx,.xls,.csv,.txt" required class="sr-only"
                                       @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                            </label>
                        </div>
                        <p class="text-[11px] text-slate-400">Inasaidia .xlsx, .csv, .xls, .txt (Hadi 10MB)</p>
                        <template x-if="fileName">
                            <p class="text-xs font-bold text-emerald-700 bg-emerald-50 py-1.5 px-3 rounded-lg inline-block border border-emerald-200 mt-2" x-text="'Faili lililochaguliwa: ' + fileName"></p>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Role Selection -->
            <div class="max-w-md">
                <label for="role" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nafasi ya Wanachama Hawa kwenye Idara</label>
                <select id="role" name="role" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs focus:outline-none focus:border-brand-500 bg-slate-50">
                    <option value="member" selected>Mwanachama (Member)</option>
                    @if (auth()->user()->isAdmin())
                        <option value="leader">Kiongozi wa Idara (Leader)</option>
                    @endif
                </select>
                @unless (auth()->user()->isAdmin())
                    <p class="text-[10px] text-slate-400 mt-1">Uteuzi wa Kiongozi unahitaji akaunti ya Admin.</p>
                @endunless
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('departments.show', $department) }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
                    Ghairi (Rudi Nyuma)
                </a>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-6 py-3 rounded-xl shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <span>Ingiza Wanachama Kwenye Idara</span>
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
