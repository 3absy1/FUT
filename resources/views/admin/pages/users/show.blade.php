@extends('admin.layouts.app')
@section('title', 'Player Profile')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> /
            <a href="{{ route('admin.users.index') }}">Players</a> /
            {{ $user->nick_name ?? 'Player' }}
        </div>
        <div class="section-title">Player Profile</div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-accent">Edit Player</a>
        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this player?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

<div class="grid-2" style="margin-bottom:24px;">

    {{-- Player card (FIFA style) --}}
    <div class="card">
        <div style="display:flex; align-items:center; gap:24px; margin-bottom:24px;">
            <div class="avatar" style="width:72px;height:72px;font-size:28px;border-color:var(--accent);">
                {{ strtoupper(substr($user->nick_name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <div style="font-family:'Bebas Neue',sans-serif; font-size:30px; letter-spacing:1px;">
                    {{ $user->nick_name ?? (is_array($user->name) ? ($user->name['en'] ?? '') : $user->name) }}
                </div>
                <div style="color:var(--text-secondary); font-size:13px;">{{ $user->email }}</div>
                <div style="margin-top:8px; display:flex; gap:8px; flex-wrap:wrap;">
                    @if($user->position)
                        <span class="badge badge-blue">{{ ucfirst(str_replace('_',' ',$user->position)) }}</span>
                    @endif
                    @if($user->is_verified)
                        <span class="badge badge-green">Verified</span>
                    @else
                        <span class="badge badge-orange">Unverified</span>
                    @endif
                    @if($user->division)
                        <span class="badge badge-purple">{{ $user->division->localized_name }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Rating block --}}
        @if($user->overallRating())
        <div style="background:var(--bg-elevated); border-radius:12px; padding:20px; margin-bottom:20px; display:flex; align-items:center; gap:24px;">
            <div style="font-family:'Bebas Neue',sans-serif; font-size:72px; line-height:1; color:var(--accent);">{{ $user->overallRating() }}</div>
            <div style="flex:1;">
                @php
                $stats = $user->position === 'goal_keeper'
                    ? ['DIV'=>$user->gk_diving,'HAN'=>$user->gk_handling,'KIC'=>$user->gk_kicking,'REF'=>$user->gk_reflexes,'POS'=>$user->gk_positioning]
                    : ['PAC'=>$user->pac,'SHO'=>$user->sho,'PAS'=>$user->pas,'DRI'=>$user->dri,'DEF'=>$user->def,'PHY'=>$user->phy];
                @endphp
                @foreach($stats as $label => $val)
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:5px;">
                    <span style="font-size:10px;font-weight:700;color:var(--text-muted);width:28px;">{{ $label }}</span>
                    <div style="flex:1; height:4px; background:var(--bg-base); border-radius:2px; overflow:hidden;">
                        <div style="width:{{ $val ?? 0 }}%; height:100%; background:{{ ($val ?? 0) >= 85 ? 'var(--accent)' : (($val ?? 0) >= 70 ? 'var(--blue)' : 'var(--text-muted)') }}; border-radius:2px;"></div>
                    </div>
                    <span style="font-weight:700; font-size:13px; width:28px; text-align:right; color:{{ ($val ?? 0) >= 85 ? 'var(--accent)' : 'var(--text-primary)' }};">{{ $val ?? '—' }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Info rows --}}
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            @php
            $info = [
                'Phone'          => $user->phone ?? '—',
                'Goals'          => ($user->goals_scored ?? 0) . ' G / ' . ($user->assists_count ?? 0) . ' A',
                'EXP'            => number_format($user->exp ?? 0),
                'Wallet'         => 'LE ' . number_format($user->wallet_balance ?? 0, 2),
                'Birth Date'     => $user->birth_date?->format('d M Y') ?? '—',
                'Member Since'   => $user->created_at->format('d M Y'),
            ];
            @endphp
            @foreach($info as $label => $val)
            <div style="background:var(--bg-elevated); border-radius:8px; padding:14px;">
                <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:4px;">{{ $label }}</div>
                <div style="font-weight:600; font-size:14px;">{{ $val }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Club & Match history --}}
    <div style="display:flex; flex-direction:column; gap:20px;">

        {{-- Club membership --}}
        <div class="card">
            <div class="card-title" style="margin-bottom:16px;">Club Membership</div>
            @forelse($user->clubMembers as $member)
            <div style="display:flex; align-items:center; justify-content:space-between; padding:12px; background:var(--bg-elevated); border-radius:8px; margin-bottom:8px;">
                <div>
                    <div style="font-weight:600;">{{ $member->club?->name ?? '—' }}</div>
                    <div style="font-size:12px;color:var(--text-muted);">{{ $member->is_active ? 'Active' : 'Inactive' }}</div>
                </div>
                @if($member->club)
                    <a href="{{ route('admin.clubs.show', $member->club) }}" class="btn btn-ghost btn-sm">View</a>
                @endif
            </div>
            @empty
            <div style="color:var(--text-muted); font-size:13px;">No club memberships.</div>
            @endforelse
        </div>

        {{-- Recent match players --}}
        <div class="card" style="flex:1;">
            <div class="card-title" style="margin-bottom:16px;">Recent Matches</div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Match</th><th>Club</th><th>Result</th></tr></thead>
                    <tbody>
                        @forelse($user->matchPlayers->take(8) as $mp)
                        <tr>
                            <td>
                                @if($mp->match)
                                    <a href="{{ route('admin.matches.show', $mp->match) }}" style="color:var(--accent); text-decoration:none; font-size:13px;">
                                        Match #{{ $mp->match_id }}
                                    </a>
                                    <div style="font-size:11px;color:var(--text-muted);">{{ $mp->match->scheduled_datetime?->format('d M Y') }}</div>
                                @else #{{ $mp->match_id }} @endif
                            </td>
                            <td class="muted" style="font-size:12px;">{{ $mp->match?->stadium?->name ?? '—' }}</td>
                            <td>
                                @if($mp->match?->status === 'completed')
                                    <span class="badge badge-green">Done</span>
                                @else
                                    <span class="badge badge-gray">{{ $mp->match?->status ?? '—' }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="color:var(--text-muted);text-align:center;padding:30px;">No matches played.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
