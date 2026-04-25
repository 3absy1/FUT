@extends('admin.layouts.app')
@section('title', $club->name)

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.clubs.index') }}">Clubs</a> / {{ $club->name }}</div>
        <div class="section-title">{{ $club->name }}</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-accent">Edit</a>
        <form method="POST" action="{{ route('admin.clubs.destroy', $club) }}" onsubmit="return confirm('Delete club?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

<div class="grid-2" style="margin-bottom:24px;">
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;">Club Info</div>
        @php
        $info = [
            'Area'        => $club->area?->name ?? '—',
            'Rating'      => number_format($club->rating ?? 0, 2),
            'EXP'         => number_format($club->exp ?? 0),
            'Max Players' => $club->max_players ?? '—',
            'Created'     => $club->created_at->format('d M Y'),
        ];
        @endphp
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            @foreach($info as $label => $val)
            <div style="background:var(--bg-elevated); border-radius:8px; padding:14px;">
                <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:4px;">{{ $label }}</div>
                <div style="font-weight:600; font-size:15px;">{{ $val }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-title" style="margin-bottom:16px;">Active Squad</div>
        @forelse($club->activeMembers as $member)
        <div style="display:flex; align-items:center; gap:12px; padding:10px; background:var(--bg-elevated); border-radius:8px; margin-bottom:6px;">
            <div class="avatar">{{ strtoupper(substr($member->user?->nick_name ?? 'U', 0, 1)) }}</div>
            <div style="flex:1;">
                <div style="font-weight:600; font-size:13px;">{{ $member->user?->nick_name ?? '—' }}</div>
                @if($member->user?->position)
                <span class="badge badge-blue" style="font-size:10px;">{{ ucfirst(str_replace('_',' ',$member->user->position)) }}</span>
                @endif
            </div>
            @if($member->user)
            <a href="{{ route('admin.users.show', $member->user) }}" class="btn btn-ghost btn-sm">View</a>
            @endif
        </div>
        @empty
        <div style="color:var(--text-muted); font-size:13px;">No active members.</div>
        @endforelse
    </div>
</div>

<div class="card">
    <div class="card-title" style="margin-bottom:16px;">Recent Matches</div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Opponent</th><th>Stadium</th><th>Date</th><th>Score</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @php $allMatches = $club->matchesAsClubA->merge($club->matchesAsClubB)->sortByDesc('scheduled_datetime')->take(10); @endphp
                @forelse($allMatches as $m)
                <tr>
                    <td>
                        @php $opponent = $m->club_a_id == $club->id ? $m->clubB : $m->clubA; @endphp
                        <span style="font-weight:600;">{{ $opponent?->name ?? '—' }}</span>
                    </td>
                    <td class="muted">{{ $m->stadium?->name ?? '—' }}</td>
                    <td class="muted">{{ $m->scheduled_datetime?->format('d M Y') }}</td>
                    <td>
                        @if($m->status === 'completed')
                            <span class="score-display">{{ $m->score_club_a }} – {{ $m->score_club_b }}</span>
                        @else <span class="muted">—</span> @endif
                    </td>
                    <td><span class="badge {{ ['completed'=>'badge-green','in_progress'=>'badge-orange','pending'=>'badge-blue','cancelled'=>'badge-red'][$m->status] ?? 'badge-gray' }}">{{ $m->status }}</span></td>
                    <td><a href="{{ route('admin.matches.show', $m) }}" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:40px;">No matches.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
