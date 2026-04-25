@extends('admin.layouts.app')
@section('title', 'Matches')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Matches</div>
        <div class="section-title">Matches</div>
    </div>
</div>

<div class="filter-bar">
    <form method="GET" style="display:contents;">
        <input type="text" name="search" class="form-control" placeholder="Search club name…" value="{{ request('search') }}">
        <select name="status" class="form-control">
            <option value="">All statuses</option>
            <option value="pending"     {{ request('status')=='pending'?'selected':'' }}>Pending</option>
            <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
            <option value="completed"   {{ request('status')=='completed'?'selected':'' }}>Completed</option>
            <option value="cancelled"   {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
        </select>
        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        <button type="submit" class="btn btn-accent">Filter</button>
        <a href="{{ route('admin.matches.index') }}" class="btn btn-ghost">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Match</th>
                    <th>Stadium</th>
                    <th>Pitch</th>
                    <th>Date & Time</th>
                    <th>Score</th>
                    <th>Result</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($matches as $match)
                @php
                $badgeMap = ['completed'=>'badge-green','in_progress'=>'badge-orange','pending'=>'badge-blue','cancelled'=>'badge-red'];
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $match->clubA?->name ?? '—' }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">vs {{ $match->clubB?->name ?? '—' }}</div>
                    </td>
                    <td class="muted">{{ $match->stadium?->name ?? '—' }}</td>
                    <td class="muted">{{ $match->pitch?->name ?? '—' }}</td>
                    <td class="muted" style="font-size:12px;">{{ $match->scheduled_datetime?->format('d M Y') }}<br>{{ $match->scheduled_datetime?->format('H:i') }}</td>
                    <td>
                        @if($match->status === 'completed')
                            <span class="score-display" style="color:var(--accent);">{{ $match->score_club_a }} – {{ $match->score_club_b }}</span>
                        @else <span class="muted">—</span> @endif
                    </td>
                    <td>
                        @if($match->result)
                            @php
                            $resultMap = ['club_a_wins'=>'A Wins','club_b_wins'=>'B Wins','draw'=>'Draw'];
                            $resultBadge = ['club_a_wins'=>'badge-green','club_b_wins'=>'badge-blue','draw'=>'badge-orange'];
                            @endphp
                            <span class="badge {{ $resultBadge[$match->result] ?? 'badge-gray' }}">{{ $resultMap[$match->result] ?? $match->result }}</span>
                        @else <span class="muted">—</span> @endif
                    </td>
                    <td><span class="badge {{ $badgeMap[$match->status] ?? 'badge-gray' }}">{{ $match->status }}</span></td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.matches.show', $match) }}" class="btn btn-ghost btn-sm">View</a>
                            <a href="{{ route('admin.matches.edit', $match) }}" class="btn btn-ghost btn-sm">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;color:var(--text-muted);padding:60px;">No matches found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $matches->links('admin.pagination') }}</div>
</div>

@endsection
