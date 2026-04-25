@extends('admin.layouts.app')
@section('title', 'Payments')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / Payments</div>
        <div class="section-title">Payments</div>
    </div>
</div>

{{-- Revenue summary --}}
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
    <div class="card" style="text-align:center; padding:20px; border-color:rgba(34,197,94,.2);">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:8px;">Paid</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:36px;color:var(--green);">LE {{ number_format($totals['paid'],0) }}</div>
    </div>
    <div class="card" style="text-align:center; padding:20px; border-color:rgba(249,115,22,.2);">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:8px;">Pending</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:36px;color:var(--orange);">LE {{ number_format($totals['pending'],0) }}</div>
    </div>
    <div class="card" style="text-align:center; padding:20px; border-color:rgba(239,68,68,.2);">
        <div style="font-size:10px;text-transform:uppercase;letter-spacing:2px;color:var(--text-muted);margin-bottom:8px;">Failed</div>
        <div style="font-family:'Bebas Neue',sans-serif;font-size:36px;color:var(--red);">LE {{ number_format($totals['failed'],0) }}</div>
    </div>
</div>

<div class="filter-bar">
    <form method="GET" style="display:contents;">
        <select name="status" class="form-control">
            <option value="">All statuses</option>
            <option value="paid"    {{ request('status')=='paid'?'selected':'' }}>Paid</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
            <option value="failed"  {{ request('status')=='failed'?'selected':'' }}>Failed</option>
        </select>
        <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="From">
        <input type="date" name="to"   class="form-control" value="{{ request('to') }}"   placeholder="To">
        <button type="submit" class="btn btn-accent">Filter</button>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-ghost">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Club</th>
                    <th>Match / Tournament</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Paid At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td class="muted">{{ $payment->id }}</td>
                    <td style="font-weight:600;">{{ $payment->club?->name ?? '—' }}</td>
                    <td class="muted">
                        @if($payment->match_id)
                            <a href="{{ route('admin.matches.show', $payment->match_id) }}" style="color:var(--accent); text-decoration:none;">Match #{{ $payment->match_id }}</a>
                        @elseif($payment->tournament_id)
                            Tournament #{{ $payment->tournament_id }}
                        @else —
                        @endif
                    </td>
                    <td style="font-weight:700; color:var(--accent);">LE {{ number_format($payment->amount, 2) }}</td>
                    <td>
                        <span class="badge {{ ['paid'=>'badge-green','pending'=>'badge-orange','failed'=>'badge-red'][$payment->status] ?? 'badge-gray' }}">{{ $payment->status }}</span>
                    </td>
                    <td class="muted">{{ $payment->paid_at?->format('d M Y, H:i') ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:60px;">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $payments->links('admin.pagination') }}</div>
</div>

@endsection
