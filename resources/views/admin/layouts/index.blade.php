@extends('admin.layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- KPI Stats --}}
<div class="stat-grid">

    <div class="stat-card" style="--glow-color: rgba(200,240,38,.12);">
        <div class="stat-icon" style="background:var(--accent-dim); color:var(--accent);">⚽</div>
        <div class="stat-label">Total Matches</div>
        <div class="stat-value accent">{{ number_format($stats['total_matches']) }}</div>
        <div class="stat-sub">{{ $matchCounts['daily'] }} today · {{ $matchCounts['weekly'] }} this week</div>
    </div>

    <div class="stat-card" style="--glow-color: rgba(59,130,246,.1);">
        <div class="stat-icon" style="background:rgba(59,130,246,.15); color:var(--blue);">👥</div>
        <div class="stat-label">Players</div>
        <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
        <div class="stat-sub">+{{ $newUsers }} last 30 days</div>
    </div>

    <div class="stat-card" style="--glow-color: rgba(34,197,94,.1);">
        <div class="stat-icon" style="background:rgba(34,197,94,.15); color:var(--green);">💰</div>
        <div class="stat-label">Monthly Revenue</div>
        <div class="stat-value" style="font-size:32px;">{{ number_format($revenue['monthly'], 0) }}</div>
        <div class="stat-sub" style="color:var(--green);">LE · All time: {{ number_format($revenue['total'], 0) }}</div>
    </div>

    <div class="stat-card" style="--glow-color: rgba(249,115,22,.1);">
        <div class="stat-icon" style="background:rgba(249,115,22,.15); color:var(--orange);">🏟️</div>
        <div class="stat-label">Stadiums</div>
        <div class="stat-value">{{ number_format($stats['total_stadiums']) }}</div>
        <div class="stat-sub">{{ $stats['total_pitches'] }} pitches total</div>
    </div>

    <div class="stat-card" style="--glow-color: rgba(168,85,247,.1);">
        <div class="stat-icon" style="background:rgba(168,85,247,.15); color:var(--purple);">🛡️</div>
        <div class="stat-label">Clubs</div>
        <div class="stat-value">{{ number_format($stats['total_clubs']) }}</div>
        <div class="stat-sub">Ranked clubs</div>
    </div>

    <div class="stat-card" style="--glow-color: rgba(200,240,38,.08);">
        <div class="stat-icon" style="background:rgba(239,68,68,.15); color:var(--red);">🔴</div>
        <div class="stat-label">Live Matches</div>
        <div class="stat-value" style="color:var(--red);">{{ $stats['active_matches'] }}</div>
        <div class="stat-sub">Currently in progress</div>
    </div>

</div>

{{-- Revenue split --}}
<div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; margin-bottom:28px;">
    <div class="card" style="text-align:center; padding:20px;">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:8px;">Daily Revenue</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:32px;color:var(--accent);">{{ number_format($revenue['daily'],0) }} LE</div>
    </div>
    <div class="card" style="text-align:center; padding:20px;">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:8px;">Weekly Revenue</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:32px;color:var(--accent);">{{ number_format($revenue['weekly'],0) }} LE</div>
    </div>
    <div class="card" style="text-align:center; padding:20px;">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:8px;">Matches This Month</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:32px;color:var(--blue);">{{ $matchCounts['monthly'] }}</div>
    </div>
</div>

{{-- Charts row --}}
<div class="grid-2" style="margin-bottom:28px;">

    {{-- Monthly matches chart --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Matches / Month</div>
            <span class="badge badge-accent">Last 12 months</span>
        </div>
        <canvas id="monthlyMatchesChart" height="200"></canvas>
    </div>

    {{-- Monthly revenue chart --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Revenue / Month</div>
            <span class="badge badge-green">LE</span>
        </div>
        <canvas id="monthlyRevenueChart" height="200"></canvas>
    </div>

</div>

{{-- Daily chart + Top Stadiums --}}
<div class="grid-2" style="margin-bottom:28px;">

    <div class="card">
        <div class="card-header">
            <div class="card-title">Matches — Last 7 Days</div>
        </div>
        <canvas id="dailyChart" height="180"></canvas>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Top Stadiums</div>
            <a href="{{ route('admin.stadiums.index') }}" class="btn btn-ghost btn-sm">View all</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Stadium</th>
                        <th>Matches</th>
                        <th>Rating</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topStadiums as $i => $s)
                    <tr>
                        <td class="muted">{{ $i+1 }}</td>
                        <td>
                            <div style="font-weight:600;">{{ $s->name }}</div>
                        </td>
                        <td><span class="badge badge-accent">{{ $s->matches_count }}</span></td>
                        <td style="color:var(--gold);">★ {{ number_format($s->rating, 1) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Recent matches --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">Recent Matches</div>
        <a href="{{ route('admin.matches.index') }}" class="btn btn-ghost btn-sm">View all</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Match</th>
                    <th>Stadium</th>
                    <th>Date</th>
                    <th>Score</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentMatches as $match)
                <tr>
                    <td>
                        <div style="font-weight:600; font-size:14px;">
                            {{ $match->clubA?->name ?? '—' }}
                            <span style="color:var(--text-muted); margin:0 6px;">vs</span>
                            {{ $match->clubB?->name ?? '—' }}
                        </div>
                    </td>
                    <td class="muted">{{ $match->stadium?->name ?? '—' }}</td>
                    <td class="muted">{{ $match->scheduled_datetime?->format('d M Y, H:i') ?? '—' }}</td>
                    <td>
                        @if($match->status === 'completed')
                            <span class="score-display">{{ $match->score_club_a }} – {{ $match->score_club_b }}</span>
                        @else
                            <span class="muted">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $badgeMap = [
                                'completed'   => 'badge-green',
                                'in_progress' => 'badge-orange',
                                'pending'     => 'badge-blue',
                                'cancelled'   => 'badge-red',
                            ];
                        @endphp
                        <span class="badge {{ $badgeMap[$match->status] ?? 'badge-gray' }}">{{ $match->status }}</span>
                    </td>
                    <td>
                        <a href="{{ route('admin.matches.show', $match) }}" class="btn btn-ghost btn-sm">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:40px;">No matches yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
const chartDefaults = {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: '#8b99b0', font: { size: 11 } } },
        y: { grid: { color: 'rgba(255,255,255,.04)' }, ticks: { color: '#8b99b0', font: { size: 11 } }, beginAtZero: true },
    },
};

// Monthly Matches
new Chart(document.getElementById('monthlyMatchesChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($monthlyMatchesChart->pluck('label')) !!},
        datasets: [{
            data: {!! json_encode($monthlyMatchesChart->pluck('count')) !!},
            backgroundColor: 'rgba(200,240,38,.25)',
            borderColor: '#c8f026',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: chartDefaults,
});

// Monthly Revenue
new Chart(document.getElementById('monthlyRevenueChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyRevenueChart->pluck('label')) !!},
        datasets: [{
            data: {!! json_encode($monthlyRevenueChart->pluck('total')) !!},
            borderColor: '#22c55e',
            backgroundColor: 'rgba(34,197,94,.08)',
            borderWidth: 2.5,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#22c55e',
            pointRadius: 4,
        }]
    },
    options: chartDefaults,
});

// Daily last 7 days
new Chart(document.getElementById('dailyChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($dailyMatchesChart->pluck('label')) !!},
        datasets: [{
            data: {!! json_encode($dailyMatchesChart->pluck('count')) !!},
            backgroundColor: 'rgba(59,130,246,.3)',
            borderColor: '#3b82f6',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: chartDefaults,
});
</script>
@endpush
