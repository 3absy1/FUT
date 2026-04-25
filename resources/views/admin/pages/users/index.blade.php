@extends('admin.layouts.app')
@section('title', 'Players')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Players</div>
        <div class="section-title">Players</div>
    </div>
</div>

<div class="filter-bar">
    <form method="GET" style="display:contents;">
        <input type="text" name="search" class="form-control" placeholder="Search name, email, phone…" value="{{ request('search') }}">
        <select name="position" class="form-control">
            <option value="">All positions</option>
            <option value="attacker" {{ request('position')=='attacker'?'selected':'' }}>Attacker</option>
            <option value="midfielder" {{ request('position')=='midfielder'?'selected':'' }}>Midfielder</option>
            <option value="defender" {{ request('position')=='defender'?'selected':'' }}>Defender</option>
            <option value="goal_keeper" {{ request('position')=='goal_keeper'?'selected':'' }}>Goalkeeper</option>
        </select>
        <button type="submit" class="btn btn-accent">Filter</button>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Player</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Rating</th>
                    <th>Division</th>
                    <th>Goals</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="avatar">{{ strtoupper(substr($user->nick_name ?? $user->name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <div style="font-weight:600;">{{ $user->nick_name ?? (is_array($user->name) ? ($user->name['en'] ?? '') : $user->name) }}</div>
                                <div style="font-size:11px;color:var(--text-muted);">ID #{{ $user->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="muted">{{ $user->email }}</td>
                    <td>
                        @if($user->position)
                        <span class="badge badge-blue">{{ ucfirst(str_replace('_',' ',$user->position)) }}</span>
                        @else <span class="muted">—</span> @endif
                    </td>
                    <td>
                        @if($user->overallRating())
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--accent);">{{ $user->overallRating() }}</span>
                        @else <span class="muted">—</span> @endif
                    </td>
                    <td class="muted">{{ $user->division?->localized_name ?? '—' }}</td>
                    <td>
                        <span style="font-weight:600;">{{ $user->goals_scored ?? 0 }}</span>
                        <span class="muted" style="font-size:11px;"> G / {{ $user->assists_count ?? 0 }} A</span>
                    </td>
                    <td>
                        @if($user->is_verified)
                            <span class="badge badge-green">Verified</span>
                        @else
                            <span class="badge badge-orange">Pending</span>
                        @endif
                    </td>
                    <td class="muted">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-ghost btn-sm">View</a>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-sm">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;color:var(--text-muted);padding:60px;">No players found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">
        {{ $users->links('admin.pagination') }}
    </div>
</div>

@endsection
