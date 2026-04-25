@extends('admin.layouts.app')
@section('title', 'Edit Match')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.matches.index') }}">Matches</a> / Edit</div>
        <div class="section-title">Edit Match #{{ $match->id }}</div>
    </div>
    <a href="{{ route('admin.matches.show', $match) }}" class="btn btn-ghost">← Back</a>
</div>

<div class="card" style="max-width:720px;">
    <form method="POST" action="{{ route('admin.matches.update', $match) }}">
        @csrf @method('PUT')
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Club A</label>
                <select name="club_a_id" class="form-control" required>
                    @foreach($clubs as $club)
                    <option value="{{ $club->id }}" {{ old('club_a_id', $match->club_a_id) == $club->id ? 'selected' : '' }}>{{ $club->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Club B</label>
                <select name="club_b_id" class="form-control" required>
                    @foreach($clubs as $club)
                    <option value="{{ $club->id }}" {{ old('club_b_id', $match->club_b_id) == $club->id ? 'selected' : '' }}>{{ $club->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Stadium</label>
                <select name="stadium_id" class="form-control">
                    <option value="">— None —</option>
                    @foreach($stadiums as $s)
                    <option value="{{ $s->id }}" {{ old('stadium_id', $match->stadium_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Scheduled Date & Time</label>
                <input type="datetime-local" name="scheduled_datetime" class="form-control"
                    value="{{ old('scheduled_datetime', $match->scheduled_datetime?->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control" required>
                    @foreach(['pending','in_progress','completed','cancelled'] as $st)
                    <option value="{{ $st }}" {{ old('status', $match->status) == $st ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$st)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Result</label>
                <select name="result" class="form-control">
                    <option value="">— None —</option>
                    <option value="club_a_wins" {{ old('result', $match->result) == 'club_a_wins' ? 'selected' : '' }}>Club A Wins</option>
                    <option value="club_b_wins" {{ old('result', $match->result) == 'club_b_wins' ? 'selected' : '' }}>Club B Wins</option>
                    <option value="draw"        {{ old('result', $match->result) == 'draw'        ? 'selected' : '' }}>Draw</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Score — Club A</label>
                <input type="number" min="0" name="score_club_a" class="form-control" value="{{ old('score_club_a', $match->score_club_a) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Score — Club B</label>
                <input type="number" min="0" name="score_club_b" class="form-control" value="{{ old('score_club_b', $match->score_club_b) }}">
            </div>
        </div>
        <div style="display:flex; gap:12px;">
            <button type="submit" class="btn btn-accent">Save Changes</button>
            <a href="{{ route('admin.matches.show', $match) }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
