@extends('admin.layouts.app')
@section('title', 'Clubs')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Clubs</div>
        <div class="section-title">Clubs</div>
    </div>
</div>

<div class="filter-bar">
    <form method="GET" style="display:contents;">
        <input type="text" name="search" class="form-control" placeholder="Search club name…" value="{{ request('search') }}">
        <button type="submit" class="btn btn-accent">Search</button>
        <a href="{{ route('admin.clubs.index') }}" class="btn btn-ghost">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Club</th>
                    <th>Area</th>
                    <th>Rating</th>
                    <th>EXP</th>
                    <th>Members</th>
                    <th>Max Players</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($clubs as $club)
                <tr>
                    <td>
                        <div style="font-weight:600; font-size:15px;">{{ $club->name }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">ID #{{ $club->id }}</div>
                    </td>
                    <td class="muted">{{ $club->area?->name ?? '—' }}</td>
                    <td>
                        <span style="font-family:'Bebas Neue',sans-serif;font-size:22px;color:var(--accent);">{{ number_format($club->rating ?? 0, 1) }}</span>
                    </td>
                    <td class="muted">{{ number_format($club->exp ?? 0) }}</td>
                    <td>
                        <span class="badge badge-blue">{{ $club->active_members_count }} active</span>
                        <span class="muted" style="font-size:11px;"> / {{ $club->members_count }}</span>
                    </td>
                    <td class="muted">{{ $club->max_players ?? '—' }}</td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.clubs.show', $club) }}" class="btn btn-ghost btn-sm">View</a>
                            <a href="{{ route('admin.clubs.edit', $club) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.clubs.destroy', $club) }}" onsubmit="return confirm('Delete club?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:60px;">No clubs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $clubs->links('admin.pagination') }}</div>
</div>

@endsection
