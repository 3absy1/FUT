@extends('admin.layouts.app')
@section('title', 'Tournaments')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Tournaments</div>
        <div class="section-title">Tournaments</div>
    </div>
    <a href="{{ route('admin.tournaments.create') }}" class="btn btn-accent">+ New Tournament</a>
</div>

<div class="filter-bar">
    <form method="GET" style="display:contents;">
        <input type="text" name="search" class="form-control" placeholder="Search tournament name…" value="{{ request('search') }}">
        <select name="status" class="form-control">
            <option value="">All statuses</option>
            <option value="upcoming"  {{ request('status')=='upcoming'?'selected':'' }}>Upcoming</option>
            <option value="ongoing"   {{ request('status')=='ongoing'?'selected':'' }}>Ongoing</option>
            <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
            <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
        </select>
        <button type="submit" class="btn btn-accent">Filter</button>
        <a href="{{ route('admin.tournaments.index') }}" class="btn btn-ghost">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tournament</th>
                    <th>Stadium</th>
                    <th>Dates</th>
                    <th>Max Teams</th>
                    <th>Participants</th>
                    <th>Entry Fee</th>
                    <th>Min Division</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tournaments as $tournament)
                @php
                    $nameArr = is_array($tournament->name) ? $tournament->name : [];
                    $nameEn  = $nameArr['en'] ?? '—';
                    $statusBadge = [
                        'upcoming'  => 'badge-blue',
                        'ongoing'   => 'badge-orange',
                        'completed' => 'badge-green',
                        'cancelled' => 'badge-red',
                    ];
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:700; font-size:14px;">{{ $nameEn }}</div>
                        @php $nameAr = $nameArr['ar'] ?? ''; @endphp
                        @if($nameAr)
                            <div style="font-size:12px; color:var(--text-muted); font-family:serif;" dir="rtl">{{ $nameAr }}</div>
                        @endif
                    </td>
                    <td class="muted">{{ $tournament->stadium?->name ?? '—' }}</td>
                    <td>
                        <div style="font-size:12px;">{{ $tournament->start_date?->format('d M Y') }}</div>
                        <div style="font-size:11px; color:var(--text-muted);">→ {{ $tournament->end_date?->format('d M Y') }}</div>
                    </td>
                    <td style="font-weight:600; color:var(--accent);">{{ $tournament->max_teams }}</td>
                    <td>
                        <span class="badge badge-purple">{{ $tournament->participants_count }} / {{ $tournament->max_teams }}</span>
                    </td>
                    <td style="font-weight:600; color:var(--green);">LE {{ number_format($tournament->entry_fee_per_team, 0) }}</td>
                    <td class="muted">{{ $tournament->minDivision?->localized_name ?? '—' }}</td>
                    <td><span class="badge {{ $statusBadge[$tournament->status] ?? 'badge-gray' }}">{{ $tournament->status }}</span></td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.tournaments.show', $tournament) }}" class="btn btn-ghost btn-sm">View</a>
                            <a href="{{ route('admin.tournaments.edit', $tournament) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.tournaments.destroy', $tournament) }}" onsubmit="return confirm('Delete this tournament?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center; color:var(--text-muted); padding:60px;">
                        No tournaments yet. <a href="{{ route('admin.tournaments.create') }}" style="color:var(--accent);">Create one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $tournaments->links('admin.pagination') }}</div>
</div>

@endsection
