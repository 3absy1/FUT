@extends('admin.layouts.app')
@section('title', 'Edit Club')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.clubs.index') }}">Clubs</a> / Edit</div>
        <div class="section-title">Edit Club: {{ $club->name }}</div>
    </div>
    <a href="{{ route('admin.clubs.show', $club) }}" class="btn btn-ghost">← Back</a>
</div>

<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.clubs.update', $club) }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Club Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $club->name) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Area</label>
            <select name="area_id" class="form-control">
                <option value="">— None —</option>
                @foreach($areas as $area)
                <option value="{{ $area->id }}" {{ old('area_id', $club->area_id) == $area->id ? 'selected' : '' }}>
                    {{ $area->localized_name ?? data_get($area->name, 'en') ?? data_get($area->name, 'ar') ?? '—' }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Max Players</label>
                <input type="number" min="1" name="max_players" class="form-control" value="{{ old('max_players', $club->max_players) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Rating</label>
                <input type="number" step="0.01" name="rating" class="form-control" value="{{ old('rating', $club->rating) }}">
            </div>
        </div>
        <div style="display:flex; gap:12px;">
            <button type="submit" class="btn btn-accent">Save Changes</button>
            <a href="{{ route('admin.clubs.show', $club) }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
