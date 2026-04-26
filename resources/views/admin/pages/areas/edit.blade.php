@extends('admin.layouts.app')
@section('title', 'Edit Area')

@section('content')

@php
    $nameArr = is_array($area->name) ? $area->name : [];
    $nameEn  = $nameArr['en'] ?? '';
    $nameAr  = $nameArr['ar'] ?? '';
@endphp

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.areas.index') }}">Areas</a> / Edit</div>
        <div class="section-title">Edit Area: {{ $nameEn }}</div>
    </div>
    <a href="{{ route('admin.areas.show', $area) }}" class="btn btn-ghost">← Back</a>
</div>

<div class="card" style="max-width:600px;">
    <form method="POST" action="{{ route('admin.areas.update', $area) }}">
        @csrf @method('PUT')

        <div style="background:var(--bg-elevated); border-radius:10px; padding:18px; margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:14px;">Area Name</div>
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

        <div class="form-group">
            <label class="form-label">Coordinates <span style="color:var(--text-muted);font-weight:400;">(optional)</span></label>
            <input type="text" name="coordinates" class="form-control" value="{{ old('coordinates', $area->coordinates) }}"
                placeholder="e.g. 30.0444,31.2357" style="font-family:monospace;">
        </div>

        <div style="display:flex; gap:12px; margin-top:8px;">
            <button type="submit" class="btn btn-accent">Save Changes</button>
            <a href="{{ route('admin.areas.show', $area) }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
