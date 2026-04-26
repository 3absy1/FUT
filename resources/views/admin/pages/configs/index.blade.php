@extends('admin.layouts.app')
@section('title', 'App Config')

@section('content')

<div class="section-header">
    <div>
        <div class="breadcrumb"><a href="{{ route('admin.dashboard') }}">Dashboard</a> / App Config</div>
        <div class="section-title">App Configuration</div>
    </div>
    <a href="{{ route('admin.configs.create') }}" class="btn btn-accent">+ New Key</a>
</div>

{{-- Info banner --}}
<div style="background:rgba(200,240,38,.06); border:1px solid rgba(200,240,38,.15); border-radius:12px; padding:16px 20px; margin-bottom:20px; display:flex; gap:12px; align-items:flex-start;">
    <span style="font-size:20px;">⚙️</span>
    <div>
        <div style="font-weight:600; font-size:14px; margin-bottom:3px;">Global App Settings</div>
        <div style="font-size:13px; color:var(--text-secondary);">These key-value pairs drive runtime configuration for the FútRivals app. Be careful when editing — changes take effect immediately.</div>
    </div>
</div>

<div class="filter-bar">
    <form method="GET" style="display:contents;">
        <input type="text" name="search" class="form-control" placeholder="Search key or value…" value="{{ request('search') }}">
        <button type="submit" class="btn btn-accent">Search</button>
        <a href="{{ route('admin.configs.index') }}" class="btn btn-ghost">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Key</th>
                    <th>Value</th>
                    <th>Updated</th>
                    <th style="width:130px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($configs as $config)
                <tr>
                    <td>
                        <code style="background:var(--bg-elevated); padding:4px 10px; border-radius:6px; font-size:13px; color:var(--accent); font-family:monospace;">{{ $config->key }}</code>
                    </td>
                    <td>
                        @php $val = $config->value; $isJson = str_starts_with(trim($val), '{') || str_starts_with(trim($val), '['); @endphp
                        @if($isJson)
                            <code style="background:var(--bg-elevated); padding:4px 10px; border-radius:6px; font-size:12px; color:var(--blue); font-family:monospace; display:inline-block; max-width:420px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $val }}</code>
                        @else
                            <span style="color:var(--text-primary); font-size:14px;">{{ Str::limit($val, 80) }}</span>
                        @endif
                    </td>
                    <td class="muted" style="font-size:12px;">{{ $config->updated_at->format('d M Y, H:i') }}</td>
                    <td>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.configs.edit', $config) }}" class="btn btn-ghost btn-sm">Edit</a>
                            <form method="POST" action="{{ route('admin.configs.destroy', $config) }}" onsubmit="return confirm('Delete config key « {{ $config->key }} »?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align:center; color:var(--text-muted); padding:60px;">
                        No config keys found. <a href="{{ route('admin.configs.create') }}" style="color:var(--accent);">Add one →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">{{ $configs->links('admin.pagination') }}</div>
</div>

@endsection
