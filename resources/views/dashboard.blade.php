@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Hello, {{ Auth::user()->name }}</h2>
            <p class="page-subtitle">Financial overview for {{ $now->format('F Y') }}.</p>
        </div>
        <div class="button-row">
            <a href="{{ route('accounts.index') }}" class="btn btn-primary">Manage accounts</a>
            <a href="{{ route('incomes.create') }}" class="btn btn-income">Add income</a>
            <a href="{{ route('expenses.create') }}" class="btn btn-expense">Add expense</a>
        </div>
    </div>

    <div class="grid-3">
        <div class="card">
            <div class="stat-label">Total assets</div>
            <div class="stat-value {{ $totalAssets >= 0 ? 'text-income' : 'text-expense' }}">RM {{ number_format($totalAssets, 2) }}</div>
        </div>
        <div class="card">
            <div class="stat-label">Income this month</div>
            <div class="stat-value text-income">RM {{ number_format($totalIncomeThisMonth, 2) }}</div>
        </div>
        <div class="card">
            <div class="stat-label">Expenses this month</div>
            <div class="stat-value text-expense">RM {{ number_format($totalExpensesThisMonth, 2) }}</div>
        </div>
        <div class="card">
            <div class="stat-label">Monthly surplus</div>
            <div class="stat-value {{ $netBalance >= 0 ? 'text-income' : 'text-expense' }}">
                RM {{ number_format($netBalance, 2) }}
            </div>
        </div>
    </div>

    <div class="button-row" style="margin: 18px 0 24px;">
        <a href="{{ route('categories.index') }}" class="btn">Manage categories</a>
        <a href="{{ route('allocations.index') }}" class="btn">Plan month</a>
        <a href="{{ route('financial-goals.index') }}" class="btn">View goals</a>
    </div>

    <div class="grid-2">
        <section class="card">
            <h3 class="card-title">Accounts</h3>
            @forelse($accounts as $account)
                <div style="display:flex; justify-content:space-between; gap:12px; margin-bottom:12px;">
                    <div>
                        <strong>{{ $account->name }}</strong><br>
                        <span class="text-muted">{{ $account->type_label }}{{ $account->is_active ? '' : ' - archived' }}</span>
                    </div>
                    <strong class="{{ $account->balance >= 0 ? 'text-income' : 'text-expense' }}">
                        RM {{ number_format($account->balance, 2) }}
                    </strong>
                </div>
            @empty
                <div class="empty-state">No accounts yet. Add a cash or bank account to track assets.</div>
            @endforelse
        </section>

        <section class="card">
            <h3 class="card-title">Recent activity</h3>

            @if($recentIncomes->isEmpty() && $recentExpenses->isEmpty())
                <div class="empty-state">No transactions yet. Add income or expenses to see activity here.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Date</th>
                                <th style="text-align:right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentExpenses as $expense)
                                <tr>
                                    <td>
                                        <strong>{{ $expense->description }}</strong><br>
                                        <span class="text-muted">{{ $expense->category->name ?? 'Uncategorized' }} - {{ $expense->account->name ?? 'No account' }}</span>
                                    </td>
                                    <td>{{ $expense->date->format('d M Y') }}</td>
                                    <td class="text-expense" style="text-align:right;">-RM {{ number_format($expense->amount, 2) }}</td>
                                </tr>
                            @endforeach
                            @foreach($recentIncomes as $income)
                                <tr>
                                    <td>
                                        <strong>{{ $income->source }}</strong><br>
                                        <span class="text-muted">{{ $income->category->name ?? 'Uncategorized' }} - {{ $income->account->name ?? 'No account' }}</span>
                                    </td>
                                    <td>{{ $income->income_date->format('d M Y') }}</td>
                                    <td class="text-income" style="text-align:right;">+RM {{ number_format($income->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="card">
            <h3 class="card-title">Recent transfers</h3>
            @if($recentTransfers->isEmpty())
                <div class="empty-state">No transfers yet. Use this for bank withdrawals or moving money to savings.</div>
            @else
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Transfer</th>
                                <th>Date</th>
                                <th style="text-align:right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransfers as $transfer)
                                <tr>
                                    <td>
                                        <strong>{{ $transfer->description ?: 'Transfer' }}</strong><br>
                                        <span class="text-muted">{{ $transfer->fromAccount->name ?? 'Deleted account' }} to {{ $transfer->toAccount->name ?? 'Deleted account' }}</span>
                                    </td>
                                    <td>{{ $transfer->transfer_date->format('d M Y') }}</td>
                                    <td style="text-align:right;">RM {{ number_format($transfer->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="card">
            <h3 class="card-title">Current budgets</h3>
            @forelse($budgets as $budget)
                <div style="margin-bottom: 16px;">
                    <div style="display:flex; justify-content:space-between; gap:12px; margin-bottom:6px;">
                        <strong>{{ $budget->category->name ?? 'Category' }}</strong>
                        <span class="text-muted">RM {{ number_format($budget->spent, 2) }} / RM {{ number_format($budget->allocated_amount, 2) }}</span>
                    </div>
                    <div class="progress">
                        <div class="progress-fill" style="width: {{ $budget->usage_percentage }}%; background: {{ $budget->is_exceeded ? 'var(--expense)' : 'var(--income)' }};"></div>
                    </div>
                    @if($budget->is_exceeded)
                        <div class="error-msg">Over budget by RM {{ number_format($budget->spent - $budget->allocated_amount, 2) }}</div>
                    @endif
                </div>
            @empty
                <div class="empty-state">No budgets set for this month.</div>
            @endforelse
        </section>
    </div>

    <section class="card">
        <h3 class="card-title">Active goals</h3>
        @forelse($activeGoals as $goal)
            <div style="margin-bottom: 16px;">
                <div style="display:flex; justify-content:space-between; gap:12px; margin-bottom:6px;">
                    <strong>{{ $goal->name }}</strong>
                    <span class="text-muted">RM {{ number_format($goal->current_amount, 2) }} / RM {{ number_format($goal->target_amount, 2) }}</span>
                </div>
                <div class="progress">
                    <div class="progress-fill" style="width: {{ $goal->progress_percentage }}%;"></div>
                </div>
            </div>
        @empty
            <div class="empty-state">No active goals yet.</div>
        @endforelse
    </section>
@endsection
