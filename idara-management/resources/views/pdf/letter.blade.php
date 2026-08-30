<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #1f2937; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-size: 16px; margin: 0; }
        .header p { color: #6b7280; margin: 2px 0; }
        .body { white-space: pre-line; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>{{ $department->name }}</p>
    </div>

    <div class="body">{!! $body !!}</div>
</body>
</html>
