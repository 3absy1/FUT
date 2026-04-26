@extends('admin.layouts.app')
@section('title', 'Edit Division')

@section('content')

@php
    $nameArr  = is_array($division->name) ? $division->name : [];
    $nameEn   = $nameArr['en'] ?? '';
    $nameAr   = $nameArr['ar'] ?? '';
    $checkpts = is_array($division->checkpoints) ? implode(',', $division->checkpoints) : '';
@endphp

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.divisions.index') }}">Divisions</a> / Edit</div>
        <div class="section-title">Edit: {{ $nameEn }}</div>
    </div>
    <a href="{{ route('admin.divisions.index') }}" class="btn btn-ghost">← Back</a>
</div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('admin.divisions.update', $division) }}">
        @csrf @method('PUT')

        <div style="background:var(--bg-elevated); border-radius:10px; padding:18px; margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:14px;">Division Name</div>
            <div class="grid-2">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">English</label>
                    <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $nameEn) }}" required>
                    @error('name_en')<div style="color:var(--red);font-size:12px;margin-top:5px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Arabic</label>
                    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $nameAr) }}" dir="rtl">
                </div>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $division->sort_order) }}" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">Matches per Season</label>
                <input type="number" name="matches_count" class="form-control" value="{{ old('matches_count', $division->matches_count) }}" min="1" required>
            </div>
            <div class="form-group">
                <label class="form-label">EXP on Win</label>
                <input type="number" name="exp_win" class="form-control" value="{{ old('exp_win', $division->exp_win) }}" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">EXP on Draw</label>
                <input type="number" name="draw_exp" class="form-control" value="{{ old('draw_exp', $division->draw_exp) }}" min="0" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Checkpoints</label>
            <input type="text" name="checkpoints" class="form-control" value="{{ old('checkpoints', $checkpts) }}"
                placeholder="e.g. 3,6,9" style="font-family:monospace;">
            <div style="font-size:11px;color:var(--text-muted);margin-top:5px;">Comma-separated match number milestones.</div>
        </div>

        <div style="display:flex; gap:12px; margin-top:8px;">
            <button type="submit" class="btn btn-accent">Save Changes</button>
            <a href="{{ route('admin.divisions.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
