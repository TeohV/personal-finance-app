<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FinTrack') }}</title>
    <link rel="stylesheet" href="{{ asset('css/finance.css') }}">
</head>
<body>
    <main class="auth-page">
        <div>
            <div class="auth-brand">
                <a href="{{ url('/') }}">FinTrack</a>
            </div>
            <section class="auth-card">
                {{ $slot }}
            </section>
        </div>
    </main>
</body>
</html>
