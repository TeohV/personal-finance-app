@extends('layouts.app')

@section('title', 'Admin Users')

@section('content')
    <div class="page-header">
        <div>
            <h2 class="page-title">User management</h2>
            <p class="page-subtitle">Manage users, roles, bans, and basic financial totals.</p>
        </div>
    </div>

    <div class="grid-3">
        <div class="card">
            <div class="stat-label">Users</div>
            <div class="stat-value">{{ $totalUsers }}</div>
        </div>
        <div class="card">
            <div class="stat-label">System income</div>
            <div class="stat-value text-income">RM {{ number_format($totalIncomes, 2) }}</div>
        </div>
        <div class="card">
            <div class="stat-label">System expenses</div>
            <div class="stat-value text-expense">RM {{ number_format($totalExpenses, 2) }}</div>
        </div>
    </div>

    <div class="table-wrap" style="margin-top: 18px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Income</th>
                    <th>Expenses</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <strong>{{ $user->name }}</strong><br>
                            <span class="text-muted">{{ $user->email }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'badge-warning' : 'badge-muted' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $user->is_banned ? 'badge-expense' : 'badge-income' }}">
                                {{ $user->is_banned ? 'Banned' : 'Active' }}
                            </span>
                        </td>
                        <td class="text-income">RM {{ number_format($user->incomes_sum_amount ?? 0, 2) }}</td>
                        <td class="text-expense">RM {{ number_format($user->expenses_sum_amount ?? 0, 2) }}</td>
                        <td>
                            <div class="table-actions">
                                @if($user->role !== 'admin')
                                    <a href="{{ route('admin.user.financials', $user) }}" class="btn btn-sm">Financials</a>
                                @endif

                                @if($user->id !== Auth::id())
                                    <form method="POST" action="{{ route('admin.toggleRole', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm" type="submit">
                                            {{ $user->role === 'admin' ? 'Make user' : 'Make admin' }}
                                        </button>
                                    </form>
                                @endif

                                @if($user->id !== Auth::id() && $user->role !== 'admin')
                                    <form method="POST" action="{{ route('admin.toggleBan', $user) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm {{ $user->is_banned ? 'btn-income' : 'btn-expense' }}" type="submit">
                                            {{ $user->is_banned ? 'Unban' : 'Ban' }}
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty-state">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
