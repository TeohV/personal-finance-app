<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FinTrack') }} - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/finance.css') }}">
</head>
<body>
    <div class="app-shell">
        @include('partials.sidebar')

        <div class="main-panel">
            <header class="topbar">
                <h1 class="topbar-title">@yield('title', 'Dashboard')</h1>
                <div class="button-row">
                    @yield('topbar-actions')
                </div>
            </header>

            <main class="content">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-error">
                        {{ $errors->first() }}
                    </div>
                @endif

                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
