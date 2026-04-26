@extends('admin.layouts.app')
@section('title', 'Divisions')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Divisions</div>
        <div class="section-title">Divisions / Levels</div>
    </div>
    <a href="{{ route('admin.divisions.create') }}" class="btn btn-accent">+ New Division</a>
</div>

<div class="filter-bar">
    <form method="GET" style="display:contents;">
        <input type="text" name="search" class="form-control" placeholder="Search by name…" value="{{ request('search') }}">
        <button type="submit" class="btn btn-accent">Search</button>
        <a href="{{ route('admin.divisions.index') }}" class="btn btn-ghost">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Name (EN)</th>
                    <th>Name (AR)</th>
                    <th>Matches</th>
                    <th>EXP Win</th>
                    <th>Draw EXP</th>
                    <th>Checkpoints</th>
                    <th style="width:130px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($divisions as $division)
                @php
                    $nameArr  = is_array($division->name) ? $division->name : [];
                    $nameEn   = $nameArr['en'] ?? '—';
                    $nameAr   = $nameArr['ar'] ?? '—';
                    $checkpts = is_array($division->checkpoints) ? implode(', ', $division->checkpoints) : '—';
                @endphp
                <tr>
                    <td>
                        <div style="width:32px;height:32px;background:var(--accent-dim);border-radius:8px;display:flex;align-items:center;justify-content:center;font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--accent);">
                            {{ $division->sort_order }}
                        </div>
                    </td>
                    <td style="font-weight:600;">{{ $nameEn }}</td>
                    <td class="muted" style="font-family:serif;">{{ $nameAr }}</td>
                    <td><span class="badge badge-blue">{{ $division->matches_count }}</span></td>
                    <td style="color:var(--green); font-weight:600;">+{{ $division->exp_win }}</td>
                    <td style="color:var(--orange); font-weight:600;">+{{ $division->draw_exp }}</td>
                    <td>
                        @if($checkpts !== '—')
                            <span style="font-size:12px; font-family:monospace; color:var(--text-secondary);">{{ $checkpts }}</span>
                        @else
                            <span class="muted">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.divisions.edit', $division) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.divisions.destroy', $division) }}" onsubmit="return confirm('Delete division « {{ $nameEn }} »?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center; color:var(--text-muted); padding:60px;">
                        No divisions yet. <a href="{{ route('admin.divisions.create') }}" style="color:var(--accent);">Create one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $divisions->links('admin.pagination') }}</div>
</div>

@endsection
