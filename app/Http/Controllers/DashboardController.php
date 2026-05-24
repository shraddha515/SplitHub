<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\BalanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function landing(): View
    {
        return view('landing');
    }

    public function index(Request $request, BalanceService $balances): View
    {
        $groups = $request->user()
            ->groups()
            ->with(['members:id,name,email', 'expenses', 'settlements'])
            ->latest('groups.created_at')
            ->get();

        $portfolio = $groups->map(fn ($group) => [
            'group' => $group,
            'summary' => $balances->summary($group),
        ]);

        return view('dashboard', [
            'groups' => $groups,
            'portfolio' => $portfolio,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
