<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; font-size: 11px; }
        th { background: #f3f4f6; }
        .rate { font-size: 14px; font-weight: bold; margin-top: 8px; }
    </style>
</head>
<body>
    <h1>Ripoti ya Mwaka {{ $year }} - {{ $department->name }}</h1>
    <p class="muted">Imezalishwa: {{ now()->format('d/m/Y H:i') }}</p>

    @if (!is_null($completionRate))
        <p class="rate">Ratiba iliyotekelezwa: {{ $completionRate }}%</p>
    @endif

    <h3>Ratiba ya Mwaka</h3>
    <table>
        <thead>
            <tr><th>Mwezi</th><th>Kichwa</th><th>Hali</th></tr>
        </thead>
        <tbody>
            @forelse ($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->planned_month }}</td>
                    <td>{{ $schedule->title }}</td>
                    <td>{{ $schedule->status }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Hakuna ratiba iliyopangwa mwaka huu.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>Shughuli Zilizofanyika</h3>
    <table>
        <thead>
            <tr><th>Tarehe</th><th>Kichwa</th><th>Maelezo</th></tr>
        </thead>
        <tbody>
            @forelse ($activityLogs as $log)
                <tr>
                    <td>{{ $log->occurred_at->format('d/m/Y') }}</td>
                    <td>{{ $log->title }}</td>
                    <td>{{ $log->description }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Hakuna shughuli zilizorekodiwa mwaka huu.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
