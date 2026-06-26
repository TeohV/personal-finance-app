<?php

namespace App\Http\Controllers;

use App\Models\FinancialGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialGoalController extends Controller
{
    public function index()
    {
        $goals = Auth::user()->financialGoals()->get();

        return view('financial_goals.index', compact('goals'));
    }

    public function create()
    {
        return view('financial_goals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'required|numeric|min:0',
            'target_date' => 'nullable|date|after_or_equal:today',
            'status' => 'required|in:in_progress,completed,cancelled',
        ]);

        Auth::user()->financialGoals()->create($validated);

        return redirect()->route('financial-goals.index')->with('success', 'Financial goal created.');
    }

    public function show(FinancialGoal $financialGoal)
    {
        if ($financialGoal->user_id !== Auth::id()) {
            abort(403);
        }

        return view('financial_goals.show', compact('financialGoal'));
    }

    public function edit(FinancialGoal $financialGoal)
    {
        if ($financialGoal->user_id !== Auth::id()) {
            abort(403);
        }

        return view('financial_goals.edit', compact('financialGoal'));
    }

    public function update(Request $request, FinancialGoal $financialGoal)
    {
        if ($financialGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'required|numeric|min:0',
            'target_date' => 'nullable|date|after_or_equal:today',
            'status' => 'required|in:in_progress,completed,cancelled',
        ]);

        $financialGoal->update($validated);

        return redirect()->route('financial-goals.index')->with('success', 'Financial goal updated.');
    }

    public function destroy(FinancialGoal $financialGoal)
    {
        if ($financialGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $financialGoal->delete();

        return redirect()->route('financial-goals.index')->with('success', 'Financial goal deleted.');
    }
}
