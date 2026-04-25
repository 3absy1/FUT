@extends('admin.layouts.app')
@section('title', 'Edit Player')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
            <a href="{{ route('admin.users.index') }}">Players</a> /
            <a href="{{ route('admin.users.show', $user) }}">{{ $user->nick_name }}</a> /
            Edit
        </div>
        <div class="section-title">Edit Player</div>
    </div>
    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-ghost">← Back</a>
</div>

<div class="card" style="max-width:720px;">
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf @method('PUT')
        <div class="grid-2">
            <div class="form-group">
                <label class="form-label">Nickname</label>
                <input type="text" name="nick_name" class="form-control" value="{{ old('nick_name', $user->nick_name) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Position</label>
                <select name="position" class="form-control">
                    <option value="">— None —</option>
                    <option value="attacker"   {{ old('position', $user->position) == 'attacker'    ? 'selected' : '' }}>Attacker</option>
                    <option value="midfielder" {{ old('position', $user->position) == 'midfielder'  ? 'selected' : '' }}>Midfielder</option>
                    <option value="defender"   {{ old('position', $user->position) == 'defender'    ? 'selected' : '' }}>Defender</option>
                    <option value="goal_keeper"{{ old('position', $user->position) == 'goal_keeper' ? 'selected' : '' }}>Goalkeeper</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Wallet Balance (LE)</label>
                <input type="number" step="0.01" name="wallet_balance" class="form-control" value="{{ old('wallet_balance', $user->wallet_balance) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Verified</label>
                <select name="is_verified" class="form-control">
                    <option value="1" {{ $user->is_verified ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ !$user->is_verified ? 'selected' : '' }}>No</option>
                </select>
            </div>
        </div>

        <div style="display:flex; gap:12px; margin-top:8px;">
            <button type="submit" class="btn btn-accent">Save Changes</button>
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>

@endsection
