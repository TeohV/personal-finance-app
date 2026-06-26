<aside class="sidebar">
    <div class="sidebar-brand">
        <strong>FinTrack</strong>
        <span>Personal Finance</span>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Overview</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'nav-link-active' : '' }}">Dashboard</a>

        <div class="nav-section">Money</div>
        <a href="{{ route('accounts.index') }}" class="nav-link {{ request()->routeIs('accounts.*') ? 'nav-link-active' : '' }}">Accounts</a>
        <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'nav-link-active' : '' }}">Expenses</a>
        <a href="{{ route('incomes.index') }}" class="nav-link {{ request()->routeIs('incomes.*') ? 'nav-link-active' : '' }}">Income</a>
        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'nav-link-active' : '' }}">Categories</a>

        <div class="nav-section">Planning</div>
        <a href="{{ route('allocations.index') }}" class="nav-link {{ request()->routeIs('allocations.*') ? 'nav-link-active' : '' }}">Allocations</a>
        <a href="{{ route('financial-goals.index') }}" class="nav-link {{ request()->routeIs('financial-goals.*') ? 'nav-link-active' : '' }}">Financial Goals</a>

        @if (auth()->user()->is_admin)
            <div class="nav-section">Admin</div>
            <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.*') ? 'nav-link-active' : '' }}">Users</a>
        @endif
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('profile.edit') }}" class="user-summary" style="display:block; text-decoration:none;">
            {{ auth()->user()->name }}
            <span>Profile and settings</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-button">Sign out</button>
        </form>
    </div>
</aside>
