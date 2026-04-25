<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\GameMatch;
use App\Models\MatchPlayer;
use App\Models\Stadium;
use Illuminate\Http\Request;

class MatchAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = GameMatch::with(['clubA', 'clubB', 'stadium', 'pitch']);

        if ($search = $request->get('search')) {
            $query->whereHas('clubA', fn($q) => $q->where('name', 'like', "%$search%"))
                  ->orWhereHas('clubB', fn($q) => $q->where('name', 'like', "%$search%"));
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($date = $request->get('date')) {
            $query->whereDate('scheduled_datetime', $date);
        }

        $matches = $query->orderByDesc('scheduled_datetime')->paginate(20)->withQueryString();

        return view('admin.pages.matches.index', compact('matches'));
    }

    public function show(GameMatch $match)
    {
        $match->load(['clubA', 'clubB', 'stadium', 'pitch', 'matchPlayers.user', 'payments']);
        return view('admin.pages.matches.show', compact('match'));
    }

    public function edit(GameMatch $match)
    {
        $clubs    = Club::orderBy('name')->get();
        $stadiums = Stadium::orderBy('name')->get();
        return view('admin.pages.matches.edit', compact('match', 'clubs', 'stadiums'));
    }

    public function update(Request $request, GameMatch $match)
    {
        $data = $request->validate([
            'club_a_id'          => 'required|exists:clubs,id',
            'club_b_id'          => 'required|exists:clubs,id|different:club_a_id',
            'stadium_id'         => 'nullable|exists:stadiums,id',
            'scheduled_datetime' => 'required|date',
            'status'             => 'required|in:pending,in_progress,completed,cancelled',
            'score_club_a'       => 'nullable|integer|min:0',
            'score_club_b'       => 'nullable|integer|min:0',
            'result'             => 'nullable|in:club_a_wins,club_b_wins,draw',
        ]);

        $match->update($data);
        return redirect()->route('admin.matches.show', $match)->with('success', 'Match updated.');
    }

    public function destroy(GameMatch $match)
    {
        $match->delete();
        return redirect()->route('admin.matches.index')->with('success', 'Match deleted.');
    }

    public function players(GameMatch $match)
    {
        $players = MatchPlayer::where('match_id', $match->id)->with('user')->get();
        return view('admin.pages.matches.players', compact('match', 'players'));
    }
}
