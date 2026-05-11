<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Admin Magang PUSDATIN</title>
    <meta name="description" content="Sistem Manajemen Magang PUSDATIN PUPR">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Google Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome 6 --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        /* ======================================
           DESIGN SYSTEM — PUSDATIN MAGANG
           Primary: #1e3a8a  Accent: #fbbf24
           Font: Inter 400/500/600
        ====================================== */

        :root {
            --primary: #1e3a8a;
            --primary-light: #2548a8;
            --primary-lighter: #dbeafe;
            --primary-dark: #172e6e;
            --accent: #fbbf24;
            --accent-light: #fcd34d;
            --accent-dark: #d97706;
            --bg-body: #f8fafc;
            --bg-card: #ffffff;
            --bg-sidebar: #0f1d3d;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --text-sidebar: #94a3b8;
            --text-sidebar-hover: #ffffff;
            --border: #e2e8f0;
            --radius: 10px;
            --radius-sm: 6px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
            --shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -2px rgba(0,0,0,0.05);
            --transition: 150ms ease;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-weight: 400;
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-primary);
            background: var(--bg-body);
            -webkit-font-smoothing: antialiased;
        }

        /* ── LAYOUT ── */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 260px;
            min-width: 260px;
            background: var(--bg-sidebar);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 24px 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .sidebar-brand .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--accent);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: var(--primary-dark);
            font-weight: 700;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-text {
            color: #fff;
            font-weight: 600;
            font-size: 15px;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }

        .sidebar-brand .brand-text small {
            display: block;
            font-weight: 400;
            font-size: 11px;
            color: var(--text-sidebar);
            letter-spacing: 0.02em;
        }

        .sidebar-section {
            padding: 20px 16px 4px;
        }

        .sidebar-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.28);
            padding: 0 12px;
            margin-bottom: 8px;
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--text-sidebar);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            transition: all var(--transition);
            position: relative;
        }

        .sidebar-nav li a i {
            width: 20px;
            text-align: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .sidebar-nav li a:hover {
            color: var(--text-sidebar-hover);
            background: rgba(255,255,255,0.06);
        }

        .sidebar-nav li a.active {
            color: #fff;
            background: var(--primary);
            font-weight: 600;
        }

        .sidebar-nav li a.active i {
            color: var(--accent);
        }

        .sidebar-nav .nav-badge {
            margin-left: auto;
            background: #ef4444;
            color: #fff;
            font-size: 10px;
            font-weight: 600;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 16px 24px 20px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .sidebar-footer .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-footer .user-avatar {
            width: 34px;
            height: 34px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 14px;
        }

        .sidebar-footer .user-name {
            color: #fff;
            font-size: 13px;
            font-weight: 500;
        }

        .sidebar-footer .user-role {
            color: var(--text-sidebar);
            font-size: 11px;
        }

        /* ── MAIN CONTENT ── */
        .main-content {
            flex: 1;
            margin-left: 260px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ── TOPBAR ── */
        .topbar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar .page-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.02em;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .topbar-actions .topbar-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all var(--transition);
            font-size: 15px;
        }

        .topbar-actions .topbar-btn:hover {
            background: var(--primary-lighter);
            color: var(--primary);
            border-color: var(--primary);
        }

        .topbar-actions .topbar-avatar {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: 600;
            margin-left: 4px;
        }

        /* ── CONTENT AREA ── */
        .content-area {
            padding: 28px 32px 40px;
            flex: 1;
        }

        /* ── ALERT / FLASH MESSAGES ── */
        .alert-toast {
            border: none;
            border-radius: var(--radius);
            padding: 14px 18px;
            font-size: 13.5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            animation: slideIn 0.3s ease;
        }

        .alert-toast.alert-success {
            background: #ecfdf5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .alert-toast.alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .alert-toast .btn-close {
            filter: none;
            opacity: 0.5;
            font-size: 10px;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ══════════════════════════════════
           REUSABLE COMPONENTS
        ══════════════════════════════════ */

        /* Cards */
        .card-clean {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .card-clean .card-header-clean {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-clean .card-header-clean h3 {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .card-clean .card-body-clean {
            padding: 22px;
        }

        /* Tables */
        .table-clean {
            width: 100%;
            border-collapse: collapse;
        }

        .table-clean thead th {
            font-size: 11.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--text-secondary);
            padding: 10px 14px;
            border-bottom: 1px solid var(--border);
            background: #f8fafc;
            white-space: nowrap;
            text-align: left;
        }

        .table-clean tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13.5px;
            vertical-align: middle;
        }

        .table-clean tbody tr {
            transition: background var(--transition);
        }

        .table-clean tbody tr:hover {
            background: #f8fafc;
        }

        .table-clean tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badges */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: 0.01em;
        }

        .badge-status.badge-aktif     { background: #ecfdf5; color: #065f46; }
        .badge-status.badge-belum     { background: #fffbeb; color: #92400e; }
        .badge-status.badge-selesai   { background: var(--primary-lighter); color: var(--primary); }
        .badge-status.badge-anulir    { background: #fef2f2; color: #991b1b; }
        .badge-status.badge-review    { background: #f5f3ff; color: #5b21b6; }
        .badge-status.badge-sent      { background: #ecfdf5; color: #065f46; }
        .badge-status.badge-pending   { background: #f1f5f9; color: #64748b; }

        /* Buttons */
        .btn-primary-custom {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary-custom:hover {
            background: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30,58,138,0.25);
        }

        .btn-accent-custom {
            background: var(--accent);
            color: var(--primary-dark);
            border: none;
            border-radius: var(--radius-sm);
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-accent-custom:hover {
            background: var(--accent-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(251,191,36,0.35);
        }

        .btn-outline-custom {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-outline-custom:hover {
            background: var(--primary-lighter);
            color: var(--primary);
            border-color: var(--primary);
        }

        .btn-sm-custom {
            padding: 5px 12px;
            font-size: 12px;
            border-radius: 6px;
        }

        .btn-success-custom {
            background: #10b981;
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            padding: 7px 14px;
            font-size: 12.5px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-success-custom:hover { background: #059669; }

        .btn-danger-custom {
            background: #ef4444;
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            padding: 7px 14px;
            font-size: 12.5px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-danger-custom:hover { background: #dc2626; }

        /* Tab Navigation */
        .tab-nav-clean {
            display: flex;
            gap: 4px;
            border-bottom: 1px solid var(--border);
            padding: 0 22px;
            background: #fff;
        }

        .tab-nav-clean .tab-item {
            padding: 12px 18px;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--text-secondary);
            border: none;
            background: none;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            white-space: nowrap;
        }

        .tab-nav-clean .tab-item:hover {
            color: var(--primary);
        }

        .tab-nav-clean .tab-item.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            font-weight: 600;
        }

        .tab-nav-clean .tab-count {
            background: var(--primary-lighter);
            color: var(--primary);
            font-size: 11px;
            font-weight: 600;
            min-width: 22px;
            height: 20px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
        }

        .tab-nav-clean .tab-item.active .tab-count {
            background: var(--primary);
            color: #fff;
        }

        /* KPI Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            transition: all var(--transition);
        }

        .kpi-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .kpi-card .kpi-info h4 {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .kpi-card .kpi-info .kpi-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .kpi-card .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            flex-shrink: 0;
        }

        /* Progress bar */
        .progress-clean {
            height: 6px;
            background: #f1f5f9;
            border-radius: 3px;
            overflow: hidden;
        }

        .progress-clean .progress-fill {
            height: 100%;
            border-radius: 3px;
            transition: width 0.6s ease;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 48px 20px;
        }

        .empty-state i {
            font-size: 40px;
            color: var(--text-muted);
            margin-bottom: 14px;
        }

        .empty-state p {
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Link text */
        .link-name {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color var(--transition);
        }

        .link-name:hover {
            color: var(--primary-light);
            text-decoration: underline;
        }

        /* Responsive for smaller screens */
        @media (max-width: 1024px) {
            .sidebar { width: 220px; min-width: 220px; }
            .main-content { margin-left: 220px; }
            .content-area { padding: 20px; }
        }

        /* Custom styling for native file input buttons */
        input[type="file"]::file-selector-button {
            background: #fff;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all var(--transition);
            margin-right: 12px;
        }

        input[type="file"]::file-selector-button:hover {
            background: var(--primary-lighter);
            color: var(--primary);
            border-color: var(--primary);
        }

        .hidden {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="admin-wrapper">
    {{-- ── SIDEBAR ── --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <img src="/images/logo-pu.png" alt="Logo PUPR" style="width: 38px; height: 38px; flex-shrink: 0; object-fit: cover;">
            <div class="brand-text">
                PUSDATIN
                <small>Manajemen Magang</small>
            </div>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-label">Menu Utama</div>
            <ul class="sidebar-nav">
                <li>
                    <a href="{{ route('admin.dashboard.index') }}" class="{{ request()->is('admin/dashboard*') ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.lamaran.index') }}" class="{{ request()->is('admin/lamaran*') ? 'active' : '' }}">
                        <i class="fas fa-inbox"></i>
                        <span>Lamaran Masuk</span>
                        @php $lamaranCount = \App\Models\PesertaMagang::where('status_magang', 'Menunggu Review')->count(); @endphp
                        @if($lamaranCount > 0)
                            <span class="nav-badge">{{ $lamaranCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-label">Kelola</div>
            <ul class="sidebar-nav">
                <li>
                    <a href="{{ route('admin.manajemen.index') }}" class="{{ request()->is('admin/manajemen*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Manajemen Magang</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.dokumen.index') }}" class="{{ request()->is('admin/dokumen*') ? 'active' : '' }}">
                        <i class="fas fa-file-lines"></i>
                        <span>Pusat Dokumen</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.arsip-dokumen.index') }}" class="{{ request()->is('admin/arsip-dokumen*') ? 'active' : '' }}">
                        <i class="fas fa-archive"></i>
                        <span>Arsip Dokumen</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-label">Pengaturan</div>
            <ul class="sidebar-nav">
                <li>
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->is('admin/users*') ? 'active' : '' }}">
                        <i class="fas fa-user-shield"></i>
                        <span>Manajemen Admin</span>
                    </a>
                </li>
            </ul>
        </div>

    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <div class="main-content">
        {{-- Topbar --}}
        <header class="topbar">
            <h1 class="page-title">@yield('title')</h1>
            <div class="topbar-actions">
                {{-- Notification Dropdown --}}
                <div style="position: relative;" id="notifDropdownContainer">
                    <button id="notifDropdownBtn" class="topbar-btn" style="position: relative; border: none; cursor: pointer;" title="Notifikasi Aktivitas">
                        <i class="fas fa-bell"></i>
                        @if(isset($globalNotifications) && $globalNotifications->count() > 0)
                            <span style="position: absolute; top: 4px; right: 4px; width: 8px; height: 8px; background-color: #ef4444; border-radius: 50%; border: 2px solid #fff;"></span>
                        @endif
                    </button>
                    
                    {{-- Dropdown Menu Notifikasi --}}
                    <div id="notifDropdownMenu" class="hidden" style="position: absolute; right: 0; top: 100%; margin-top: 8px; background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm); box-shadow: var(--shadow-md); width: 320px; z-index: 100; overflow: hidden; animation: slideIn 0.2s ease;">
                        <div style="padding: 12px 16px; border-bottom: 1px solid var(--border); background: #f8fafc; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 13px; font-weight: 600; color: var(--text-primary);">Aktivitas Terbaru</span>
                        </div>
                        <ul style="list-style: none; padding: 0; margin: 0; max-height: 350px; overflow-y: auto;">
                            @if(isset($globalNotifications) && $globalNotifications->count() > 0)
                                @foreach($globalNotifications as $notif)
                                    <li style="padding: 12px 16px; border-bottom: 1px solid var(--border); transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='none'">
                                        <div style="display: flex; gap: 12px; align-items: flex-start;">
                                            @php
                                                // Tentukan ikon berdasarkan tipe_aksi
                                                $icon = 'fa-info-circle';
                                                $color = 'var(--text-muted)';
                                                if ($notif->tipe_aksi === 'terima_lamaran') { $icon = 'fa-check-circle'; $color = '#10b981'; }
                                                if ($notif->tipe_aksi === 'tolak_lamaran' || $notif->tipe_aksi === 'anulir') { $icon = 'fa-times-circle'; $color = '#ef4444'; }
                                                if ($notif->tipe_aksi === 'kirim_dokumen') { $icon = 'fa-envelope-open-text'; $color = '#3b82f6'; }
                                                if ($notif->tipe_aksi === 'manajemen_admin') { $icon = 'fa-user-shield'; $color = '#8b5cf6'; }
                                            @endphp
                                            <div style="margin-top: 2px;">
                                                <i class="fas {{ $icon }}" style="color: {{ $color }}; font-size: 14px;"></i>
                                            </div>
                                            <div style="flex: 1;">
                                                <p style="margin: 0; font-size: 12px; color: var(--text-primary); line-height: 1.4;">
                                                    <b>{{ $notif->user->name ?? 'Admin' }}</b> {!! $notif->deskripsi !!}
                                                </p>
                                                <span style="font-size: 10px; color: var(--text-muted); margin-top: 4px; display: block;">
                                                    {{ $notif->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                <li style="padding: 24px 16px; text-align: center; color: var(--text-muted); font-size: 12px;">
                                    Belum ada aktivitas.
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Profile Dropdown --}}
                <div style="position: relative;" id="profileDropdownContainer">
                    <button id="profileDropdownBtn" class="topbar-avatar" style="border: none; cursor: pointer;" title="Menu Profil">
                        {{ Auth::check() ? strtoupper(substr(Auth::user()->name, 0, 1)) : 'A' }}
                    </button>
                    
                    {{-- Dropdown Menu --}}
                    <div id="profileDropdownMenu" class="hidden" style="position: absolute; right: 0; top: 100%; margin-top: 8px; background: #fff; border: 1px solid var(--border); border-radius: var(--radius-sm); box-shadow: var(--shadow-md); width: 200px; z-index: 100; overflow: hidden; animation: slideIn 0.2s ease;">
                        <div style="padding: 12px 16px; border-bottom: 1px solid var(--border); background: #f8fafc;">
                            <div style="font-size: 13px; font-weight: 600; color: var(--text-primary);">{{ Auth::check() ? Auth::user()->name : 'Admin' }}</div>
                            <div style="font-size: 11px; color: var(--text-secondary); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ Auth::check() ? Auth::user()->email : 'admin@pusdatin.go.id' }}</div>
                        </div>
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            <li>
                                <a href="{{ route('admin.profile.index') }}" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; color: var(--text-secondary); text-decoration: none; font-size: 13px; transition: all 0.2s;" onmouseover="this.style.background='#f1f5f9'; this.style.color='var(--primary)'" onmouseout="this.style.background='none'; this.style.color='var(--text-secondary)'">
                                    <i class="fas fa-user-circle"></i> Profil Saya
                                </a>
                            </li>
                            <li style="border-top: 1px solid var(--border);">
                                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="display: flex; align-items: center; gap: 8px; padding: 10px 16px; color: #dc2626; text-decoration: none; font-size: 13px; transition: all 0.2s; background: none; border: none; width: 100%; text-align: left; cursor: pointer; font-family: inherit;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='none'">
                                        <i class="fas fa-sign-out-alt"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="content-area">
            @if(session('success'))
                <div class="alert-toast alert-success" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" style="margin-left:auto; background:none; border:none; cursor:pointer; opacity:0.6;" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert-toast alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" style="margin-left:auto; background:none; border:none; cursor:pointer; opacity:0.6;" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    // Profile & Notif Dropdown Logic
    document.addEventListener('DOMContentLoaded', function() {
        const profileBtn = document.getElementById('profileDropdownBtn');
        const profileMenu = document.getElementById('profileDropdownMenu');
        const notifBtn = document.getElementById('notifDropdownBtn');
        const notifMenu = document.getElementById('notifDropdownMenu');
        
        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if(notifMenu) notifMenu.classList.add('hidden'); // Close other
                profileMenu.classList.toggle('hidden');
            });
        }

        if (notifBtn && notifMenu) {
            notifBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if(profileMenu) profileMenu.classList.add('hidden'); // Close other
                notifMenu.classList.toggle('hidden');
            });
        }
            
        document.addEventListener('click', function(e) {
            if (profileBtn && !profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.add('hidden');
            }
            if (notifBtn && !notifBtn.contains(e.target) && !notifMenu.contains(e.target)) {
                notifMenu.classList.add('hidden');
            }
        });
    });
</script>

@stack('scripts')
</body>
</html>
