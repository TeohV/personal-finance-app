<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Auth::user()
            ->incomes()
            ->with(['account', 'category'])
            ->orderBy('income_date', 'desc')
            ->get();

        return view('incomes.index', compact('incomes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('user_id', Auth::id())
                    ->where('type', 'income'),
            ],
            'account_id' => $this->activeAccountRule(),
            'amount' => 'required|numeric|min:0.01',
            'source' => 'required|string|max:255',
            'income_date' => 'required|date',
        ]);

        Auth::user()->incomes()->create($validated);

        return redirect()->route('incomes.index')->with('success', 'Income created.');
    }

    // Add this to show the create form
    public function create()
    {
        $categories = Auth::user()
            ->categories()
            ->where('type', 'income')
            ->orderBy('name')
            ->get();
        $accounts = $this->activeAccounts();

        return view('incomes.create', compact('accounts', 'categories'));
    }

    // Add this to show the edit form
    public function edit(Income $income)
    {
        if ($income->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Auth::user()
            ->categories()
            ->where('type', 'income')
            ->orderBy('name')
            ->get();
        $accounts = $this->activeAccounts($income->account_id);

        return view('incomes.edit', compact('accounts', 'income', 'categories'));
    }

    public function update(Request $request, Income $income)
    {
        if ($income->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')
                    ->where('user_id', Auth::id())
                    ->where('type', 'income'),
            ],
            'account_id' => $this->editableAccountRule($income->account_id),
            'amount' => 'required|numeric|min:0.01',
            'source' => 'required|string|max:255',
            'income_date' => 'required|date',
        ]);

        $income->update($validated);

        return redirect()->route('incomes.index')->with('success', 'Income updated.');
    }

    public function destroy(Income $income)
    {
        if ($income->user_id !== Auth::id()) {
            abort(403);
        }

        $income->delete();

        return redirect()->route('incomes.index')->with('success', 'Income deleted.');
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
