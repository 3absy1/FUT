@extends('admin.layouts.app')
@section('title', 'Tournament Details')

@section('content')

@php
    $nameArr = is_array($tournament->name) ? $tournament->name : [];
    $nameEn  = $nameArr['en'] ?? 'Tournament';
    $statusBadge = ['upcoming'=>'badge-blue','ongoing'=>'badge-orange','completed'=>'badge-green','cancelled'=>'badge-red'];
    $totalRevenue = $tournament->payments->where('status','paid')->sum('amount');
    $totalPending = $tournament->payments->where('status','pending')->sum('amount');
@endphp

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.tournaments.index') }}">Tournaments</a> / {{ $nameEn }}</div>
        <div class="section-title">{{ $nameEn }}</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.tournaments.edit', $tournament) }}" class="btn btn-accent">Edit</a>
        <form method="POST" action="{{ route('admin.tournaments.destroy', $tournament) }}" onsubmit="return confirm('Delete this tournament?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

{{-- Hero banner --}}
<div class="card" style="margin-bottom:24px; background:linear-gradient(135deg, var(--bg-card) 0%, rgba(200,240,38,.05) 100%); border-color:rgba(200,240,38,.15);">
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:20px;">
        <div>
            <div style="font-family:'Bebas Neue',sans-serif; font-size:36px; letter-spacing:1px;">{{ $nameEn }}</div>
            @if(isset($nameArr['ar']) && $nameArr['ar'])
                <div style="font-size:18px; font-family:serif; color:var(--text-muted);" dir="rtl">{{ $nameArr['ar'] }}</div>
            @endif
            <div style="display:flex; gap:10px; margin-top:12px; flex-wrap:wrap;">
                <span class="badge {{ $statusBadge[$tournament->status] ?? 'badge-gray' }}" style="font-size:12px;">{{ strtoupper($tournament->status) }}</span>
                @if($tournament->stadium)
                    <span class="badge badge-gray">🏟️ {{ $tournament->stadium->name }}</span>
                @endif
                @if($tournament->minDivision)
                    <span class="badge badge-purple">Min: {{ $tournament->minDivision->localized_name }}</span>
                @endif
            </div>
        </div>
        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:20px; text-align:center;">
            <div>
                <div style="font-family:'Bebas Neue',sans-serif; font-size:42px; color:var(--accent);">{{ $tournament->participants->count() }}<span style="font-size:22px;color:var(--text-muted);">/{{ $tournament->max_teams }}</span></div>
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);">Teams</div>
            </div>
            <div>
                <div style="font-family:'Bebas Neue',sans-serif; font-size:42px; color:var(--green);">{{ number_format($totalRevenue,0) }}</div>
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);">LE Collected</div>
            </div>
            <div>
                <div style="font-family:'Bebas Neue',sans-serif; font-size:42px; color:var(--blue);">{{ $tournament->matches->count() }}</div>
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);">Matches</div>
            </div>
        </div>
    </div>
</div>

{{-- Key details row --}}
<div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
    @php
    $details = [
        ['Start Date',    $tournament->start_date?->format('d M Y') ?? '—',   'var(--blue)'],
        ['End Date',      $tournament->end_date?->format('d M Y') ?? '—',     'var(--orange)'],
        ['Entry Fee',     'LE ' . number_format($tournament->entry_fee_per_team,0), 'var(--green)'],
        ['Pending Revenue','LE ' . number_format($totalPending,0),             'var(--gold)'],
    ];
    @endphp
    @foreach($details as [$label, $val, $color])
    <div class="card" style="padding:18px; text-align:center;">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:8px;">{{ $label }}</div>
        <div style="font-weight:700; font-size:18px; color:{{ $color }};">{{ $val }}</div>
    </div>
    @endforeach
</div>

<div class="grid-2" style="margin-bottom:24px;">

    {{-- Participants --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Participating Clubs</div>
            <span class="badge badge-accent">{{ $tournament->participants->count() }} teams</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Rank</th><th>Club</th><th>Division</th><th>Score</th><th>Matches</th></tr></thead>
                <tbody>
                    @forelse($tournament->participants->sortBy('rank') as $p)
                    <tr>
                        <td>
                            @if($p->rank && $p->rank <= 3)
                                <span style="font-size:20px;">{{ ['🥇','🥈','🥉'][$p->rank-1] }}</span>
                            @elseif($p->rank)
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--text-muted);">#{{ $p->rank }}</span>
                            @else
                                <span class="muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($p->club)
                                <a href="{{ route('admin.clubs.show', $p->club) }}" style="color:var(--text-primary);text-decoration:none;font-weight:600;">{{ $p->club->name }}</a>
                            @else <span class="muted">—</span> @endif
                        </td>
                        <td class="muted" style="font-size:12px;">{{ $p->division?->localized_name ?? '—' }}</td>
                        <td>
                            @if($p->total_score !== null)
                                <span style="font-family:'Bebas Neue',sans-serif;font-size:18px;color:var(--accent);">{{ $p->total_score }}</span>
                            @else <span class="muted">—</span> @endif
                        </td>
                        <td class="muted">{{ $p->current_match ?? 0 }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:30px;">No participants yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Payments --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Payments</div>
            <span class="badge badge-green">LE {{ number_format($totalRevenue,0) }} paid</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Club</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse($tournament->payments as $payment)
                    <tr>
                        <td style="font-weight:600;">{{ $payment->club?->name ?? '—' }}</td>
                        <td style="color:var(--accent); font-weight:700;">LE {{ number_format($payment->amount,2) }}</td>
                        <td><span class="badge {{ ['paid'=>'badge-green','pending'=>'badge-orange','failed'=>'badge-red'][$payment->status] ?? 'badge-gray' }}">{{ $payment->status }}</span></td>
                        <td class="muted" style="font-size:12px;">{{ $payment->paid_at?->format('d M Y') ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:30px;">No payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Tournament Matches --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">Tournament Matches</div>
        <span class="badge badge-blue">{{ $tournament->matches->count() }} total</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Match</th><th>Date</th><th>Score</th><th>Result</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($tournament->matches->sortByDesc('scheduled_datetime') as $m)
                @php $resultMap = ['club_a_wins'=>'A Wins','club_b_wins'=>'B Wins','draw'=>'Draw']; @endphp
                <tr>
                    <td>
                        <span style="font-weight:600;">{{ $m->clubA?->name ?? '?' }}</span>
                        <span style="color:var(--text-muted); margin:0 8px; font-size:12px;">vs</span>
                        <span style="font-weight:600;">{{ $m->clubB?->name ?? '?' }}</span>
                    </td>
                    <td class="muted">{{ $m->scheduled_datetime?->format('d M Y, H:i') }}</td>
                    <td>
                        @if($m->status === 'completed')
                            <span class="score-display" style="color:var(--accent);">{{ $m->score_club_a }} – {{ $m->score_club_b }}</span>
                        @else <span class="muted">—</span> @endif
                    </td>
                    <td>
                        @if($m->result)
                            <span class="badge {{ ['club_a_wins'=>'badge-green','club_b_wins'=>'badge-blue','draw'=>'badge-orange'][$m->result] ?? 'badge-gray' }}">{{ $resultMap[$m->result] ?? '—' }}</span>
                        @else <span class="muted">—</span> @endif
                    </td>
                    <td><span class="badge {{ ['completed'=>'badge-green','in_progress'=>'badge-orange','pending'=>'badge-blue','cancelled'=>'badge-red'][$m->status] ?? 'badge-gray' }}">{{ $m->status }}</span></td>
                    <td><a href="{{ route('admin.matches.show', $m) }}" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:40px;">No matches scheduled.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
