@extends('admin.layouts.app')
@section('title', 'Stadiums')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Stadiums</div>
        <div class="section-title">Stadiums</div>
    </div>
    <a href="{{ route('admin.stadiums.create') }}" class="btn btn-accent">+ Add Stadium</a>
</div>

<div class="filter-bar">
    <form method="GET" style="display:contents;">
        <input type="text" name="search" class="form-control" placeholder="Search stadium…" value="{{ request('search') }}">
        <select name="area_id" class="form-control">
            <option value="">All areas</option>
            @foreach($areas as $area)
            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                {{ $area->localized_name ?? data_get($area->name, 'en') ?? data_get($area->name, 'ar') ?? '—' }}
            </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-accent">Filter</button>
        <a href="{{ route('admin.stadiums.index') }}" class="btn btn-ghost">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Stadium</th>
                    <th>Area</th>
                    <th>Price/hr</th>
                    <th>Rating</th>
                    <th>Pitches</th>
                    <th>Matches</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($stadiums as $stadium)
                <tr>
                    <td>
                        <div style="font-weight:600;">{{ $stadium->name }}</div>
                        @if($stadium->location)
                        <div style="font-size:11px;color:var(--text-muted);">📍 {{ Str::limit($stadium->location, 50) }}</div>
                        @endif
                    </td>
                    <td class="muted">{{ $stadium->area?->localized_name ?? data_get($stadium->area?->name, 'en') ?? data_get($stadium->area?->name, 'ar') ?? '—' }}</td>
                    <td style="font-weight:600; color:var(--accent);">LE {{ number_format($stadium->price_per_hour, 0) }}</td>
                    <td style="color:var(--gold);">★ {{ number_format($stadium->rating ?? 0, 1) }}</td>
                    <td><span class="badge badge-blue">{{ $stadium->pitches_count }}</span></td>
                    <td><span class="badge badge-accent">{{ $stadium->matches_count }}</span></td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.stadiums.show', $stadium) }}" class="btn btn-ghost btn-sm">View</a>
                            <a href="{{ route('admin.stadiums.edit', $stadium) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.stadiums.destroy', $stadium) }}" onsubmit="return confirm('Delete this stadium?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;color:var(--text-muted);padding:60px;">No stadiums found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $stadiums->links('admin.pagination') }}</div>
</div>

@endsection
