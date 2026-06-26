<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Auth::user()
            ->accounts()
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        $activeAccounts = $accounts->where('is_active', true);
        $totalAssets = $accounts->sum(fn (Account $account) => $account->balance);

        $recentTransfers = Auth::user()
            ->transfers()
            ->with(['fromAccount', 'toAccount'])
            ->orderBy('transfer_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        return view('accounts.index', compact(
            'accounts',
            'activeAccounts',
            'totalAssets',
            'recentTransfers'
        ));
    }

    public function create()
    {
        $types = Account::types();

        return view('accounts.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        Auth::user()->accounts()->create($validated);

        return redirect()->route('accounts.index')->with('success', 'Account created.');
    }

    public function edit(Account $account)
    {
        $this->authorizeAccount($account);

        $types = Account::types();

        return view('accounts.edit', compact('account', 'types'));
    }

    public function update(Request $request, Account $account)
    {
        $this->authorizeAccount($account);

        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active');

        $account->update($validated);

        return redirect()->route('accounts.index')->with('success', 'Account updated.');
    }

    public function destroy(Account $account)
    {
        $this->authorizeAccount($account);

        if ($account->hasActivity()) {
            $account->update(['is_active' => false]);

            return redirect()->route('accounts.index')->with('success', 'Account archived because it has transaction history.');
        }

        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Account deleted.');
    }

    private function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(array_keys(Account::types()))],
            'opening_balance' => 'required|numeric|min:0|max:9999999.99',
        ];
    }

    private function authorizeAccount(Account $account): void
    {
        if ($account->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
