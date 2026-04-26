@extends('admin.layouts.app')
@section('title', 'New Division')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.divisions.index') }}">Divisions</a> / New</div>
        <div class="section-title">New Division</div>
    </div>
    <a href="{{ route('admin.divisions.index') }}" class="btn btn-ghost">← Back</a>
</div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('admin.divisions.store') }}">
        @csrf

        <div style="background:var(--bg-elevated); border-radius:10px; padding:18px; margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:14px;">Division Name</div>
            <div class="grid-2">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">English</label>
                    <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}" placeholder="e.g. Division 1" required>
                    @error('name_en')<div style="color:var(--red);font-size:12px;margin-top:5px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Arabic</label>
                    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" placeholder="e.g. الدوري الأول" dir="rtl">
                </div>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 1) }}" min="0" required>
                <div style="font-size:11px;color:var(--text-muted);margin-top:5px;">Lower = higher tier</div>
            </div>
            <div class="form-group">
                <label class="form-label">Matches per Season</label>
                <input type="number" name="matches_count" class="form-control" value="{{ old('matches_count', 10) }}" min="1" required>
            </div>
            <div class="form-group">
                <label class="form-label">EXP on Win</label>
                <input type="number" name="exp_win" class="form-control" value="{{ old('exp_win', 100) }}" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">EXP on Draw</label>
                <input type="number" name="draw_exp" class="form-control" value="{{ old('draw_exp', 40) }}" min="0" required>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Checkpoints</label>
            <input type="text" name="checkpoints" class="form-control" value="{{ old('checkpoints') }}"
                placeholder="e.g. 3,6,9 (comma-separated match numbers)" style="font-family:monospace;">
            <div style="font-size:11px;color:var(--text-muted);margin-top:5px;">Comma-separated integers representing milestone match numbers.</div>
        </div>

        <div style="display:flex; gap:12px; margin-top:8px;">
            <button type="submit" class="btn btn-accent">Create Division</button>
            <a href="{{ route('admin.divisions.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
