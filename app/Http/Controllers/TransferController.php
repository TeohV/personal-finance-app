<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TransferController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_account_id' => ['required', $this->activeAccountRule()],
            'to_account_id' => ['required', 'different:from_account_id', $this->activeAccountRule()],
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'transfer_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ], [
            'to_account_id.different' => 'Choose two different accounts for a transfer.',
        ]);

        $fromAccount = Auth::user()
            ->accounts()
            ->whereKey($validated['from_account_id'])
            ->firstOrFail();

        if ($fromAccount->balance < (float) $validated['amount']) {
            throw ValidationException::withMessages([
                'amount' => 'The source account does not have enough balance for this transfer.',
            ]);
        }

        Auth::user()->transfers()->create($validated);

        return redirect()->route('accounts.index')->with('success', 'Transfer recorded.');
    }

    public function destroy(Transfer $transfer)
    {
        if ($transfer->user_id !== Auth::id()) {
            abort(403);
        }

        $transfer->delete();

        return redirect()->route('accounts.index')->with('success', 'Transfer deleted.');
    }

    private function activeAccountRule()
    {
        return Rule::exists('accounts', 'id')
            ->where('user_id', Auth::id())
            ->where('is_active', true);
    }
}
