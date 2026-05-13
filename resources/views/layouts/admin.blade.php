<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RenTara — @yield('title', 'Admin')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --sidebar-w: 80px;
            --sidebar-expanded: 260px;
            --brand: #F59E0B;
            --brand-dark: #D97706;
            --navy: #1E293B;
            --page-bg: #F1F5F9;
            --card-bg: #FFFFFF;
            --border: #E2E8F0;
            --text: #1E293B;
            --muted: #64748B;

            /* Sidebar dark theme */
            --sb-bg: #0F172A;
            --sb-border: #1E293B;
            --sb-text: #94A3B8;
            --sb-active-bg: rgba(245,158,11,.15);
            --sb-active-text: #F59E0B;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--page-bg);
            color: var(--text);
        }

        /* ════════════════════════════════════════
           SIDEBAR — Dark with 3D flip nav items
        ════════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--sb-bg);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 200;
            transition: width .4s cubic-bezier(.4,0,.2,1);
            overflow: hidden;
            border-right: 1px solid var(--sb-border);
        }

        .sidebar:hover {
            width: var(--sidebar-expanded);
        }

        /* Top brand area */
        .sb-brand {
            width: 100%;
            padding: 25px 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--sb-bg);
            border-bottom: 1px solid var(--sb-border);
            min-height: 96px;
            overflow: hidden;
        }

        .brand-link {
            width: 100%;
            max-width: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: inherit;
            text-decoration: none;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            object-fit: contain;
            border-radius: 10px;
            flex-shrink: 0;
        }

        .brand-wordmark {
            min-width: 0;
            opacity: 0;
            width: 0;
            overflow: hidden;
            transform: translateX(-8px);
            transition: opacity .25s, width .25s, transform .25s;
            white-space: nowrap;
        }

        .sidebar:hover .brand-wordmark {
            opacity: 1;
            width: auto;
            transform: translateX(0);
        }

        .brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.15rem;
            font-weight: 700;
            line-height: 1;
        }

        .brand-ren {
            color: #F1F5F9;
        }

        .brand-tara {
            color: #F59E0B;
        }

        .brand-sub {
            margin-top: 3px;
            font-size: .58rem;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #94A3B8;
        }

        /* ── 3D Flip Nav Items (from Navigation_Bar) ── */
        .sb-nav {
            flex: 1;
            width: 100%;
            padding: 16px 0;
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sb-nav li {
            position: relative;
            width: 100%;
            padding: 0 12px;
        }

        .sb-nav li a {
            display: flex;
            align-items: center;
            height: 46px;
            padding: 0 8px;
            border-radius: 10px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            transition: background .2s;
            perspective: 600px;
        }

        .sb-nav li a:hover,
        .sb-nav li a.active {
            background: var(--sb-active-bg);
        }

        /* Icon container — 3D flip */
        .nav-icon-wrap {
            width: 28px; height: 28px;
            position: relative;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transform-style: preserve-3d;
            transition: transform .5s cubic-bezier(.4,0,.2,1);
        }

        .sb-nav li a:hover .nav-icon-wrap,
        .sb-nav li a.active .nav-icon-wrap {
            transform: rotateX(-360deg);
        }

        .nav-icon-wrap i {
            font-size: 1.25rem;
            color: var(--sb-text);
            transition: color .3s;
        }

        .sb-nav li a:hover .nav-icon-wrap i,
        .sb-nav li a.active .nav-icon-wrap i {
            color: var(--brand);
        }

        /* Nav text — slides in from left on sidebar expand */
        .nav-text {
            font-size: .85rem;
            font-weight: 600;
            color: var(--sb-text);
            white-space: nowrap;
            margin-left: 12px;
            opacity: 0;
            transform: translateX(-8px);
            transition: opacity .25s, transform .25s, color .25s;
        }

        .sidebar:hover .nav-text {
            opacity: 1;
            transform: translateX(0);
        }

        .sb-nav li a:hover .nav-text,
        .sb-nav li a.active .nav-text {
            color: var(--sb-active-text);
        }

        /* Active indicator dot */
        .sb-nav li a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3px;
            height: 22px;
            background: var(--brand);
            border-radius: 0 3px 3px 0;
        }

        /* ── Sidebar Footer ── */
        .sb-footer {
            width: 100%;
            border-top: 1px solid var(--sb-border);
            padding: 16px 12px;
            overflow: hidden;
        }

        .sb-user {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            min-height: 44px;
        }

        .user-av {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--brand), var(--brand-dark));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: .9rem; color: #0A0800;
            flex-shrink: 0;
        }

        .user-info {
            opacity: 0;
            transform: translateX(-8px);
            transition: opacity .25s, transform .25s;
            white-space: nowrap;
            overflow: hidden;
        }

        .sidebar:hover .user-info {
            opacity: 1;
            transform: translateX(0);
        }

        .user-name { font-size: .82rem; font-weight: 700; color: #F1F5F9; }
        .user-role { font-size: .65rem; font-weight: 600; color: var(--brand); text-transform: uppercase; letter-spacing: .5px; }

        .logout-form { width: 100%; }

        .logout-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 9px;
            background: rgba(245,158,11,.12);
            border: 1px solid rgba(245,158,11,.2);
            border-radius: 9px;
            color: var(--brand);
            font-family: 'DM Sans', sans-serif;
            font-size: .8rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s;
            overflow: hidden;
        }
        .logout-btn:hover { background: rgba(245,158,11,.2); }

        .logout-text {
            opacity: 0;
            transform: translateX(-6px);
            transition: opacity .25s, transform .25s;
            white-space: nowrap;
        }
        .sidebar:hover .logout-text {
            opacity: 1;
            transform: translateX(0);
        }

        /* ════════════════════════════════════════
           MAIN CONTENT
        ════════════════════════════════════════ */
        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            transition: margin-left .4s cubic-bezier(.4,0,.2,1);
        }

        /* ── Floating top bar (from scroll_effect glassmorphism) ── */
        .topbar {
            position: sticky;
            top: 12px;
            z-index: 100;
            margin: 12px 24px 0;
            background: rgba(255,255,255,.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,.9);
            border-radius: 14px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px rgba(0,0,0,.06);
        }

        .topbar-title {
            font-size: .95rem;
            font-weight: 700;
            color: var(--navy);
        }

        .topbar-date {
            font-size: .8rem;
            color: var(--muted);
            font-weight: 500;
        }

        /* Page content area */
        .page-body {
            padding: 24px 24px 32px;
        }

        /* ════════════════════════════════════════
           SHARED COMPONENTS
        ════════════════════════════════════════ */

        /* Cards */
        .stat-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 120px;
            background: var(--card-bg);
            border-radius: 14px;
            padding: .75rem 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.05);
            border: 1px solid var(--border);
            transition: box-shadow .2s, transform .2s;
        }

        /* Table card */
        .table-card {
            background: var(--card-bg);
            border-radius: 14px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
            overflow: hidden;
        }

        /* Chart card */
        .chart-card {
            background: var(--card-bg);
            border-radius: 14px;
            padding: 1.5rem;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }

        .chart-card h6 {
            font-size:.75rem;font-weight:800;text-transform:uppercase;
            letter-spacing:1px;color:var(--muted);margin-bottom:1.25rem;
        }

        /* Table */
        .table thead th {
            background: #F8FAFC;
            color: var(--muted);
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .6px;
            border-bottom: 1px solid var(--border);
            padding: .85rem 1rem;
        }
        .table tbody td {
            padding: .85rem 1rem;
            vertical-align: middle;
            font-size: .875rem;
            border-bottom: 1px solid #F1F5F9;
        }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover { background: #FAFAFA; }

        /* Buttons */
        .btn-brand {
            background: var(--brand);
            color: #0A0800;
            font-weight: 700;
            border: none;
            border-radius: 9px;
            padding: .5rem 1.1rem;
            font-size: .85rem;
            font-family: 'DM Sans', sans-serif;
            transition: background .15s, transform .15s;
        }
        .btn-brand:hover { background: var(--brand-dark); color: #0A0800; transform: translateY(-1px); }

        /* Status badges */
        .status-badge {
            display: inline-flex; align-items: center;
            padding: .28rem .72rem;
            border-radius: 9999px;
            font-size: .7rem; font-weight: 700;
            letter-spacing: .3px; white-space: nowrap;
        }
        .badge-pending    { background:#FEF3C7;color:#92400E }
        .badge-confirmed  { background:#DBEAFE;color:#1E40AF }
        .badge-completed  { background:#D1FAE5;color:#065F46 }
        .badge-cancelled  { background:#FEE2E2;color:#991B1B }
        .badge-available  { background:#D1FAE5;color:#065F46 }
        .badge-rented     { background:#FEE2E2;color:#991B1B }
        .badge-active     { background:#D1FAE5;color:#065F46 }
        .badge-inactive   { background:#FEE2E2;color:#991B1B }

        /* Form section titles */
        .form-section-title {
            font-size:.68rem;font-weight:800;letter-spacing:1.5px;
            text-transform:uppercase;color:var(--navy);
            border-bottom:2px solid var(--brand);padding-bottom:.4rem;margin-bottom:1rem;
        }

        /* Page header */
        .page-header { display:flex;justify-content:space-between;align-items:center;margin-bottom:1.75rem; }
        .page-title  { font-size:1.5rem;font-weight:800;color:var(--navy);margin:0; }
        .page-subtitle{ font-size:.875rem;color:var(--muted);margin:.2rem 0 0; }

        /* Toast */
        .toast-container { position:fixed;top:1.25rem;right:1.25rem;z-index:9999;min-width:300px; }

        @media (max-width: 992px) {
            .page-header { align-items:flex-start; flex-direction:column; gap:.5rem; }
            .topbar { margin:10px 14px 0; }
            .page-body { padding:18px 14px 96px; }
            .table-responsive { overflow-x:auto; -webkit-overflow-scrolling:touch; }
            .table { min-width:760px; }
            .stat-card, .chart-card, .table-card, .content-card { border-radius:10px; }
        }

        @media (max-width: 768px) {
            .sidebar {
                width:100%;
                height:72px;
                min-height:72px;
                top:auto;
                bottom:0;
                left:0;
                right:0;
                flex-direction:row;
                border-right:0;
                border-top:1px solid var(--sb-border);
                transition:none;
            }
            .sidebar:hover { width:100%; }
            .sb-brand, .sb-footer { display:none; }
            .sb-nav {
                flex:1;
                padding:8px;
                flex-direction:row;
                align-items:center;
                justify-content:space-around;
                gap:2px;
            }
            .sb-nav li { width:auto; flex:1; padding:0; }
            .sb-nav li a {
                height:56px;
                padding:0;
                justify-content:center;
                border-radius:12px;
            }
            .nav-icon-wrap { width:32px; height:32px; }
            .nav-text { display:none; }
            .main-content { margin-left:0; padding-bottom:72px; }
            .topbar {
                position:sticky;
                top:8px;
                margin:8px 10px 0;
                padding:10px 12px;
                border-radius:12px;
            }
            .topbar-date { display:none; }
            .page-body { padding:16px 10px 88px; }
            .toast-container { left:10px; right:10px; min-width:0; }
            .modal-dialog { margin:.5rem; }
        }

        @media (max-width: 576px) {
            .page-title { font-size:1.25rem; }
            .topbar-title { font-size:.9rem; }
            .btn, .form-control, .form-select { width:100%; }
            .d-flex.gap-2 { flex-wrap:wrap; }
        }
    </style>
</head>
<body>

{{-- ── SIDEBAR ── --}}
<aside class="sidebar">

    <div class="sb-brand">
        <a href="{{ url('/') }}" class="brand-link" aria-label="RenTara Home">
            <img src="{{ asset('images/rentara-logo-icon.png') }}" alt="" class="brand-icon">
            <div class="brand-wordmark">
                <div class="brand-name"><span class="brand-ren">Ren</span><span class="brand-tara">Tara</span></div>
                <div class="brand-sub">Car Rental</div>
            </div>
        </a>
    </div>

    <ul class="sb-nav">
        <li>
            <a href="{{ route('admin.dashboard') }}"
               class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <div class="nav-icon-wrap"><i class='bx bx-tachometer'></i></div>
                <span class="nav-text">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.tracking') }}"
               class="{{ request()->routeIs('admin.tracking') ? 'active' : '' }}">
                <div class="nav-icon-wrap"><i class='bx bx-map-alt'></i></div>
                <span class="nav-text">Tracking</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.car-inventory') }}"
               class="{{ request()->routeIs('admin.car-inventory') ? 'active' : '' }}">
                <div class="nav-icon-wrap"><i class='bx bx-car'></i></div>
                <span class="nav-text">Car Inventory</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.report') }}"
               class="{{ request()->routeIs('admin.report') ? 'active' : '' }}">
                <div class="nav-icon-wrap"><i class='bx bx-bar-chart-alt-2'></i></div>
                <span class="nav-text">Report</span>
            </a>
        </li>
        <li>
            <a href="{{ route('admin.manage-users') }}"
               class="{{ request()->routeIs('admin.manage-users') ? 'active' : '' }}">
                <div class="nav-icon-wrap"><i class='bx bx-group'></i></div>
                <span class="nav-text">Manage Users</span>
            </a>
        </li>
    </ul>

    <div class="sb-footer">
        <div class="sb-user">
            <div class="user-av">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                <i class='bx bx-log-out'></i>
                <span class="logout-text">Log Out</span>
            </button>
        </form>
    </div>
</aside>

{{-- ── MAIN CONTENT ── --}}
<div class="main-content">

    {{-- Glassmorphism floating topbar --}}
    <div class="topbar">
        <span class="topbar-title">@yield('title', 'Dashboard')</span>
        <span class="topbar-date">{{ now()->format('F d, Y') }}</span>
    </div>

    {{-- Toast Messages --}}
    @if(session('success'))
    <div class="toast-container">
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm border-0"
             style="border-radius:12px;font-weight:600;" role="alert">
            <i class='bx bx-check-circle text-success fs-5'></i>
            {{ session('success') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif
    @if(session('error'))
    <div class="toast-container">
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 shadow-sm border-0"
             style="border-radius:12px;font-weight:600;" role="alert">
            <i class='bx bx-error-circle text-danger fs-5'></i>
            {{ session('error') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

    <div class="page-body">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
