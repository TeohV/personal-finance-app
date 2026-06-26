@extends('layouts.app')

@section('title', 'Monthly Allocation')

@section('content')
    @php
        $monthValue = $targetMonth->format('Y-m');
        $prevMonth = $targetMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $targetMonth->copy()->addMonth()->format('Y-m');
        $totalAllocated = $budgets->sum('allocated_amount');
    @endphp

    <div class="page-header">
        <div>
            <h2 class="page-title">{{ $targetMonth->format('F Y') }} allocation</h2>
            <p class="page-subtitle">Plan where this month's income should go.</p>
        </div>
        <div class="button-row">
            <a href="{{ route('allocations.index', ['month' => $prevMonth]) }}" class="btn">Previous</a>
            <a href="{{ route('allocations.index', ['month' => $nextMonth]) }}" class="btn">Next</a>
        </div>
    </div>

    <div class="grid-3">
        <div class="card">
            <div class="stat-label">Income</div>
            <div class="stat-value text-income">RM {{ number_format($totalIncome, 2) }}</div>
        </div>
        <div class="card">
            <div class="stat-label">Budgeted</div>
            <div class="stat-value">RM {{ number_format($totalAllocated, 2) }}</div>
        </div>
        <div class="card">
            <div class="stat-label">Unallocated</div>
            <div class="stat-value {{ $unallocatedCash >= 0 ? 'text-income' : 'text-expense' }}">
                RM {{ number_format($unallocatedCash, 2) }}
            </div>
        </div>
    </div>

    <div class="grid-2" style="margin-top: 18px;">
        <section class="card">
            <h3 class="card-title">Expense budgets</h3>

            <form action="{{ route('allocations.budgets') }}" method="POST">
                @csrf
                <input type="hidden" name="month" value="{{ $monthValue }}">

                <div class="table-wrap" style="box-shadow:none;">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Spent</th>
                                <th>Budget</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($budgets as $budget)
                                @php
                                    $spent = $budget->spent ?? 0;
                                    $allocated = $budget->allocated_amount ?? 0;
                                    $isOver = $spent > $allocated && $allocated > 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $budget->category->name }}</strong>
                                        @if($isOver)
                                            <span class="badge badge-expense">Over</span>
                                        @endif
                                    </td>
                                    <td class="{{ $isOver ? 'text-expense' : '' }}">RM {{ number_format($spent, 2) }}</td>
                                    <td>
                                        <input
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            name="allocations[{{ $budget->category_id }}]"
                                            value="{{ old('allocations.' . $budget->category_id, $allocated) }}"
                                            style="max-width: 140px;"
                                        >
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="empty-state">
                                        No expense categories yet. Create one before planning budgets.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($budgets->isNotEmpty())
                    <div class="form-actions" style="margin-top: 16px;">
                        <button type="submit" class="btn btn-primary">Save budgets</button>
                    </div>
                @endif
            </form>
        </section>

        <section class="card">
            <h3 class="card-title">Reserve surplus for a goal</h3>
            <p class="page-subtitle" style="margin-bottom: 16px;">
                Use this after budgeting. This tracks goal progress without changing account balances.
            </p>

            @if($goals->isEmpty())
                <div class="empty-state">No active goals yet.</div>
            @else
                <form action="{{ route('allocations.sweep') }}" method="POST">
                    @csrf
                    <input type="hidden" name="month" value="{{ $monthValue }}">

                    <div class="field">
                        <label for="financial_goal_id">Goal</label>
                        <select id="financial_goal_id" name="financial_goal_id">
                            <option value="">Select goal</option>
                            @foreach($goals as $goal)
                                <option value="{{ $goal->id }}">
                                    {{ $goal->name }} - RM {{ number_format($goal->remaining_amount, 2) }} remaining
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="amount">Amount (RM)</label>
                        <input
                            id="amount"
                            name="amount"
                            type="number"
                            min="0.01"
                            step="0.01"
                            max="{{ max(0, $unallocatedCash) }}"
                            value="{{ old('amount', max(0, $unallocatedCash)) }}"
                        >
                    </div>

                    <button type="submit" class="btn btn-income" @disabled($unallocatedCash <= 0)>Reserve for goal</button>
                </form>
            @endif
        </section>
    </div>
@endsection
