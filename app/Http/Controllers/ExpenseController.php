<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    /**
     * Display a listing of expenses with stats and filters.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $now = Carbon::now();

        // Calculate Summary Stats for This Month
        $thisMonth = Expense::forUser($userId)
            ->forMonth($now->year, $now->month)
            ->sum('amount');

        // Calculate Summary Stats for Last Month
        $lastMonthDate = $now->copy()->subMonth();
        $lastMonth = Expense::forUser($userId)
            ->forMonth($lastMonthDate->year, $lastMonthDate->month)
            ->sum('amount');

        // Calculate Daily Average (prevent division by zero)
        $daysPassed = max(1, $now->day);
        $avgPerDay = round($thisMonth / $daysPassed, 2);

        // Get Total Count of All Expenses
        $totalCount = Expense::forUser($userId)->count();

        // Fetch Categories for the Dropdown Filter (Only Expense types)
        $categories = Category::where('user_id', $userId)
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        // Build the Query with Search and Filters
        $query = Expense::with(['account', 'category'])
            ->forUser($userId);

        // Search Filter
        if ($request->filled('search')) {
            $query->where('description', 'like', '%'.$request->search.'%');
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Month Filter
        if ($request->filled('month')) {
            $parts = explode('-', $request->month);
            if (count($parts) === 2) {
                $query->forMonth((int) $parts[0], (int) $parts[1]);
            }
        }

        // Paginate the results (10 per page) - ordered by newest first
        $expenses = $query->orderBy('date', 'desc')->paginate(10);

        return view('expenses.index', compact(
            'expenses',
            'thisMonth',
            'lastMonth',
            'avgPerDay',
            'totalCount',
            'categories'
        ));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        $categories = Category::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();
        $accounts = $this->activeAccounts();

        return view('expenses.create', compact('accounts', 'categories'));
    }

    /**
     * Store a newly created expense in the database.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('user_id', Auth::id())
                    ->where('type', 'expense'),
            ],
            'account_id' => $this->activeAccountRule(),
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ], [
            'description.required' => 'Please provide a description for this expense.',
            'amount.required' => 'Please enter the expense amount.',
            'amount.min' => 'The amount must be greater than 0.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'account_id.required' => 'Please select the account used for this expense.',
            'date.required' => 'Please select a date for this expense.',
        ]);

        $validated['user_id'] = Auth::id();

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense recorded successfully!');
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit(Expense $expense)
    {
        // Authorization: Ensure the user owns this expense
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = Category::where('user_id', Auth::id())
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();
        $accounts = $this->activeAccounts($expense->account_id);

        return view('expenses.edit', compact('accounts', 'expense', 'categories'));
    }

    /**
     * Update the specified expense in the database.
     */
    public function update(Request $request, Expense $expense)
    {
        // Authorization: Ensure the user owns this expense
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the incoming request
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('user_id', Auth::id())
                    ->where('type', 'expense'),
            ],
            'account_id' => $this->editableAccountRule($expense->account_id),
            'date' => 'required|date',
            'notes' => 'nullable|string|max:1000',
        ], [
            'description.required' => 'Please provide a description for this expense.',
            'amount.required' => 'Please enter the expense amount.',
            'amount.min' => 'The amount must be greater than 0.',
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',
            'account_id.required' => 'Please select the account used for this expense.',
            'date.required' => 'Please select a date for this expense.',
        ]);

        $expense->update($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Expense updated successfully!');
    }

    /**
     * Remove the specified expense from the database.
     */
    public function destroy(Expense $expense)
    {
        // Authorization: Ensure the user owns this expense
        if ($expense->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Expense deleted successfully!');
    }

    private function activeAccounts(?int $currentAccountId = null)
    {
        return Auth::user()
            ->accounts()
            ->where(function ($query) use ($currentAccountId) {
                $query->where('is_active', true);

                if ($currentAccountId) {
                    $query->orWhereKey($currentAccountId);
                }
            })
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();
    }

    private function activeAccountRule(): array
    {
        return [
            'required',
            Rule::exists('accounts', 'id')
                ->where('user_id', Auth::id())
                ->where('is_active', true),
        ];
    }

    private function editableAccountRule(?int $currentAccountId): array
    {
        return [
            'required',
            Rule::exists('accounts', 'id')
                ->where('user_id', Auth::id())
                ->where(function ($query) use ($currentAccountId) {
                    $query->where('is_active', true);

                    if ($currentAccountId) {
                        $query->orWhere('id', $currentAccountId);
                    }
                }),
        ];
    }
}
