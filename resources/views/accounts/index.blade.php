@extends('layouts.app')

@section('title', 'Accounts')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">Accounts</h2>
            <p class="page-subtitle">Track where your money is stored. Transfers move money between accounts without becoming income or expenses.</p>
        </div>
        <a href="{{ route('accounts.create') }}" class="btn btn-primary">New account</a>
    </div>

    <div class="grid-3">
        <div class="card">
            <div class="stat-label">Total assets</div>
            <div class="stat-value {{ $totalAssets >= 0 ? 'text-income' : 'text-expense' }}">RM {{ number_format($totalAssets, 2) }}</div>
        </div>
        <div class="card">
            <div class="stat-label">Active accounts</div>
            <div class="stat-value">{{ $activeAccounts->count() }}</div>
        </div>
        <div class="card">
            <div class="stat-label">Transfers</div>
            <div class="stat-value">{{ $recentTransfers->count() }}</div>
        </div>
    </div>

    <section class="card" style="margin-top: 18px;">
        <h3 class="card-title">Record transfer</h3>

        @if($activeAccounts->count() < 2)
            <div class="alert alert-error">Create at least two active accounts before recording transfers.</div>
        @endif

        <form method="POST" action="{{ route('transfers.store') }}">
            @csrf
            <div class="form-grid">
                <div class="field">
                    <label for="from_account_id">From</label>
                    <select id="from_account_id" name="from_account_id">
                        <option value="">Select account</option>
                        @foreach($activeAccounts as $account)
                            <option value="{{ $account->id }}" @selected(old('from_account_id') == $account->id)>
                                {{ $account->name }} (RM {{ number_format($account->balance, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('from_account_id') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="to_account_id">To</label>
                    <select id="to_account_id" name="to_account_id">
                        <option value="">Select account</option>
                        @foreach($activeAccounts as $account)
                            <option value="{{ $account->id }}" @selected(old('to_account_id') == $account->id)>{{ $account->name }}</option>
                        @endforeach
                    </select>
                    @error('to_account_id') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="amount">Amount (RM)</label>
                    <input id="amount" name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}">
                    @error('amount') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="transfer_date">Date</label>
                    <input id="transfer_date" name="transfer_date" type="date" value="{{ old('transfer_date', now()->toDateString()) }}">
                    @error('transfer_date') <div class="input-error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="description">Description</label>
                    <input id="description" name="description" type="text" value="{{ old('description') }}" placeholder="Example: ATM withdrawal">
                    @error('description') <div class="input-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" @disabled($activeAccounts->count() < 2)>Save transfer</button>
            </div>
        </form>
    </section>

    <section class="table-wrap" style="margin-top: 18px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Account</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th style="text-align:right;">Opening</th>
                    <th style="text-align:right;">Balance</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $account)
                    <tr>
                        <td><strong>{{ $account->name }}</strong></td>
                        <td><span class="badge badge-muted">{{ $account->type_label }}</span></td>
                        <td>
                            <span class="badge {{ $account->is_active ? 'badge-income' : 'badge-warning' }}">
                                {{ $account->is_active ? 'Active' : 'Archived' }}
                            </span>
                        </td>
                        <td style="text-align:right;">RM {{ number_format($account->opening_balance, 2) }}</td>
                        <td class="{{ $account->balance >= 0 ? 'text-income' : 'text-expense' }}" style="text-align:right;">
                            RM {{ number_format($account->balance, 2) }}
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm">Edit</a>
                                <form method="POST" action="{{ route('accounts.destroy', $account) }}" onsubmit="return confirm('Archive or delete this account?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-expense">{{ $account->hasActivity() ? 'Archive' : 'Delete' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">No accounts yet. Create a cash or bank account to begin.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="card" style="margin-top: 18px;">
        <h3 class="card-title">Recent transfers</h3>

        @if($recentTransfers->isEmpty())
            <div class="empty-state">No transfers recorded yet.</div>
        @else
            <div class="table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Date</th>
                            <th style="text-align:right;">Amount</th>
                            <th style="text-align:right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransfers as $transfer)
                            <tr>
                                <td><strong>{{ $transfer->description ?: 'Transfer' }}</strong></td>
                                <td>{{ $transfer->fromAccount->name ?? 'Deleted account' }}</td>
                                <td>{{ $transfer->toAccount->name ?? 'Deleted account' }}</td>
                                <td>{{ $transfer->transfer_date->format('d M Y') }}</td>
                                <td style="text-align:right;">RM {{ number_format($transfer->amount, 2) }}</td>
                                <td>
                                    <div class="table-actions">
                                        <form method="POST" action="{{ route('transfers.destroy', $transfer) }}" onsubmit="return confirm('Delete this transfer?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-expense">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
