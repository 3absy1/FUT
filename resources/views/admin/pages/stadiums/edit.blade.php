@extends('admin.layouts.app')
@section('title', 'Edit Stadium')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.stadiums.index') }}">Stadiums</a> / Edit</div>
        <div class="section-title">Edit Stadium</div>
    </div>
    <a href="{{ route('admin.stadiums.show', $stadium) }}" class="btn btn-ghost">← Back</a>
</div>

<div class="card" style="max-width:720px;">
    <form method="POST" action="{{ route('admin.stadiums.update', $stadium) }}">
        @csrf @method('PUT')
        <div class="grid-2">
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Stadium Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $stadium->name) }}" required>
            </div>
            <div class="form-group" style="grid-column:1/-1;">
                <label class="form-label">Location / Address</label>
                <input type="text" name="location" class="form-control" value="{{ old('location', $stadium->location) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Area</label>
                <select name="area_id" class="form-control" required>
                    @foreach($areas as $area)
                    <option value="{{ $area->id }}" {{ old('area_id', $stadium->area_id) == $area->id ? 'selected' : '' }}>
                        {{ $area->localized_name ?? data_get($area->name, 'en') ?? data_get($area->name, 'ar') ?? '—' }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Price per Hour (LE)</label>
                <input type="number" step="0.01" name="price_per_hour" class="form-control" value="{{ old('price_per_hour', $stadium->price_per_hour) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">WhatsApp Number</label>
                <input type="text" name="whatsapp_number" class="form-control" value="{{ old('whatsapp_number', $stadium->whatsapp_number) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Rating (0–5)</label>
                <input type="number" step="0.1" min="0" max="5" name="rating" class="form-control" value="{{ old('rating', $stadium->rating) }}">
            </div>
        </div>
        <div style="display:flex; gap:12px;">
            <button type="submit" class="btn btn-accent">Save Changes</button>
            <a href="{{ route('admin.stadiums.show', $stadium) }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
