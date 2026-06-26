<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Prepared by: Lim Chi Chung 2302502
class AdminController extends Controller
{
    /**
     * Display the admin user management page with financial overview.
     */
    public function index()
    {
        $users = User::withCount(['expenses', 'incomes'])
            ->withSum('expenses', 'amount')
            ->withSum('incomes', 'amount')
            ->orderBy('created_at', 'desc')
            ->get();

        // System-wide stats for the overview cards
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $totalExpenses = Expense::sum('amount');
        $totalIncomes = Income::sum('amount');
        $bannedUsers = User::where('is_banned', true)->count();

        return view('admin.users', compact(
            'users',
            'totalUsers',
            'totalExpenses',
            'totalIncomes',
            'bannedUsers'
        ));
    }

    /**
     * Delete a user and all their associated data.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting other admins
        if ($user->role === 'admin') {
            return back()->with('error', 'You cannot delete another admin account.');
        }

        $user->delete();

        return back()->with('success', "User \"{$user->name}\" has been deleted.");
    }

    /**
     * Promote a user to admin or demote an admin to user.
     */
    public function toggleRole($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from changing their own role
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->role = ($user->role === 'admin') ? 'user' : 'admin';
        $user->save();

        $action = $user->role === 'admin' ? 'promoted to Admin' : 'demoted to User';

        return back()->with('success', "\"{$user->name}\" has been {$action}.");
    }

    /**
     * Ban or unban a user.
     */
    public function toggleBan($id)
    {
        $user = User::findOrFail($id);

        // Prevent admin from banning themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot ban your own account.');
        }

        // Prevent banning other admins
        if ($user->role === 'admin') {
            return back()->with('error', 'You cannot ban an admin account.');
        }

        $user->is_banned = ! $user->is_banned;
        $user->save();

        $action = $user->is_banned ? 'banned' : 'unbanned';

        return back()->with('success', "\"{$user->name}\" has been {$action}.");
    }

    /**
     * Show detailed financial overview for a specific user.
     */
    public function showFinancials($id)
    {
        $user = User::findOrFail($id);

        $expenses = Expense::where('user_id', $id)
            ->with(['account', 'category'])
            ->orderBy('date', 'desc')
            ->get();

        $incomes = Income::where('user_id', $id)
            ->with(['account', 'category'])
            ->orderBy('income_date', 'desc')
            ->get();

        $accounts = Account::where('user_id', $id)
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $recentTransfers = Transfer::with(['fromAccount', 'toAccount'])
            ->where('user_id', $id)
            ->orderBy('transfer_date', 'desc')
            ->take(5)
            ->get();

        $totalExpenses = $expenses->sum('amount');
        $totalIncomes = $incomes->sum('amount');
        $totalAssets = $accounts->sum(fn (Account $account) => $account->balance);

        return view('admin.user-financials', compact(
            'user',
            'accounts',
            'recentTransfers',
            'expenses',
            'incomes',
            'totalExpenses',
            'totalIncomes',
            'totalAssets'
        ));
    }
}
