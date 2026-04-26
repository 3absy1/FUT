@extends('admin.layouts.app')
@section('title', 'New Tournament')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.tournaments.index') }}">Tournaments</a> / New</div>
        <div class="section-title">New Tournament</div>
    </div>
    <a href="{{ route('admin.tournaments.index') }}" class="btn btn-ghost">← Back</a>
</div>

<div class="card" style="max-width:760px;">
    <form method="POST" action="{{ route('admin.tournaments.store') }}">
        @csrf

        {{-- Name --}}
        <div style="background:var(--bg-elevated); border-radius:10px; padding:18px; margin-bottom:20px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:14px;">Tournament Name</div>
            <div class="grid-2">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">English *</label>
                    <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}" placeholder="e.g. Cairo Cup 2025" required>
                    @error('name_en')<div style="color:var(--red);font-size:12px;margin-top:5px;">{{ $message }}</div>@enderror
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Arabic</label>
                    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" placeholder="e.g. كأس القاهرة 2025" dir="rtl">
                </div>
            </div>
        </div>

        {{-- Venue --}}
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Stadium</label>
                <select name="stadium_id" class="form-control">
                    <option value="">— None —</option>
                    @foreach($stadiums as $s)
                    <option value="{{ $s->id }}" {{ old('stadium_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Minimum Division</label>
                <select name="min_division_id" class="form-control">
                    <option value="">— Any Division —</option>
                    @foreach($divisions as $div)
                    @php $dn = is_array($div->name) ? ($div->name['en'] ?? '') : $div->name; @endphp
                    <option value="{{ $div->id }}" {{ old('min_division_id') == $div->id ? 'selected' : '' }}>{{ $dn }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Schedule --}}
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Start Date *</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                @error('start_date')<div style="color:var(--red);font-size:12px;margin-top:5px;">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">End Date *</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                @error('end_date')<div style="color:var(--red);font-size:12px;margin-top:5px;">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- Registration --}}
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Max Teams *</label>
                <input type="number" name="max_teams" class="form-control" value="{{ old('max_teams', 8) }}" min="2" required>
            </div>
            <div class="form-group">
                <label class="form-label">Entry Fee per Team (LE) *</label>
                <input type="number" step="0.01" name="entry_fee_per_team" class="form-control" value="{{ old('entry_fee_per_team', 0) }}" min="0" required>
            </div>
        </div>

        {{-- Status --}}
        <div class="form-group">
            <label class="form-label">Status *</label>
            <select name="status" class="form-control" required>
                <option value="upcoming"  {{ old('status','upcoming') == 'upcoming'  ? 'selected' : '' }}>Upcoming</option>
                <option value="ongoing"   {{ old('status') == 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <div style="display:flex; gap:12px; margin-top:8px;">
            <button type="submit" class="btn btn-accent">Create Tournament</button>
            <a href="{{ route('admin.tournaments.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
