<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Club;
use Illuminate\Http\Request;

class ClubAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Club::with('area')->withCount(['members', 'activeMembers']);

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%$search%");
        }

        $clubs = $query->orderByDesc('rating')->paginate(20)->withQueryString();
        return view('admin.pages.clubs.index', compact('clubs'));
    }

    public function show(Club $club)
    {
        $club->load(['area', 'activeMembers.user', 'matchesAsClubA.stadium', 'matchesAsClubB.stadium']);
        return view('admin.pages.clubs.show', compact('club'));
    }

    public function edit(Club $club)
    {
        $areas = Area::orderBy('name')->get();
        return view('admin.pages.clubs.edit', compact('club', 'areas'));
    }

    public function update(Request $request, Club $club)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:200',
            'area_id'     => 'nullable|exists:areas,id',
            'max_players' => 'nullable|integer|min:1|max:50',
            'rating'      => 'nullable|numeric|min:0|max:100',
        ]);

        $club->update($data);
        return redirect()->route('admin.clubs.show', $club)->with('success', 'Club updated.');
    }

    public function destroy(Club $club)
    {
        $club->delete();
        return redirect()->route('admin.clubs.index')->with('success', 'Club deleted.');
    }
}
