<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FinTrack</title>
    <link rel="stylesheet" href="{{ asset('css/finance.css') }}">
</head>
<body>
    <main class="content" style="max-width: 980px; margin: 0 auto;">
        <div class="page-header" style="padding-top: 24px;">
            <div>
                <h1 class="page-title">FinTrack</h1>
                <p class="page-subtitle">A simple personal finance system for tracking accounts, income, expenses, budgets, and savings goals.</p>
            </div>
            <div class="button-row">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn">Log in</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                @endauth
            </div>
        </div>

        <section class="card" style="margin-top: 24px;">
            <h2 class="card-title">Project overview</h2>
            <p class="page-subtitle">
                This application helps users record transactions, organise categories, allocate monthly budgets,
                monitor account balances, and move money between accounts without confusing transfers with income or expenses.
            </p>
        </section>

        <section class="grid-2" style="margin-top: 18px;">
            <div class="card">
                <h3 class="card-title">Track money</h3>
                <p class="page-subtitle">Record income and expenses with categories, accounts, and dates.</p>
            </div>
            <div class="card">
                <h3 class="card-title">Manage accounts</h3>
                <p class="page-subtitle">Use cash, bank, e-wallet, or savings accounts and transfer money between them.</p>
            </div>
            <div class="card">
                <h3 class="card-title">Plan budgets</h3>
                <p class="page-subtitle">Allocate monthly income into expense categories and see what remains.</p>
            </div>
            <div class="card">
                <h3 class="card-title">Save for goals</h3>
                <p class="page-subtitle">Create goals and reserve extra cash toward savings targets.</p>
            </div>
        </section>
    </main>
</body>
</html>
