@extends('admin.layouts.app')
@section('title', 'Areas')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Areas</div>
        <div class="section-title">Areas</div>
    </div>
    <a href="{{ route('admin.areas.create') }}" class="btn btn-accent">+ New Area</a>
</div>

<div class="filter-bar">
    <form method="GET" style="display:contents;">
        <input type="text" name="search" class="form-control" placeholder="Search area name…" value="{{ request('search') }}">
        <button type="submit" class="btn btn-accent">Search</button>
        <a href="{{ route('admin.areas.index') }}" class="btn btn-ghost">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name (EN)</th>
                    <th>Name (AR)</th>
                    <th>Clubs</th>
                    <th>Stadiums</th>
                    <th>Coordinates</th>
                    <th style="width:160px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($areas as $area)
                @php
                    $nameArr = is_array($area->name) ? $area->name : [];
                    $nameEn  = $nameArr['en'] ?? '—';
                    $nameAr  = $nameArr['ar'] ?? '—';
                @endphp
                <tr>
                    <td class="muted">{{ $area->id }}</td>
                    <td style="font-weight:600;">{{ $nameEn }}</td>
                    <td class="muted" style="font-family:serif;">{{ $nameAr }}</td>
                    <td><span class="badge badge-purple">{{ $area->clubs_count }}</span></td>
                    <td><span class="badge badge-orange">{{ $area->stadiums_count }}</span></td>
                    <td>
                        @if($area->coordinates)
                            <code style="font-size:11px; color:var(--text-muted); font-family:monospace;">{{ Str::limit($area->coordinates, 40) }}</code>
                        @else
                            <span class="muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.areas.show', $area) }}" class="btn btn-ghost btn-sm">View</a>
                            <a href="{{ route('admin.areas.edit', $area) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.areas.destroy', $area) }}" onsubmit="return confirm('Delete area « {{ $nameEn }} »?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; color:var(--text-muted); padding:60px;">
                        No areas yet. <a href="{{ route('admin.areas.create') }}" style="color:var(--accent);">Add one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $areas->links('admin.pagination') }}</div>
</div>

@endsection
