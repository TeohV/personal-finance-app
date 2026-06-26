@extends('layouts.app')

@section('title', 'User Financials')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">{{ $user->name }} financial overview</h2>
            <p class="page-subtitle">{{ $user->email }} - registered {{ $user->created_at->format('d M Y') }}</p>
        </div>
        <a href="{{ route('admin.users') }}" class="btn">Back to users</a>
    </div>

    <div class="grid-3">
        <div class="card">
            <div class="stat-label">Total assets</div>
            <div class="stat-value {{ $totalAssets >= 0 ? 'text-income' : 'text-expense' }}">
                RM {{ number_format($totalAssets, 2) }}
            </div>
        </div>
        <div class="card">
            <div class="stat-label">Total income</div>
            <div class="stat-value text-income">RM {{ number_format($totalIncomes, 2) }}</div>
        </div>
        <div class="card">
            <div class="stat-label">Total expenses</div>
            <div class="stat-value text-expense">RM {{ number_format($totalExpenses, 2) }}</div>
        </div>
    </div>

    <div class="grid-2" style="margin-top: 18px;">
        <section class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th colspan="4">Accounts</th>
                    </tr>
                    <tr>
                        <th>Account</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th style="text-align:right;">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accounts as $account)
                        <tr>
                            <td>{{ $account->name }}</td>
                            <td>{{ $account->type_label }}</td>
                            <td>{{ $account->is_active ? 'Active' : 'Archived' }}</td>
                            <td class="{{ $account->balance >= 0 ? 'text-income' : 'text-expense' }}" style="text-align:right;">
                                RM {{ number_format($account->balance, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state">No accounts recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th colspan="4">Recent transfers</th>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <th>From</th>
                        <th>To</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransfers as $transfer)
                        <tr>
                            <td>{{ $transfer->description ?: 'Transfer' }}</td>
                            <td>{{ $transfer->fromAccount->name ?? 'Deleted account' }}</td>
                            <td>{{ $transfer->toAccount->name ?? 'Deleted account' }}</td>
                            <td style="text-align:right;">RM {{ number_format($transfer->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state">No transfers recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th colspan="5">Expenses</th>
                    </tr>
                    <tr>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Account</th>
                        <th>Date</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->description }}</td>
                            <td>{{ $expense->category->name ?? 'Uncategorized' }}</td>
                            <td>{{ $expense->account->name ?? 'No account' }}</td>
                            <td>{{ $expense->date->format('d M Y') }}</td>
                            <td class="text-expense" style="text-align:right;">RM {{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-state">No expenses recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <section class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th colspan="5">Income</th>
                    </tr>
                    <tr>
                        <th>Source</th>
                        <th>Category</th>
                        <th>Account</th>
                        <th>Date</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomes as $income)
                        <tr>
                            <td>{{ $income->source }}</td>
                            <td>{{ $income->category->name ?? 'Uncategorized' }}</td>
                            <td>{{ $income->account->name ?? 'No account' }}</td>
                            <td>{{ $income->income_date->format('d M Y') }}</td>
                            <td class="text-income" style="text-align:right;">RM {{ number_format($income->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="empty-state">No income recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>
@endsection
