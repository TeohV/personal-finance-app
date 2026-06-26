<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Expense;
use App\Models\FinancialGoal;
use App\Models\Income;
use App\Models\MonthlyBudget;
use App\Models\Transfer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $now = Carbon::now();

        // --- Current Month Summary ---
        $totalIncomeThisMonth = Income::where('user_id', $userId)
            ->whereYear('income_date', $now->year)
            ->whereMonth('income_date', $now->month)
            ->sum('amount');

        $totalExpensesThisMonth = Expense::where('user_id', $userId)
            ->whereYear('date', $now->year)
            ->whereMonth('date', $now->month)
            ->sum('amount');

        $netBalance = $totalIncomeThisMonth - $totalExpensesThisMonth;

        // --- Account balances ---
        $accounts = Account::where('user_id', $userId)
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();
        $totalAssets = $accounts->sum(fn (Account $account) => $account->balance);

        // --- All-Time Totals ---
        $totalIncomeAllTime = Income::where('user_id', $userId)->sum('amount');
        $totalExpensesAllTime = Expense::where('user_id', $userId)->sum('amount');

        // --- Recent Transactions (last 5 of each) ---
        $recentExpenses = Expense::with(['account', 'category'])
            ->where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        $recentIncomes = Income::with(['account', 'category'])
            ->where('user_id', $userId)
            ->orderBy('income_date', 'desc')
            ->take(5)
            ->get();

        $recentTransfers = Transfer::with(['fromAccount', 'toAccount'])
            ->where('user_id', $userId)
            ->orderBy('transfer_date', 'desc')
            ->take(5)
            ->get();

        // --- Budget Overview (current month) ---
        $budgets = MonthlyBudget::with('category')
            ->where('user_id', $userId)
            ->whereYear('month_year', $now->year)
            ->whereMonth('month_year', $now->month)
            ->get();

        // --- Financial Goals Summary ---
        $activeGoals = FinancialGoal::where('user_id', $userId)
            ->where('status', 'in_progress')
            ->orderBy('target_date', 'asc')
            ->take(3)
            ->get();

        $completedGoalsCount = FinancialGoal::where('user_id', $userId)
            ->where('status', 'completed')
            ->count();

        return view('dashboard', compact(
            'totalIncomeThisMonth',
            'totalExpensesThisMonth',
            'netBalance',
            'accounts',
            'totalAssets',
            'totalIncomeAllTime',
            'totalExpensesAllTime',
            'recentExpenses',
            'recentIncomes',
            'recentTransfers',
            'budgets',
            'activeGoals',
            'completedGoalsCount',
            'now'
        ));
    }
}
