@extends('admin.layouts.app')
@section('title', 'Area Details')

@section('content')

@php
    $nameArr = is_array($area->name) ? $area->name : [];
    $nameEn  = $nameArr['en'] ?? 'Area';
    $nameAr  = $nameArr['ar'] ?? '';
@endphp

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.areas.index') }}">Areas</a> / {{ $nameEn }}</div>
        <div class="section-title">{{ $nameEn }}</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.areas.edit', $area) }}" class="btn btn-accent">Edit</a>
        <form method="POST" action="{{ route('admin.areas.destroy', $area) }}" onsubmit="return confirm('Delete area?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

{{-- Info cards --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
    <div class="card" style="text-align:center;">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:8px;">Clubs</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:42px;color:var(--purple);">{{ $area->clubs_count }}</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:8px;">Stadiums</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:42px;color:var(--orange);">{{ $area->stadiums_count }}</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:8px;">Name (AR)</div>
        <div style="font-size:22px; font-family:serif; color:var(--text-primary);">{{ $nameAr ?: '—' }}</div>
    </div>
</div>

@if($area->coordinates)
<div class="card" style="margin-bottom:24px;">
    <div class="card-title" style="margin-bottom:12px;">Coordinates</div>
    <code style="background:var(--bg-elevated); padding:12px 16px; border-radius:8px; font-size:13px; color:var(--text-secondary); font-family:monospace; display:block; word-break:break-all;">{{ $area->coordinates }}</code>
</div>
@endif

<div class="grid-2">
    {{-- Clubs in this area --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Clubs in {{ $nameEn }}</div>
            <a href="{{ route('admin.clubs.index', ['area_id' => $area->id]) }}" class="btn btn-ghost btn-sm">View all</a>
        </div>
        @forelse($area->clubs->take(8) as $club)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--bg-elevated);border-radius:8px;margin-bottom:6px;">
            <div style="font-weight:600;">{{ $club->name }}</div>
            <a href="{{ route('admin.clubs.show', $club) }}" class="btn btn-ghost btn-sm">View</a>
        </div>
        @empty
        <div style="color:var(--text-muted);font-size:13px;">No clubs in this area.</div>
        @endforelse
    </div>

    {{-- Stadiums in this area --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Stadiums in {{ $nameEn }}</div>
            <a href="{{ route('admin.stadiums.index', ['area_id' => $area->id]) }}" class="btn btn-ghost btn-sm">View all</a>
        </div>
        @forelse($area->stadiums->take(8) as $stadium)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--bg-elevated);border-radius:8px;margin-bottom:6px;">
            <div>
                <div style="font-weight:600;">{{ $stadium->name }}</div>
                <div style="font-size:11px;color:var(--text-muted);">LE {{ number_format($stadium->price_per_hour,0) }}/hr</div>
            </div>
            <a href="{{ route('admin.stadiums.show', $stadium) }}" class="btn btn-ghost btn-sm">View</a>
        </div>
        @empty
        <div style="color:var(--text-muted);font-size:13px;">No stadiums in this area.</div>
        @endforelse
    </div>
</div>

@endsection
