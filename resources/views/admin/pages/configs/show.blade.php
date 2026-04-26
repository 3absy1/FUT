@extends('admin.layouts.app')
@section('title', $stadium->name)

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / <a href="{{ route('admin.stadiums.index') }}">Stadiums</a> / {{ $stadium->name }}</div>
        <div class="section-title">{{ $stadium->name }}</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.stadiums.edit', $stadium) }}" class="btn btn-accent">Edit</a>
        <form method="POST" action="{{ route('admin.stadiums.destroy', $stadium) }}" onsubmit="return confirm('Delete stadium?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

<div class="grid-2" style="margin-bottom:24px;">
    <div class="card">
        <div class="card-title" style="margin-bottom:20px;">Stadium Info</div>
        @php
        $info = [
            'Area'         => $stadium->area?->localized_name ?? data_get($stadium->area?->name, 'en') ?? data_get($stadium->area?->name, 'ar') ?? '—',
            'Location'     => $stadium->location ?? '—',
            'Price/hr'     => 'LE ' . number_format($stadium->price_per_hour, 0),
            'Rating'       => '★ ' . number_format($stadium->rating ?? 0, 1),
            'WhatsApp'     => $stadium->whatsapp_number ?? '—',
            'Total Pitches'=> $stadium->pitches->count(),
            'Total Matches'=> $stadium->matches->count(),
            'Created'      => $stadium->created_at->format('d M Y'),
        ];
        @endphp
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            @foreach($info as $label => $val)
            <div style="background:var(--bg-elevated); border-radius:8px; padding:14px;">
                <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:4px;">{{ $label }}</div>
                <div style="font-weight:600;">{{ $val }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-title" style="margin-bottom:16px;">Pitches</div>
        @forelse($stadium->pitches->sortBy('sort_order') as $pitch)
        <div style="background:var(--bg-elevated); border-radius:8px; padding:14px; margin-bottom:8px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <div style="font-weight:600;">{{ $pitch->name }}</div>
                <div style="font-size:11px;color:var(--text-muted);">Order #{{ $pitch->sort_order }}</div>
            </div>
            <span class="badge badge-blue">{{ $pitch->matches->count() ?? 0 }} matches</span>
        </div>
        @empty
        <div style="color:var(--text-muted); font-size:13px;">No pitches added yet.</div>
        @endforelse
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title">Recent Matches at this Stadium</div>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Teams</th><th>Date</th><th>Score</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse($stadium->matches as $match)
                <tr>
                    <td>
                        <span style="font-weight:600;">{{ $match->clubA?->name ?? '?' }}</span>
                        <span style="color:var(--text-muted); margin:0 8px;">vs</span>
                        <span style="font-weight:600;">{{ $match->clubB?->name ?? '?' }}</span>
                    </td>
                    <td class="muted">{{ $match->scheduled_datetime?->format('d M Y, H:i') }}</td>
                    <td>
                        @if($match->status === 'completed')
                            <span class="score-display">{{ $match->score_club_a }} – {{ $match->score_club_b }}</span>
                        @else <span class="muted">—</span> @endif
                    </td>
                    <td><span class="badge {{ ['completed'=>'badge-green','in_progress'=>'badge-orange','pending'=>'badge-blue','cancelled'=>'badge-red'][$match->status] ?? 'badge-gray' }}">{{ $match->status }}</span></td>
                    <td><a href="{{ route('admin.matches.show', $match) }}" class="btn btn-ghost btn-sm">View</a></td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:40px;">No matches.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
