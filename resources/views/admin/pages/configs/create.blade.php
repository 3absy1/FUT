@extends('admin.layouts.app')
@section('title', 'New Config Key')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.configs.index') }}">App Config</a> / New</div>
        <div class="section-title">New Config Key</div>
    </div>
    <a href="{{ route('admin.configs.index') }}" class="btn btn-ghost">← Back</a>
</div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('admin.configs.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">Config Key</label>
            <input type="text" name="key" class="form-control" value="{{ old('key') }}"
                placeholder="e.g. app_version, max_clubs_per_user, feature_tournaments"
                required style="font-family:monospace;">
            @error('key')<div style="color:var(--red);font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
            <div style="margin-top:6px; font-size:12px; color:var(--text-muted);">Use snake_case. Must be unique.</div>
        </div>

        <div class="form-group">
            <label class="form-label">Value</label>
            <textarea name="value" class="form-control" rows="5"
                placeholder='Simple string, number, or valid JSON…'
                required style="font-family:monospace; resize:vertical;">{{ old('value') }}</textarea>
            @error('value')<div style="color:var(--red);font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
        </div>

        <div style="display:flex; gap:12px;">
            <button type="submit" class="btn btn-accent">Save Config</button>
            <a href="{{ route('admin.configs.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
