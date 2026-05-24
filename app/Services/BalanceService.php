<?php

namespace App\Services;

use App\Models\Group;

class BalanceService
{
    public function summary(Group $group): array
    {
        $group->loadMissing([
            'members:id,name,email',
            'expenses.payers.user:id,name,email',
            'expenses.splits.user:id,name,email',
            'settlements.payer:id,name,email',
            'settlements.receiver:id,name,email',
        ]);

        $people = $group->members->keyBy('id');
        $ledger = $people->mapWithKeys(fn ($user) => [$user->id => 0.0])->all();
        $paid = $people->mapWithKeys(fn ($user) => [$user->id => 0.0])->all();
        $share = $people->mapWithKeys(fn ($user) => [$user->id => 0.0])->all();

        foreach ($group->expenses as $expense) {
            foreach ($expense->payers as $payer) {
                $amount = (float) $payer->paid_amount;
                $ledger[$payer->user_id] = ($ledger[$payer->user_id] ?? 0) + $amount;
                $paid[$payer->user_id] = ($paid[$payer->user_id] ?? 0) + $amount;
            }

            foreach ($expense->splits as $split) {
                $amount = (float) $split->share_amount;
                $ledger[$split->user_id] = ($ledger[$split->user_id] ?? 0) - $amount;
                $share[$split->user_id] = ($share[$split->user_id] ?? 0) + $amount;
            }
        }

        foreach ($group->settlements as $settlement) {
            $amount = (float) $settlement->amount;
            $ledger[$settlement->paid_by] = ($ledger[$settlement->paid_by] ?? 0) + $amount;
            $ledger[$settlement->paid_to] = ($ledger[$settlement->paid_to] ?? 0) - $amount;
        }

        $balances = collect($ledger)->map(function ($balance, $userId) use ($people, $paid, $share) {
            $user = $people->get((int) $userId);

            return [
                'user_id' => (int) $userId,
                'name' => $user?->name ?? 'Unknown',
                'email' => $user?->email,
                'paid' => round($paid[$userId] ?? 0, 2),
                'share' => round($share[$userId] ?? 0, 2),
                'balance' => round($balance, 2),
            ];
        })->values();

        return [
            'total' => round((float) $group->expenses->sum('amount'), 2),
            'balances' => $balances,
            'simplified' => $this->simplify($balances->all()),
        ];
    }

    private function simplify(array $balances): array
    {
        $debtors = collect($balances)
            ->filter(fn ($row) => $row['balance'] < -0.009)
            ->map(fn ($row) => ['user_id' => $row['user_id'], 'name' => $row['name'], 'amount' => abs($row['balance'])])
            ->sortByDesc('amount')
            ->values();

        $creditors = collect($balances)
            ->filter(fn ($row) => $row['balance'] > 0.009)
            ->map(fn ($row) => ['user_id' => $row['user_id'], 'name' => $row['name'], 'amount' => $row['balance']])
            ->sortByDesc('amount')
            ->values();

        $transfers = [];
        $i = 0;
        $j = 0;

        while ($i < $debtors->count() && $j < $creditors->count()) {
            $debt = $debtors[$i];
            $credit = $creditors[$j];
            $amount = min($debt['amount'], $credit['amount']);

            if ($amount > 0.009) {
                $transfers[] = [
                    'from_id' => $debt['user_id'],
                    'from' => $debt['name'],
                    'to_id' => $credit['user_id'],
                    'to' => $credit['name'],
                    'amount' => round($amount, 2),
                ];
            }

            $debt['amount'] -= $amount;
            $credit['amount'] -= $amount;
            $debtors[$i] = $debt;
            $creditors[$j] = $credit;

            if ($debt['amount'] <= 0.009) {
                $i++;
            }
            if ($credit['amount'] <= 0.009) {
                $j++;
            }
        }

        return $transfers;
    }
}
