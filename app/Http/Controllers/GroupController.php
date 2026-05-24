<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Group;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(Request $request): View
    {
        return view('groups.index', [
            'groups' => $request->user()->groups()->withCount(['members', 'expenses'])->latest('groups.created_at')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:140'],
            'description' => ['nullable', 'string', 'max:500'],
            'type' => ['required', 'string', 'max:40'],
            'currency' => ['required', 'string', 'max:8'],
        ]);

        $group = DB::transaction(function () use ($request, $data) {
            $group = Group::create($data + ['created_by' => $request->user()->id]);
            $group->members()->attach($request->user()->id, [
                'role' => 'owner',
                'joined_at' => now(),
            ]);

            return $group;
        });

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Group created.', 'group' => $group], 201);
        }

        return redirect()->route('groups.show', $group)->with('status', 'Group created.');
    }

    public function show(Request $request, Group $group, BalanceService $balances): View
    {
        $this->authorizeMember($request, $group);

        $group->load([
            'members:id,name,email,mobile',
            'expenses.category',
            'expenses.payers.user:id,name,email',
            'expenses.splits.user:id,name,email',
            'settlements.payer:id,name,email',
            'settlements.receiver:id,name,email',
        ]);

        return view('groups.show', [
            'group' => $group,
            'categories' => Category::orderBy('name')->get(),
            'summary' => $balances->summary($group),
        ]);
    }

    public function addMember(Request $request, Group $group): RedirectResponse|JsonResponse
    {
        $this->authorizeMember($request, $group);

        $data = $request->validate([
            'email' => ['required_without:mobile', 'nullable', 'email'],
            'mobile' => ['required_without:email', 'nullable', 'string', 'max:30'],
            'name' => ['nullable', 'string', 'max:120'],
        ]);

        $user = User::query()
            ->when($data['email'] ?? null, fn ($query, $email) => $query->where('email', $email))
            ->when($data['mobile'] ?? null, fn ($query, $mobile) => $query->orWhere('mobile', $mobile))
            ->first();

        if (! $user) {
            $user = User::create([
                'name' => $data['name'] ?: str($data['email'] ?? $data['mobile'])->before('@')->headline(),
                'email' => $data['email'] ?? 'guest+'.uniqid().'@splithub.local',
                'mobile' => $data['mobile'] ?? null,
                'password' => str()->password(16),
            ]);
        }

        $group->members()->syncWithoutDetaching([
            $user->id => ['role' => 'member', 'joined_at' => now()],
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Member added.', 'member' => $user]);
        }

        return back()->with('status', 'Member added.');
    }

    public function removeMember(Request $request, Group $group, User $user): RedirectResponse|JsonResponse
    {
        $this->authorizeMember($request, $group);
        abort_if($group->created_by === $user->id, 422, 'The group owner cannot be removed.');

        $group->members()->detach($user->id);

        return $request->expectsJson()
            ? response()->json(['message' => 'Member removed.'])
            : back()->with('status', 'Member removed.');
    }

    public function balances(Request $request, Group $group, BalanceService $balances): JsonResponse
    {
        $this->authorizeMember($request, $group);

        return response()->json($balances->summary($group));
    }

    public function summary(Request $request, Group $group, BalanceService $balances): JsonResponse
    {
        $this->authorizeMember($request, $group);

        return response()->json($balances->summary($group));
    }

    private function authorizeMember(Request $request, Group $group): void
    {
        abort_unless($group->members()->whereKey($request->user()->id)->exists(), 403);
    }
}
