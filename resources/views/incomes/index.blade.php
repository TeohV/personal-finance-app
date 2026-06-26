@extends('layouts.app')

@section('title', 'Income')

@section('content')
    @php
        $total = $incomes->sum('amount');
        $thisMonth = $incomes->filter(fn($income) => \Carbon\Carbon::parse($income->income_date)->isCurrentMonth())->sum('amount');
    @endphp

    <div class="page-header">
        <div>
            <h2 class="page-title">Income</h2>
            <p class="page-subtitle">Record salary, freelance work, allowance, or other income.</p>
        </div>
        <a href="{{ route('incomes.create') }}" class="btn btn-income">Add income</a>
    </div>

    <div class="grid-3">
        <div class="card">
            <div class="stat-label">Total income</div>
            <div class="stat-value text-income">RM {{ number_format($total, 2) }}</div>
        </div>
        <div class="card">
            <div class="stat-label">This month</div>
            <div class="stat-value text-income">RM {{ number_format($thisMonth, 2) }}</div>
        </div>
        <div class="card">
            <div class="stat-label">Entries</div>
            <div class="stat-value">{{ $incomes->count() }}</div>
        </div>
    </div>

    <div class="table-wrap" style="margin-top: 18px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Source</th>
                    <th>Category</th>
                    <th>Account</th>
                    <th>Date</th>
                    <th style="text-align:right;">Amount</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incomes as $income)
                    <tr>
                        <td><strong>{{ $income->source }}</strong></td>
                        <td><span class="badge badge-income">{{ $income->category->name ?? 'Uncategorized' }}</span></td>
                        <td>{{ $income->account->name ?? 'No account' }}</td>
                        <td>{{ $income->income_date->format('d M Y') }}</td>
                        <td class="text-income" style="text-align:right;">RM {{ number_format($income->amount, 2) }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('incomes.edit', $income) }}" class="btn btn-sm">Edit</a>
                                <form action="{{ route('incomes.destroy', $income) }}" method="POST" onsubmit="return confirm('Delete this income entry?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-expense">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">No income recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
