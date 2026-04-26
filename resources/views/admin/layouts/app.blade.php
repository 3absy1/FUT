<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FútRivals Admin — @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --bg-base:      #0d1117;
            --bg-card:      #161b24;
            --bg-elevated:  #1c2333;
            --bg-hover:     #212840;
            --border:       rgba(255,255,255,0.07);
            --border-light: rgba(255,255,255,0.12);
            --accent:       #c8f026;
            --accent-dim:   rgba(200,240,38,0.15);
            --accent-glow:  rgba(200,240,38,0.35);
            --text-primary: #f0f4f8;
            --text-secondary:#8b99b0;
            --text-muted:   #4a5568;
            --green:        #22c55e;
            --red:          #ef4444;
            --orange:       #f97316;
            --purple:       #a855f7;
            --blue:         #3b82f6;
            --gold:         #f59e0b;
            --sidebar-w:    260px;
            --radius:       12px;
            --radius-sm:    8px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-base);
            color: var(--text-primary);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Sidebar ─────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--bg-card);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform .3s cubic-bezier(.4,0,.2,1);
        }

        .sidebar-logo {
            padding: 28px 24px 20px;
            border-bottom: 1px solid var(--border);
        }
        .sidebar-logo .brand {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px;
            letter-spacing: 1px;
            color: var(--accent);
            line-height: 1;
        }
        .sidebar-logo .brand span { color: var(--text-primary); }
        .sidebar-logo .sub {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--text-secondary);
            margin-top: 4px;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
            scrollbar-width: thin;
            scrollbar-color: var(--bg-elevated) transparent;
        }

        .nav-section-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--text-muted);
            padding: 12px 12px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all .2s;
            margin-bottom: 2px;
            position: relative;
        }
        .nav-item:hover {
            background: var(--bg-elevated);
            color: var(--text-primary);
        }
        .nav-item.active {
            background: var(--accent-dim);
            color: var(--accent);
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 20px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }
        .nav-item .icon { width: 18px; height: 18px; flex-shrink: 0; opacity: .8; }
        .nav-item.active .icon { opacity: 1; }

        .nav-badge {
            margin-left: auto;
            background: var(--accent);
            color: #0d1117;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .sidebar-footer {
            padding: 16px 12px;
            border-top: 1px solid var(--border);
        }

        /* ── Main content ─────────────────────────────── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── Topbar ───────────────────────────────────── */
        .topbar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 90;
        }

        .topbar-title {
            font-family: 'Syne', sans-serif;
            font-size: 20px;
            font-weight: 700;
            flex: 1;
        }

        .topbar-search {
            display: flex;
            align-items: center;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 8px 14px;
            gap: 8px;
            width: 280px;
            transition: border-color .2s;
        }
        .topbar-search:focus-within { border-color: var(--accent); }
        .topbar-search input {
            background: none;
            border: none;
            outline: none;
            color: var(--text-primary);
            font-size: 13px;
            width: 100%;
        }
        .topbar-search input::placeholder { color: var(--text-muted); }
        .topbar-search svg { color: var(--text-muted); width:14px; height:14px; flex-shrink:0; }

        .topbar-admin {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 14px 6px 6px;
            background: var(--bg-elevated);
            border-radius: 30px;
            border: 1px solid var(--border);
            cursor: pointer;
        }
        .topbar-avatar {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Bebas Neue', sans-serif;
            font-size: 14px;
            color: #0d1117;
        }
        .topbar-admin-name { font-size: 13px; font-weight: 600; }

        /* ── Page content ─────────────────────────────── */
        .page-content {
            padding: 32px;
            flex: 1;
        }

        /* ── Alert / Flash ────────────────────────────── */
        .alert {
            padding: 12px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success { background: rgba(34,197,94,.15); border: 1px solid rgba(34,197,94,.3); color: var(--green); }
        .alert-error   { background: rgba(239,68,68,.15);  border: 1px solid rgba(239,68,68,.3);  color: var(--red); }

        /* ── Cards ────────────────────────────────────── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .card-title {
            font-family: 'Syne', sans-serif;
            font-size: 16px;
            font-weight: 700;
        }

        /* ── Stat cards ───────────────────────────────── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px;
            position: relative;
            overflow: hidden;
            transition: border-color .25s, transform .25s;
        }
        .stat-card:hover { border-color: var(--border-light); transform: translateY(-2px); }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 80px; height: 80px;
            border-radius: 50%;
            background: var(--glow-color, var(--accent-dim));
            filter: blur(30px);
            pointer-events: none;
        }
        .stat-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-secondary);
            margin-bottom: 10px;
        }
        .stat-value {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 42px;
            line-height: 1;
            color: var(--text-primary);
        }
        .stat-value.accent { color: var(--accent); }
        .stat-sub {
            margin-top: 8px;
            font-size: 12px;
            color: var(--text-secondary);
        }
        .stat-icon {
            position: absolute;
            top: 18px; right: 18px;
            width: 36px; height: 36px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }

        /* ── Table ────────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-muted);
            padding: 10px 16px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }
        tbody tr {
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }
        tbody tr:hover { background: var(--bg-elevated); }
        tbody td { padding: 13px 16px; color: var(--text-primary); vertical-align: middle; }
        tbody td.muted { color: var(--text-secondary); }

        /* ── Badges ───────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .badge-green  { background: rgba(34,197,94,.15);  color: var(--green); }
        .badge-red    { background: rgba(239,68,68,.15);   color: var(--red); }
        .badge-orange { background: rgba(249,115,22,.15);  color: var(--orange); }
        .badge-accent { background: var(--accent-dim);     color: var(--accent); }
        .badge-purple { background: rgba(168,85,247,.15);  color: var(--purple); }
        .badge-blue   { background: rgba(59,130,246,.15);  color: var(--blue); }
        .badge-gray   { background: rgba(255,255,255,.07); color: var(--text-secondary); }

        /* ── Buttons ──────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all .2s;
            white-space: nowrap;
        }
        .btn-accent {
            background: var(--accent);
            color: #0d1117;
        }
        .btn-accent:hover { background: #d4f52e; box-shadow: 0 0 20px var(--accent-glow); }
        .btn-ghost {
            background: var(--bg-elevated);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }
        .btn-ghost:hover { border-color: var(--border-light); }
        .btn-danger {
            background: rgba(239,68,68,.15);
            color: var(--red);
            border: 1px solid rgba(239,68,68,.2);
        }
        .btn-danger:hover { background: rgba(239,68,68,.25); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-icon { padding: 8px; }

        /* ── Form controls ────────────────────────────── */
        .form-group { margin-bottom: 20px; }
        .form-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }
        .form-control {
            width: 100%;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 14px;
            color: var(--text-primary);
            font-size: 14px;
            outline: none;
            transition: border-color .2s;
            font-family: 'DM Sans', sans-serif;
        }
        .form-control:focus { border-color: var(--accent); }
        .form-control::placeholder { color: var(--text-muted); }
        select.form-control option { background: var(--bg-card); }

        /* ── Grid helpers ─────────────────────────────── */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }

        /* ── Pagination ───────────────────────────────── */
        .pagination {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            display: inline-flex; align-items: center; justify-content: center;
            width: 34px; height: 34px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all .15s;
        }
        .pagination a { background: var(--bg-elevated); color: var(--text-primary); border: 1px solid var(--border); }
        .pagination a:hover { border-color: var(--accent); color: var(--accent); }
        .pagination span.active { background: var(--accent); color: #0d1117; }
        .pagination span.dots { background: none; border: none; color: var(--text-muted); }

        /* ── Misc ─────────────────────────────────────── */
        .avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--bg-elevated);
            border: 2px solid var(--border-light);
            object-fit: cover;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 14px; color: var(--text-secondary);
            flex-shrink: 0;
        }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }

        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 20px;
        }
        .section-title {
            font-family: 'Syne', sans-serif;
            font-size: 22px;
            font-weight: 800;
        }
        .breadcrumb {
            font-size: 12px;
            color: var(--text-muted);
            display: flex; align-items: center; gap: 6px;
            margin-bottom: 6px;
        }
        .breadcrumb a { color: var(--text-secondary); text-decoration: none; }
        .breadcrumb a:hover { color: var(--accent); }

        /* Filter bar */
        .filter-bar {
            display: flex; gap: 12px; flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .filter-bar .form-control { max-width: 200px; padding: 8px 12px; font-size: 13px; }
        .filter-bar input.form-control { max-width: 280px; }

        /* Score display */
        .score-display {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 20px;
            letter-spacing: 2px;
        }

        /* Mobile toggle */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--text-primary);
            cursor: pointer;
            padding: 4px;
        }

        @media (max-width: 1024px) {
            :root { --sidebar-w: 240px; }
            .page-content { padding: 20px; }
            .topbar { padding: 0 20px; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-wrap { margin-left: 0; }
            .sidebar-toggle { display: block; }
            .topbar-search { display: none; }
            .stat-grid { grid-template-columns: repeat(2,1fr); }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar overlay for mobile -->
<div id="sidebar-overlay" onclick="closeSidebar()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99;"></div>

<!-- ── Sidebar ──────────────────────────────────────────── -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="brand">Fút<span>Rivals</span></div>
        <div class="sub">Super Admin Panel</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>

        <div class="nav-section-label">Management</div>
        <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Players
        </a>
        <a href="{{ route('admin.clubs.index') }}" class="nav-item {{ request()->routeIs('admin.clubs.*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            Clubs
        </a>
        <a href="{{ route('admin.stadiums.index') }}" class="nav-item {{ request()->routeIs('admin.stadiums.*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            Stadiums
        </a>
        <a href="{{ route('admin.matches.index') }}" class="nav-item {{ request()->routeIs('admin.matches.*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            Matches
        </a>

        <a href="{{ route('admin.tournaments.index') }}" class="nav-item {{ request()->routeIs('admin.tournaments.*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
            Tournaments
        </a>

        <div class="nav-section-label">Finance</div>
        <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Payments
        </a>

        <div class="nav-section-label">Configuration</div>
        <a href="{{ route('admin.areas.index') }}" class="nav-item {{ request()->routeIs('admin.areas.*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Areas
        </a>
        <a href="{{ route('admin.divisions.index') }}" class="nav-item {{ request()->routeIs('admin.divisions.*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"/></svg>
            Divisions
        </a>
        <a href="{{ route('admin.configs.index') }}" class="nav-item {{ request()->routeIs('admin.configs.*') ? 'active' : '' }}">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
            App Config
        </a>
    </nav>

    <div class="sidebar-footer">
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-ghost" style="width:100%; justify-content:center;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sign Out
            </button>
        </form>
    </div>
</aside>

<!-- ── Main ─────────────────────────────────────────────── -->
<div class="main-wrap">
    <header class="topbar">
        <button class="sidebar-toggle" onclick="toggleSidebar()">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="topbar-title">@yield('title', 'Dashboard')</div>

        {{-- Global search (hooks into page search if JS present) --}}
        <form class="topbar-search" method="GET" action="{{ request()->url() }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search…">
        </form>

        <div class="topbar-admin">
            <div class="topbar-avatar">SA</div>
            <span class="topbar-admin-name">Super Admin</span>
        </div>
    </header>

    <main class="page-content">
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error') || $errors->any())
            <div class="alert alert-error">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') ?? $errors->first() }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
function toggleSidebar() {
    const s = document.getElementById('sidebar');
    const o = document.getElementById('sidebar-overlay');
    s.classList.toggle('open');
    o.style.display = s.classList.contains('open') ? 'block' : 'none';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebar-overlay').style.display = 'none';
}
</script>

@stack('scripts')
</body>
</html>
