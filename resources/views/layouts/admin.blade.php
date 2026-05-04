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
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <div class="user-name">Admin</div>
                    <div class="user-role">Kepegawaian</div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ── --}}
    <div class="main-content">
        {{-- Topbar --}}
        <header class="topbar">
            <h1 class="page-title">@yield('title')</h1>
            <div class="topbar-actions">
                <button class="topbar-btn" title="Notifikasi">
                    <i class="fas fa-bell"></i>
                </button>
                <button class="topbar-btn" title="Pengaturan">
                    <i class="fas fa-gear"></i>
                </button>
                <div class="topbar-avatar">A</div>
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
@stack('scripts')
</body>
</html>
