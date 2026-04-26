@extends('admin.layouts.app')
@section('title', 'Edit Config')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.configs.index') }}">App Config</a> / Edit</div>
        <div class="section-title">Edit Config Key</div>
    </div>
    <a href="{{ route('admin.configs.index') }}" class="btn btn-ghost">← Back</a>
</div>

<div class="card" style="max-width:640px;">
    <form method="POST" action="{{ route('admin.configs.update', $config) }}">
        @csrf @method('PUT')

        <div class="form-group">
            <label class="form-label">Config Key</label>
            <input type="text" name="key" class="form-control" value="{{ old('key', $config->key) }}"
                required style="font-family:monospace;">
            @error('key')<div style="color:var(--red);font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label class="form-label">Value</label>
            <textarea name="value" class="form-control" rows="6"
                required style="font-family:monospace; resize:vertical;">{{ old('value', $config->value) }}</textarea>
            @error('value')<div style="color:var(--red);font-size:12px;margin-top:6px;">{{ $message }}</div>@enderror
        </div>

        <div style="margin-bottom:20px; padding:14px; background:var(--bg-elevated); border-radius:8px; font-size:12px; color:var(--text-muted);">
            Last updated: <strong style="color:var(--text-primary);">{{ $config->updated_at->format('d M Y, H:i') }}</strong>
        </div>

        <div style="display:flex; gap:12px;">
            <button type="submit" class="btn btn-accent">Save Changes</button>
            <a href="{{ route('admin.configs.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
