<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 16px; margin-bottom: 0; }
        h2 { font-size: 13px; margin-top: 25px; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
        .muted { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { text-align: left; padding: 5px 6px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        th { background: #f3f4f6; }
        .total-row td { font-weight: bold; border-top: 2px solid #9ca3af; }
    </style>
</head>
<body>
    <h1>Ripoti ya {{ $department->name }}</h1>
    <p class="muted">Kipindi: {{ $label }} &middot; Imezalishwa: {{ now()->translatedFormat('d F Y H:i') }}</p>

    <h2>Shughuli Zilizofanyika ({{ $activityLogs->count() }})</h2>
    @if ($activityLogs->isEmpty())
        <p class="muted">Hakuna shughuli iliyorekodiwa kwa kipindi hiki.</p>
    @else
        <table>
            <thead>
                <tr><th>Tarehe</th><th>Ratiba Husika</th><th>Maelezo</th></tr>
            </thead>
            <tbody>
                @foreach ($activityLogs as $log)
                    <tr>
                        <td>{{ $log->occurred_at->translatedFormat('d M Y') }}</td>
                        <td>{{ $log->schedule?->title ?? '—' }}</td>
                        <td>{{ $log->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Miamala ya Idara ({{ $transactions->count() }})</h2>
    @if ($transactions->isEmpty())
        <p class="muted">Hakuna muamala uliorekodiwa kwa kipindi hiki.</p>
    @else
        <table>
            <thead>
                <tr><th>Tarehe</th><th>Aina</th><th>Maelezo</th><th>Kiasi</th></tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->occurred_at->translatedFormat('d M Y') }}</td>
                        <td>{{ $transaction->type }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td>{{ number_format((float) $transaction->amount, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3">Jumla</td>
                    <td>{{ number_format((float) $totalAmount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif
</body>
</html>
