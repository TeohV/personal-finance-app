@extends('layouts.app')

@section('title', 'Expenses')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Expenses</h2>
            <p class="page-subtitle">Track and manage your spending.</p>
        </div>
        <a href="{{ route('expenses.create') }}" class="btn btn-expense">New expense</a>
    </div>

    <div class="grid-3">
        <div class="card">
            <div class="stat-label">This month</div>
            <div class="stat-value text-expense">RM {{ number_format($thisMonth, 2) }}</div>
        </div>
        <div class="card">
            <div class="stat-label">Last month</div>
            <div class="stat-value">RM {{ number_format($lastMonth, 2) }}</div>
        </div>
        <div class="card">
            <div class="stat-label">Daily average</div>
            <div class="stat-value">RM {{ number_format($avgPerDay, 2) }}</div>
        </div>
    </div>

    <form method="GET" action="{{ route('expenses.index') }}" class="card" style="margin: 18px 0;">
        <div class="form-grid">
            <div class="field">
                <label for="search">Search</label>
                <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Description">
            </div>
            <div class="field">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="">All categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="month">Month</label>
                <input id="month" type="month" name="month" value="{{ request('month') }}">
            </div>
        </div>
        <div class="button-row">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('expenses.index') }}" class="btn">Clear</a>
        </div>
    </form>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Category</th>
                    <th>Account</th>
                    <th>Date</th>
                    <th style="text-align:right;">Amount</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td><strong>{{ $expense->description }}</strong></td>
                        <td><span class="badge badge-expense">{{ $expense->category->name ?? 'Uncategorized' }}</span></td>
                        <td>{{ $expense->account->name ?? 'No account' }}</td>
                        <td>{{ $expense->date->format('d M Y') }}</td>
                        <td class="text-expense" style="text-align:right;">RM {{ number_format($expense->amount, 2) }}</td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm">Edit</a>
                                <form action="{{ route('expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Delete this expense?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-expense">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">No expenses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">{{ $expenses->links() }}</div>
@endsection
