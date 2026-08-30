@extends('layouts.app')

@section('title', 'Ripoti - '.$department->name)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold">Ripoti - {{ $department->name }}</h1>
            <a href="{{ route('departments.show', $department) }}" class="text-xs text-gray-400 hover:underline">&larr; Rudi kwenye Idara</a>
        </div>
    </div>

    @can('generate', [\App\Models\Report::class, $department])
        <form method="POST" action="{{ route('departments.reports.generate', $department) }}"
              class="bg-white border border-gray-200 rounded-lg p-4 mb-6 flex flex-wrap items-end gap-3"
              x-data="{ type: 'yearly' }">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700">Aina</label>
                <select name="period_type" x-model="type" class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="yearly">Ya Mwaka</option>
                    <option value="monthly">Ya Mwezi</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700">Mwaka</label>
                <input type="number" name="year" value="{{ now()->year }}" class="mt-1 w-24 rounded-md border-gray-300 shadow-sm text-sm">
            </div>
            <div x-show="type === 'monthly'">
                <label class="block text-xs font-medium text-gray-700">Mwezi</label>
                <select name="month" class="mt-1 rounded-md border-gray-300 shadow-sm text-sm">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}">{{ \Illuminate\Support\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-md">
                Zalisha Sasa
            </button>
        </form>
        <p class="text-xs text-gray-400 mb-6 -mt-4">
            Ripoti ya mwaka pia inazalishwa kiotomatiki mwishoni mwa Desemba kila mwaka (angalia routes/console.php).
        </p>
    @endcan

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($reports as $report)
            <div class="flex items-center justify-between px-4 py-3 text-sm">
                <div>
                    <p class="font-medium">{{ $report->period }}</p>
                    <p class="text-xs text-gray-400">Imezalishwa {{ $report->generated_at->translatedFormat('d M Y H:i') }}</p>
                </div>
                <a href="{{ route('departments.reports.download', [$department, $report]) }}" class="text-indigo-600 hover:underline">
                    Pakua PDF
                </a>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-gray-500">Bado hakuna ripoti iliyozalishwa.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $reports->links() }}</div>
@endsection
