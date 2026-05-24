<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function store(Request $request, Group $group): RedirectResponse|JsonResponse
    {
        abort_unless($group->members()->whereKey($request->user()->id)->exists(), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'expense_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:800'],
            'split_type' => ['required', 'in:equal,percentage,custom'],
            'payers' => ['required', 'array', 'min:1'],
            'payers.*.user_id' => ['required', 'exists:users,id'],
            'payers.*.amount' => ['required', 'numeric', 'min:0.01'],
            'participants' => ['required', 'array', 'min:1'],
            'participants.*.user_id' => ['required', 'exists:users,id'],
            'participants.*.value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $memberIds = $group->members()->pluck('users.id')->all();
        $this->ensureUsersBelongToGroup($memberIds, collect($data['payers'])->pluck('user_id')->all());
        $this->ensureUsersBelongToGroup($memberIds, collect($data['participants'])->pluck('user_id')->all());

        $payerTotal = collect($data['payers'])->sum(fn ($payer) => (float) $payer['amount']);
        abort_if(abs($payerTotal - (float) $data['amount']) > 0.01, 422, 'Payer total must match the expense amount.');

        $splits = $this->buildSplits($data['participants'], (float) $data['amount'], $data['split_type']);

        $expense = DB::transaction(function () use ($request, $group, $data, $splits) {
            $expense = $group->expenses()->create([
                'created_by' => $request->user()->id,
                'category_id' => $data['category_id'] ?? null,
                'title' => $data['title'],
                'amount' => $data['amount'],
                'split_type' => $data['split_type'],
                'expense_date' => $data['expense_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['payers'] as $payer) {
                $expense->payers()->create([
                    'user_id' => $payer['user_id'],
                    'paid_amount' => $payer['amount'],
                ]);
            }

            foreach ($splits as $split) {
                $expense->splits()->create($split);
            }

            return $expense;
        });

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Expense added.', 'expense' => $expense->load('payers.user', 'splits.user')], 201);
        }

        return back()->with('status', 'Expense added.');
    }

    private function ensureUsersBelongToGroup(array $memberIds, array $submittedIds): void
    {
        abort_if(count(array_diff($submittedIds, $memberIds)) > 0, 422, 'Every selected user must belong to this group.');
    }

    private function buildSplits(array $participants, float $amount, string $type): array
    {
        if ($type === 'equal') {
            $share = round($amount / count($participants), 2);
            $splits = collect($participants)->map(fn ($participant) => [
                'user_id' => $participant['user_id'],
                'share_amount' => $share,
                'percentage' => null,
            ])->all();

            $difference = round($amount - collect($splits)->sum('share_amount'), 2);
            $splits[array_key_last($splits)]['share_amount'] += $difference;

            return $splits;
        }

        if ($type === 'percentage') {
            $totalPercent = collect($participants)->sum(fn ($participant) => (float) ($participant['value'] ?? 0));
            abort_if(abs($totalPercent - 100) > 0.01, 422, 'Percentages must total 100.');

            return collect($participants)->map(fn ($participant) => [
                'user_id' => $participant['user_id'],
                'share_amount' => round($amount * ((float) $participant['value'] / 100), 2),
                'percentage' => $participant['value'],
            ])->all();
        }

        $totalCustom = collect($participants)->sum(fn ($participant) => (float) ($participant['value'] ?? 0));
        abort_if(abs($totalCustom - $amount) > 0.01, 422, 'Custom shares must match the expense amount.');

        return collect($participants)->map(fn ($participant) => [
            'user_id' => $participant['user_id'],
            'share_amount' => $participant['value'],
            'percentage' => null,
        ])->all();
    }
}
