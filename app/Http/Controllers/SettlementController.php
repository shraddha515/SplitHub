<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function store(Request $request, Group $group): RedirectResponse|JsonResponse
    {
        abort_unless($group->members()->whereKey($request->user()->id)->exists(), 403);

        $data = $request->validate([
            'paid_by' => ['required', 'exists:users,id', 'different:paid_to'],
            'paid_to' => ['required', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'settled_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $memberIds = $group->members()->pluck('users.id')->all();
        abort_if(count(array_diff([$data['paid_by'], $data['paid_to']], $memberIds)) > 0, 422, 'Settlement users must belong to this group.');

        $settlement = $group->settlements()->create($data);

        return $request->expectsJson()
            ? response()->json(['message' => 'Settlement recorded.', 'settlement' => $settlement], 201)
            : back()->with('status', 'Settlement recorded.');
    }
}
