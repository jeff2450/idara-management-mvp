@extends('layouts.app')

@section('title', 'SMS - '.$department->name)

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold">SMS - {{ $department->name }}</h1>
            <a href="{{ route('departments.show', $department) }}" class="text-xs text-gray-400 hover:underline">&larr; Rudi kwenye Idara</a>
        </div>
        @can('create', [\App\Models\SmsLog::class, $department])
            <a href="{{ route('departments.sms.create', $department) }}"
               class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                + Tuma SMS
            </a>
        @endcan
    </div>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @forelse ($logs as $log)
            <div class="px-4 py-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="font-medium">{{ $log->sender->name }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full
                        {{ match($log->status) {
                            'sent' => 'bg-green-100 text-green-700',
                            'partially_sent' => 'bg-yellow-100 text-yellow-700',
                            'failed' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-600',
                        } }}">
                        {{ $log->sent_count }}/{{ $log->recipients_count }} zimefika
                    </span>
                </div>
                <p class="text-gray-600 mt-1">{{ $log->message }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $log->sent_at?->translatedFormat('d M Y H:i') ?? 'Bado kwenye foleni' }}</p>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-gray-500">Bado hakuna SMS iliyotumwa.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $logs->links() }}</div>
@endsection
