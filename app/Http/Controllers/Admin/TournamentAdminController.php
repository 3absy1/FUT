<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Stadium;
use App\Models\Tournament;
use Illuminate\Http\Request;

class TournamentAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Tournament::with(['stadium', 'minDivision'])->withCount('participants');

        if ($search = $request->get('search')) {
            $query->whereRaw("JSON_EXTRACT(name, '$.en') LIKE ?", ["%$search%"])
                  ->orWhereRaw("JSON_EXTRACT(name, '$.ar') LIKE ?", ["%$search%"]);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $tournaments = $query->orderByDesc('start_date')->paginate(20)->withQueryString();

        return view('admin.pages.tournaments.index', compact('tournaments'));
    }

    public function create()
    {
        $stadiums  = Stadium::orderBy('name')->get();
        $divisions = Division::orderBy('sort_order')->get();

        return view('admin.pages.tournaments.create', compact('stadiums', 'divisions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_en'           => 'required|string|max:200',
            'name_ar'           => 'nullable|string|max:200',
            'stadium_id'        => 'nullable|exists:stadiums,id',
            'min_division_id'   => 'nullable|exists:levels,id',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'max_teams'         => 'required|integer|min:2',
            'entry_fee_per_team'=> 'required|numeric|min:0',
            'status'            => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);

        Tournament::create([
            'name'               => ['en' => $data['name_en'], 'ar' => $data['name_ar'] ?? ''],
            'stadium_id'         => $data['stadium_id'],
            'min_division_id'    => $data['min_division_id'],
            'start_date'         => $data['start_date'],
            'end_date'           => $data['end_date'],
            'max_teams'          => $data['max_teams'],
            'entry_fee_per_team' => $data['entry_fee_per_team'],
            'status'             => $data['status'],
        ]);

        return redirect()->route('admin.tournaments.index')->with('success', 'Tournament created.');
    }

    public function show(Tournament $tournament)
    {
        $tournament->load([
            'stadium',
            'minDivision',
            'participants.club',
            'participants.division',
            'matches.clubA',
            'matches.clubB',
            'payments',
        ]);

        return view('admin.pages.tournaments.show', compact('tournament'));
    }

    public function edit(Tournament $tournament)
    {
        $stadiums  = Stadium::orderBy('name')->get();
        $divisions = Division::orderBy('sort_order')->get();

        return view('admin.pages.tournaments.edit', compact('tournament', 'stadiums', 'divisions'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        $data = $request->validate([
            'name_en'           => 'required|string|max:200',
            'name_ar'           => 'nullable|string|max:200',
            'stadium_id'        => 'nullable|exists:stadiums,id',
            'min_division_id'   => 'nullable|exists:levels,id',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date|after_or_equal:start_date',
            'max_teams'         => 'required|integer|min:2',
            'entry_fee_per_team'=> 'required|numeric|min:0',
            'status'            => 'required|in:upcoming,ongoing,completed,cancelled',
        ]);

        $tournament->update([
            'name'               => ['en' => $data['name_en'], 'ar' => $data['name_ar'] ?? ''],
            'stadium_id'         => $data['stadium_id'],
            'min_division_id'    => $data['min_division_id'],
            'start_date'         => $data['start_date'],
            'end_date'           => $data['end_date'],
            'max_teams'          => $data['max_teams'],
            'entry_fee_per_team' => $data['entry_fee_per_team'],
            'status'             => $data['status'],
        ]);

        return redirect()->route('admin.tournaments.show', $tournament)->with('success', 'Tournament updated.');
    }

    public function destroy(Tournament $tournament)
    {
        $tournament->delete();

        return redirect()->route('admin.tournaments.index')->with('success', 'Tournament deleted.');
    }
}
