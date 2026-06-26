<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FinancialGoal;
use App\Models\GoalContribution;
use App\Models\Income;
use App\Models\MonthlyBudget;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AllocationController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $targetMonth = $this->targetMonth($validated['month'] ?? null);
        $totalIncome = $this->monthlyIncome($userId, $targetMonth);
        $categories = Category::where('user_id', $userId)
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        $budgets = $categories->map(function (Category $category) use ($userId, $targetMonth) {
            $budget = MonthlyBudget::firstOrNew(
                [
                    'user_id' => $userId,
                    'category_id' => $category->id,
                    'month_year' => $targetMonth->format('Y-m-d'),
                ],
                ['allocated_amount' => 0.00]
            );

            return $budget->setRelation('category', $category);
        });

        $goals = FinancialGoal::where('user_id', $userId)
            ->where('status', 'in_progress')
            ->orderBy('target_date')
            ->get();

        $unallocatedCash = $this->unallocatedCash($userId, $targetMonth);

        return view('allocations.index', compact(
            'targetMonth', 'totalIncome', 'budgets', 'goals', 'unallocatedCash'
        ));
    }

    public function updateBudgets(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'allocations' => 'required|array',
            'allocations.*' => 'required|numeric|min:0',
        ]);

        $allocations = $validated['allocations'];
        $categoryIds = array_keys($allocations);
        $validCategoryCount = Category::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereIn('id', $categoryIds)
            ->count();

        if ($validCategoryCount !== count($categoryIds)) {
            return redirect()->back()->withErrors([
                'allocations' => 'One or more budget categories are invalid.',
            ]);
        }

        $targetMonth = $this->targetMonth($validated['month']);
        $totalRequestedBudget = array_sum($allocations);
        $availableForBudget = $this->monthlyIncome($userId, $targetMonth)
            - $this->monthlyGoalContributions($userId, $targetMonth);

        if ($totalRequestedBudget > $availableForBudget) {
            return redirect()->back()->withErrors([
                'allocations' => 'Your total budget (RM '.number_format($totalRequestedBudget, 2).') cannot exceed your available cash (RM '.number_format($availableForBudget, 2).') after goals.',
            ]);
        }

        foreach ($allocations as $categoryId => $amount) {
            MonthlyBudget::updateOrCreate(
                [
                    'user_id' => $userId,
                    'category_id' => $categoryId,
                    'month_year' => $targetMonth->format('Y-m-d'),
                ],
                ['allocated_amount' => $amount]
            );
        }

        return redirect()->back()->with('success', 'Budgets updated successfully!');
    }

    public function sweepToGoal(Request $request)
    {
        $userId = Auth::id();

        $validated = $request->validate([
            'month' => 'required|date_format:Y-m',
            'financial_goal_id' => [
                'required',
                Rule::exists('financial_goals', 'id')->where('user_id', $userId),
            ],
            'amount' => 'required|numeric|min:0.01',
        ]);

        $goal = FinancialGoal::where('user_id', $userId)
            ->findOrFail($validated['financial_goal_id']);
        $amount = $validated['amount'];
        $targetMonth = $this->targetMonth($validated['month']);

        if ($amount > $goal->remaining_amount) {
            return redirect()->back()->withErrors([
                'amount' => 'You cannot reserve more than the goal\'s remaining balance.',
            ]);
        }

        if ($amount > $this->unallocatedCash($userId, $targetMonth)) {
            return redirect()->back()->withErrors([
                'amount' => 'You do not have enough cash. Available: RM '.number_format(max(0, $this->unallocatedCash($userId, $targetMonth)), 2),
            ]);
        }

        GoalContribution::create([
            'financial_goal_id' => $goal->id,
            'amount' => $amount,
            'date' => $targetMonth,
        ]);

        $goal->increment('current_amount', $amount);

        $goal->refresh();
        if ($goal->current_amount >= $goal->target_amount && $goal->status !== 'completed') {
            $goal->update(['status' => 'completed']);
        }

        return redirect()->back()->with('success', 'Surplus reserved for goal successfully!');
    }

    private function targetMonth(?string $month): Carbon
    {
        return Carbon::createFromFormat('Y-m-d', ($month ?? Carbon::now()->format('Y-m')).'-01')->startOfMonth();
    }

    private function monthlyIncome(int $userId, Carbon $month): float
    {
        return (float) Income::where('user_id', $userId)
            ->whereYear('income_date', $month->year)
            ->whereMonth('income_date', $month->month)
            ->sum('amount');
    }

    private function monthlyGoalContributions(int $userId, Carbon $month): float
    {
        return (float) GoalContribution::whereHas('goal', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->sum('amount');
    }

    private function monthlyAllocatedOrSpent(int $userId, Carbon $month): float
    {
        return MonthlyBudget::where('user_id', $userId)
            ->whereYear('month_year', $month->year)
            ->whereMonth('month_year', $month->month)
            ->get()
            ->sum(fn (MonthlyBudget $budget) => max((float) $budget->allocated_amount, (float) $budget->spent));
    }

    private function unallocatedCash(int $userId, Carbon $month): float
    {
        return $this->monthlyIncome($userId, $month)
            - $this->monthlyAllocatedOrSpent($userId, $month)
            - $this->monthlyGoalContributions($userId, $month);
    }
}
