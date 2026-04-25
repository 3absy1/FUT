@extends('admin.layouts.app')
@section('title', 'Match Details')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.matches.index') }}">Matches</a> / #{{ $match->id }}</div>
        <div class="section-title">Match #{{ $match->id }}</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.matches.edit', $match) }}" class="btn btn-accent">Edit</a>
        <form method="POST" action="{{ route('admin.matches.destroy', $match) }}" onsubmit="return confirm('Delete match?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

{{-- Scoreboard --}}
<div class="card" style="margin-bottom:24px; text-align:center;">
    @php
    $badgeMap = ['completed'=>'badge-green','in_progress'=>'badge-orange','pending'=>'badge-blue','cancelled'=>'badge-red'];
    @endphp
    <div style="margin-bottom:6px;"><span class="badge {{ $badgeMap[$match->status] ?? 'badge-gray' }}" style="font-size:12px;">{{ strtoupper($match->status) }}</span></div>
    <div style="display:flex; align-items:center; justify-content:center; gap:32px; padding:24px 0;">
        <div style="flex:1; text-align:right;">
            <div style="font-family:'Syne',sans-serif; font-size:22px; font-weight:800;">{{ $match->clubA?->name ?? '—' }}</div>
            <div style="font-size:11px;color:var(--text-muted);">Club A</div>
        </div>
        <div style="font-family:'Bebas Neue',sans-serif; font-size:56px; line-height:1; color:var(--accent); letter-spacing:4px;">
            @if($match->status === 'completed')
                {{ $match->score_club_a }} – {{ $match->score_club_b }}
            @else
                – vs –
            @endif
        </div>
        <div style="flex:1; text-align:left;">
            <div style="font-family:'Syne',sans-serif; font-size:22px; font-weight:800;">{{ $match->clubB?->name ?? '—' }}</div>
            <div style="font-size:11px;color:var(--text-muted);">Club B</div>
        </div>
    </div>
    <div style="color:var(--text-secondary); font-size:13px;">
        🏟️ {{ $match->stadium?->name ?? '—' }} · 📅 {{ $match->scheduled_datetime?->format('d M Y, H:i') ?? '—' }}
        @if($match->pitch) · ⚽ {{ $match->pitch->name }} @endif
    </div>
</div>

<div class="grid-2" style="margin-bottom:24px;">

    {{-- Match info --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;">Match Details</div>
        @php
        $resultMap = ['club_a_wins'=>'Club A Wins 🏆','club_b_wins'=>'Club B Wins 🏆','draw'=>'Draw 🤝'];
        $infoRows = [
            'Result'         => $resultMap[$match->result] ?? '—',
            'Tournament'     => $match->tournament?->name ?? 'None',
            'Scheduled'      => $match->scheduled_datetime?->format('d M Y, H:i') ?? '—',
        ];
        @endphp
        <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach($infoRows as $label => $val)
            <div style="display:flex; justify-content:space-between; padding:12px 16px; background:var(--bg-elevated); border-radius:8px;">
                <span style="color:var(--text-muted); font-size:12px; text-transform:uppercase; letter-spacing:1px;">{{ $label }}</span>
                <span style="font-weight:600; font-size:14px;">{{ $val }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Payments --}}
    <div class="card">
        <div class="card-title" style="margin-bottom:16px;">Payments</div>
        @forelse($match->payments as $payment)
        <div style="display:flex; align-items:center; justify-content:space-between; padding:12px 16px; background:var(--bg-elevated); border-radius:8px; margin-bottom:8px;">
            <div>
                <div style="font-weight:600;">LE {{ number_format($payment->amount, 2) }}</div>
                <div style="font-size:11px;color:var(--text-muted);">{{ $payment->club?->name ?? '—' }}</div>
            </div>
            <span class="badge {{ ['paid'=>'badge-green','pending'=>'badge-orange','failed'=>'badge-red'][$payment->status] ?? 'badge-gray' }}">{{ $payment->status }}</span>
        </div>
        @empty
        <div style="color:var(--text-muted); font-size:13px;">No payments recorded.</div>
        @endforelse
    </div>

</div>

{{-- Players in match --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">Players in this Match</div>
        <span class="badge badge-accent">{{ $match->matchPlayers->count() }} players</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Player</th><th>Club</th><th>Position</th><th>Rating</th></tr></thead>
            <tbody>
                @forelse($match->matchPlayers as $mp)
                <tr>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="avatar">{{ strtoupper(substr($mp->user?->nick_name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <a href="{{ $mp->user_id ? route('admin.users.show', $mp->user_id) : '#' }}" style="color:var(--text-primary);text-decoration:none;font-weight:600;">
                                    {{ $mp->user?->nick_name ?? '—' }}
                                </a>
                            </div>
                        </div>
                    </td>
                    <td class="muted">{{ $mp->club_id ? 'Club #' . $mp->club_id : '—' }}</td>
                    <td>
                        @if($mp->user?->position)
                        <span class="badge badge-blue">{{ ucfirst(str_replace('_',' ',$mp->user->position)) }}</span>
                        @else <span class="muted">—</span> @endif
                    </td>
                    <td>
                        @if($mp->user?->overallRating())
                            <span style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--accent);">{{ $mp->user->overallRating() }}</span>
                        @else <span class="muted">—</span> @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:40px;">No players recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
