<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function monthly(Request $request): JsonResponse
    {
        $rows = DB::table('expenses')
            ->join('group_members', 'group_members.group_id', '=', 'expenses.group_id')
            ->leftJoin('categories', 'categories.id', '=', 'expenses.category_id')
            ->where('group_members.user_id', $request->user()->id)
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, COALESCE(categories.name, "Other") as category, SUM(amount) as total')
            ->groupBy('month', 'category')
            ->orderBy('month')
            ->get();

        return response()->json($rows);
    }
}
