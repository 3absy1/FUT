<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['division', 'stadium'])
            ->where('is_stadium_owner', false);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nick_name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        if ($position = $request->get('position')) {
            $query->where('position', $position);
        }

        $users = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.pages.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['division', 'matchPlayers.match.stadium', 'clubMembers.club']);
        return view('admin.pages.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.pages.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'nick_name'     => 'required|string|max:100',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'phone'         => 'nullable|string|max:20',
            'position'      => 'nullable|in:attacker,midfielder,defender,goal_keeper',
            'rating'        => 'nullable|numeric|min:0|max:100',
            'wallet_balance'=> 'nullable|numeric|min:0',
            'is_verified'   => 'boolean',
        ]);

        $user->update($data);

        return redirect()->route('admin.users.show', $user)->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
