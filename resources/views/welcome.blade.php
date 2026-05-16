<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Magang — PUSDATIN Kementerian PU</title>
    <meta name="description"
        content="Portal resmi pendaftaran magang PUSDATIN Kementerian Pekerjaan Umum. Cek ketersediaan kuota dan daftar magang sekarang.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #2548a8;
            --primary-lighter: #dbeafe;
            --primary-dark: #0f1d3d;
            --accent: #fbbf24;
            --accent-dark: #d97706;
            --bg: #f8fafc;
            --border: #e2e8f0;
            --text: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --radius: 12px;
            --shadow: 0 4px 16px rgba(0, 0, 0, .08);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-dark);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden
        }

        /* ── NAVBAR ── */
        .navbar {
            background: var(--primary-dark);
            padding: 0 32px;
            height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            transition: all .3s
        }

        .navbar.scrolled {
            background: rgba(15, 29, 61, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, .2)
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none
        }

        .nav-brand img {
            height: 40px
        }

        .nav-brand-text {
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: 0.5px
        }

        .nav-brand-sub {
            font-size: 11px;
            font-weight: 400;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 1px
        }

        .nav-actions {
            display: flex;
            gap: 12px;
            align-items: center
        }

        .btn-nav {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Inter', sans-serif
        }

        .btn-nav-accent {
            background: var(--accent);
            color: var(--primary-dark);
            position: relative;
            overflow: hidden;
        }
        .btn-nav-accent::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,.3) 0%, transparent 60%);
            opacity: 0;
            transition: opacity .3s;
            pointer-events: none;
        }

        .btn-nav-accent:hover {
            background: #f59e0b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(251, 191, 36, .3)
        }
        .btn-nav-accent:hover::after {
            opacity: 1;
        }
        .btn-nav-accent:hover .fa-rocket {
            animation: blastOff 0.5s ease-in-out forwards;
        }

        /* ── HERO PARALLAX ── */
        .hero-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 1;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") center center, linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
            padding: 0 24px
        }

        .hero-content {
            max-width: 1100px;
            margin-top: 40px
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(251, 191, 36, .1);
            border: 1px solid rgba(251, 191, 36, .3);
            color: var(--accent);
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-bottom: 24px
        }

        .hero-content h1 {
            font-size: clamp(28px, 4.5vw, 56px);
            font-weight: 900;
            margin-bottom: 20px;
            letter-spacing: -.02em;
            line-height: 1.2;
            text-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }

        .hero-content h1 span {
            background: linear-gradient(135deg, #fde68a 0%, #f59e0b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 2px 8px rgba(245,158,11,0.4));
        }

        .hero-content p {
            font-size: clamp(16px, 2vw, 18px);
            color: rgba(255, 255, 255, .8);
            max-width: 640px;
            margin: 0 auto 40px;
            line-height: 1.7
        }

        .hero-stats {
            display: flex;
            gap: 48px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
            background: rgba(255, 255, 255, .03);
            padding: 24px 48px;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, .05);
            backdrop-filter: blur(10px)
        }

        .hero-stat {
            text-align: center
        }

        .hero-stat-num {
            font-size: 36px;
            font-weight: 800;
            color: #fff;
            line-height: 1
        }

        .hero-stat-label {
            font-size: 13px;
            color: var(--accent);
            margin-top: 8px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase
        }

        .scroll-indicator {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: rgba(255, 255, 255, .6);
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: bounce 2s infinite
        }

        .scroll-indicator i {
            font-size: 20px
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translate(-50%, 0)
            }

            40% {
                transform: translate(-50%, -10px)
            }

            60% {
                transform: translate(-50%, -5px)
            }
        }

        /* ── MAIN CONTENT (OVERLAY) ── */
        .main-wrapper {
            position: relative;
            z-index: 2;
            margin-top: 100vh;
            background: var(--bg);
            border-radius: 40px 40px 0 0;
            box-shadow: 0 -20px 40px rgba(0, 0, 0, .15);
            min-height: 100vh
        }

        .main-content {
            padding: 80px 32px;
            max-width: 1200px;
            margin: 0 auto
        }

        .section-header {
            text-align: center;
            margin-bottom: 56px
        }

        .section-title {
            font-size: 32px;
            font-weight: 800;
            color: var(--primary-dark);
            margin-bottom: 12px
        }

        .section-sub {
            font-size: 16px;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto
        }

        /* ── TRIWULAN FILTER ── */
        .triwulan-filter {
            display: flex;
            justify-content: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 48px
        }

        .tw-btn {
            padding: 12px 24px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #fff;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .02)
        }

        .tw-btn:hover {
            border-color: var(--primary-light);
            color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, .06)
        }

        .tw-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 8px 20px rgba(30, 58, 138, .2)
        }

        .tw-btn i {
            margin-right: 8px;
            opacity: 0.7
        }

        .tw-btn .tw-sub {
            display: block;
            font-size: 11px;
            font-weight: 500;
            opacity: 0.7;
            margin-top: 4px
        }

        .info-banner {
            background: #eff6ff;
            border-left: 4px solid var(--primary);
            border-radius: 8px;
            padding: 16px 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 40px;
            font-size: 14px;
            color: var(--primary-dark);
            box-shadow: 0 2px 10px rgba(0, 0, 0, .03)
        }

        .info-banner i {
            font-size: 20px;
            color: var(--primary);
            margin-top: 2px
        }

        /* ── BIDANG & TIM CARDS ── */
        .bidang-section {
            margin-bottom: 64px
        }

        .bidang-header-bar {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px
        }

        .bidang-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, .1)
        }

        .bidang-header-text h3 {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary-dark)
        }

        .bidang-header-text p {
            font-size: 14px;
            color: var(--text-secondary)
        }

        .tim-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 24px
        }

        .tim-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 24px;
            cursor: pointer;
            transition: all .3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%
        }

        .tim-card:hover {
            border-color: var(--primary-light);
            box-shadow: 0 12px 24px rgba(30, 58, 138, .08);
            transform: translateY(-4px)
        }

        .tim-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--border);
            transition: all .3s ease
        }

        .tim-card:hover::before {
            background: var(--primary)
        }

        .tim-name-wrap {
            margin-bottom: 20px;
            flex-grow: 1
        }

        .tim-name {
            font-size: 16px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.4;
            margin-bottom: 8px;
            transition: color .2s
        }

        .tim-card:hover .tim-name {
            color: var(--primary)
        }

        .tim-preview-text {
            font-size: 13px;
            color: var(--text-secondary);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5
        }

        .tim-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px dashed var(--border);
            margin-top: auto
        }

        .kuota-indicator {
            display: flex;
            align-items: center;
            gap: 12px
        }

        .kuota-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: 800;
            background: #f1f5f9;
            color: var(--text)
        }

        .kuota-text {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px
        }

        .status-ok .kuota-circle {
            background: #dcfce7;
            color: #166534
        }

        .status-warn .kuota-circle {
            background: #fef3c7;
            color: #92400e
        }

        .status-full .kuota-circle {
            background: #fee2e2;
            color: #991b1b
        }

        .btn-detail {
            background: transparent;
            border: none;
            color: var(--primary);
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            transition: gap .2s
        }

        .tim-card:hover .btn-detail {
            gap: 8px
        }

        /* ── MODAL DETAIL TIM ── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .6);
            backdrop-filter: blur(4px);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            opacity: 0;
            transition: opacity .3s
        }

        .modal-overlay.show {
            display: flex;
            opacity: 1
        }

        .modal-content {
            background: #fff;
            border-radius: 20px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 24px 48px rgba(0, 0, 0, .2);
            transform: scale(0.95) translateY(20px);
            transition: all .3s;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            max-height: 90vh
        }

        .modal-overlay.show .modal-content {
            transform: scale(1) translateY(0)
        }

        .modal-header {
            padding: 24px 32px;
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: flex-start
        }

        .modal-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--primary-dark);
            line-height: 1.3;
            padding-right: 20px
        }

        .btn-close {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 24px;
            cursor: pointer;
            transition: color .2s;
            line-height: 1
        }

        .btn-close:hover {
            color: var(--text)
        }

        .modal-body {
            padding: 32px;
            overflow-y: auto
        }

        .detail-section {
            margin-bottom: 24px
        }

        .detail-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px
        }

        .detail-text {
            font-size: 15px;
            color: var(--text);
            line-height: 1.6
        }

        .prodi-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px
        }

        .prodi-tag {
            background: rgba(30, 58, 138, .05);
            color: var(--primary);
            border: 1px solid rgba(30, 58, 138, .1);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px
        }

        .modal-footer {
            padding: 24px 32px;
            border-top: 1px solid var(--border);
            background: #fff;
            display: flex;
            justify-content: flex-end
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all .2s;
            text-decoration: none
        }

        .btn-primary:hover {
            background: var(--primary-light);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 58, 138, .2)
        }

        /* ── REVEAL ANIMATIONS ── */
        @keyframes blastOff {
            0% { transform: translate(0, 0) }
            50% { transform: translate(20px, -20px) scale(0.5); opacity: 0 }
            51% { transform: translate(-20px, 20px) scale(0.5); opacity: 0 }
            100% { transform: translate(0, 0) scale(1); opacity: 1 }
        }
        .reveal-up {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
        }
        .reveal-up.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── CTA SECTION ── */
        .bottom-section{min-height:100vh;display:flex;flex-direction:column}
        .cta-section{background:linear-gradient(135deg,var(--primary-dark) 0%,var(--primary) 100%);padding:80px 32px;text-align:center;color:#fff;flex-grow:1;display:flex;align-items:center;justify-content:center}
        .cta-inner{max-width:700px;margin:0 auto}

        .cta-section h2 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 16px
        }

        .cta-section p {
            color: rgba(255, 255, 255, .8);
            margin-bottom: 32px;
            font-size: 18px;
            line-height: 1.6
        }

        /* ── FOOTER ── */
        footer{background:#0a1226;color:rgba(255,255,255,.6);padding:40px 32px;text-align:center;font-size:14px}

        footer a {
            color: var(--accent);
            text-decoration: none;
            transition: color .2s
        }

        footer a:hover {
            color: #fff
        }

        .footer-links {
            display: flex;
            gap: 32px;
            justify-content: center;
            margin-bottom: 20px;
            flex-wrap: wrap
        }

        .footer-links a {
            display: inline-flex;
            align-items: center;
            gap: 8px
        }

        @media (max-width:768px) {
            .hero-stats {
                gap: 24px;
                padding: 20px
            }

            .main-content {
                padding: 56px 20px
            }

            .tim-grid {
                grid-template-columns: 1fr
            }
        }
    </style>
</head>

<body>

    {{-- NAVBAR --}}
    <nav class="navbar" id="navbar">
        <a href="{{ route('landing') }}" class="nav-brand">
            @if(file_exists(public_path('logo_pu.png')))
                <img src="{{ asset('logo_pu.png') }}" alt="Logo PUPR">
            @endif
            <div>
                <div class="nav-brand-text">PUSDATIN</div>
                <div class="nav-brand-sub">Kementerian Pekerjaan Umum</div>
            </div>
        </a>
        <div class="nav-actions">
            <a href="{{ route('pendaftaran.create') }}" class="btn-nav btn-nav-accent">
                <i class="fas fa-paper-plane"></i> Daftar Sekarang
            </a>
        </div>
    </nav>

    {{-- HERO PARALLAX --}}
    <div class="hero-wrapper">
        <div class="hero-content">
            <div class="hero-badge"><i class="fas fa-star"></i> Program Magang Resmi</div>
            <h1>Portal Pendaftaran <span>Magang PUSDATIN</span></h1>
            <p>Bergabunglah bersama kami di Pusat Data dan Teknologi Informasi Kementerian Pekerjaan Umum. Temukan tim
                kerja yang sesuai dengan minat dan jurusanmu.</p>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-num">{{ $pesertaAktifCount }}</div>
                    <div class="hero-stat-label">Peserta Aktif</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-num">{{ $totalAlumni }}</div>
                    <div class="hero-stat-label">Total Alumni</div>
                </div>
                @php
                    $totalKuota = $allTeams->sum('kuota_maksimal');
                @endphp
                <div class="hero-stat">
                    <div class="hero-stat-num">{{ $totalKuota }}</div>
                    <div class="hero-stat-label">Total Kuota</div>
                </div>
            </div>
        </div>

        <div class="scroll-indicator">
            <span>Scroll untuk eksplorasi</span>
            <i class="fas fa-chevron-down"></i>
        </div>
    </div>

    {{-- MAIN CONTENT OVERLAY --}}
    <div class="main-wrapper" id="main-content">
        <div class="main-content">

            <div class="section-header">
                <h2 class="section-title">Eksplorasi Tim Kerja</h2>
                <p class="section-sub">Pilih periode magangmu dan temukan tim kerja yang masih memiliki kuota tersedia.
                    Klik pada kartu tim untuk melihat detail pekerjaan dan persyaratan jurusan.</p>
            </div>

            {{-- TRIWULAN FILTER --}}
            <div class="triwulan-filter">
                @foreach($triwulans as $i => $tw)
                    <button class="tw-btn {{ $i === 0 ? 'active' : '' }}" data-start="{{ $tw['start_date'] }}"
                        data-end="{{ $tw['end_date'] }}" data-index="{{ $i }}" onclick="setTriwulan(this)">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $tw['label'] }} {{ $tw['year'] }}
                        <span class="tw-sub">{{ $tw['bulan'] }}</span>
                    </button>
                @endforeach
            </div>

            <div class="info-banner">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Informasi Kuota:</strong> Kuota dihitung secara <em>real-time</em> berdasarkan peserta yang
                    jadwal magangnya tumpang tindih dengan periode yang kamu pilih.
                </div>
            </div>

            {{-- BIDANG & TIM CARDS --}}
            @php
                $bidangOrder = [
                    'Bidang Manajemen Teknologi Informasi',
                    'Bidang Data Analitik Pekerjaan Umum',
                    'Bidang Pengelolaan Data Bencana Infrastruktur',
                    'Subbagian Tata Usaha',
                ];

                // Tim kerja data dari MD file — key sesuai nama di DB (dengan prefix "Tim Kerja ")
                $timInfo = [
                    'Tim Kerja Tata Kelola dan Perizinan' => ['jobdesk' => 'Koordinasi dan supervisi pelaksanaan tata kelola TI dan perizinan Kementerian PU', 'prodi' => 'Informatika, Sistem Informasi, Manajemen TI'],
                    'Tim Kerja Keamanan Teknologi Informasi' => ['jobdesk' => 'Koordinasi kegiatan keamanan TI, update & monitoring uji kerentanan, penanganan insiden siber', 'prodi' => 'Cybersecurity, Informatika, Sistem Komputer'],
                    'Tim Kerja Infrastruktur Teknologi Informasi' => ['jobdesk' => 'Pengelolaan infrastruktur jaringan TI, supervisi dan koordinasi pemeliharaan jaringan', 'prodi' => 'Teknik Elektro, Jaringan Komputer'],
                    'Tim Kerja Sistem Informasi' => ['jobdesk' => 'Pengembangan, standarisasi, koordinasi dan supervisi sistem informasi PU', 'prodi' => 'Informatika, Sistem Informasi'],
                    'Tim Kerja Korespondensi dan Kolaborasi' => ['jobdesk' => 'Pengelolaan aplikasi korespondensi, kolaborasi, verifikasi permohonan sertifikat elektronik', 'prodi' => 'Informatika, Administrasi, Manajemen'],
                    'Tim Kerja Sistem Kendali Otomatis dan Kecerdasan Buatan' => ['jobdesk' => 'Koordinasi dan supervisi kegiatan teknologi IoT, pembuatan SOP, laporan kegiatan sistem kendali otomatis', 'prodi' => 'Mekatronika, Informatika, AI & Robotics'],
                    'Tim Kerja Layanan dan Integrasi Data Infrastruktur' => ['jobdesk' => 'Integrasi dan layanan data PU, pemantauan sistem informasi', 'prodi' => 'Statistika, Informatika, Geodesi'],
                    'Tim Kerja Sistem Informasi Geografis dan Portal GIS PU' => ['jobdesk' => 'Pengelolaan dan pengembangan WebGIS & Portal GIS PU, update data spasial', 'prodi' => 'Geodesi, Geografi, Informatika'],
                    'Tim Kerja Data dan Informasi Statistik Infrastruktur Pekerjaan Umum' => ['jobdesk' => 'Analisis dan pemutakhiran data statistik PU, penulisan buku informasi', 'prodi' => 'Statistika, Matematika Terapan'],
                    'Tim Kerja Digitalisasi Infrastruktur Pekerjaan Umum' => ['jobdesk' => 'Koordinasi digitalisasi infrastruktur PU, BIM, pengembangan platform digital', 'prodi' => 'Informatika, Teknik Sipil, Arsitektur'],
                    'Tim Kerja Analisis Data dan Informasi Geospasial Infrastruktur dan Kebencanaan' => ['jobdesk' => 'Koordinasi analisis data geospasial & informasi kebencanaan', 'prodi' => 'Geodesi, Statistika, Informatika'],
                    'Tim Kerja Pelaporan Kebencanaan' => ['jobdesk' => 'Monitoring dan rekapitulasi penanganan bencana, laporan mingguan & bulanan', 'prodi' => 'Administrasi Publik, Statistika, Informatika'],
                    'Tim Kerja Manajemen Data dan Bencana' => ['jobdesk' => 'Koordinasi sistem & manajemen data & informasi bencana', 'prodi' => 'Informatika, Manajemen Risiko, Teknik Sipil'],
                    'Tim Kerja Kepegawaian dan Jabatan Fungsional' => ['jobdesk' => 'Supervisi dan koordinasi kegiatan kepegawaian & jabatan fungsional', 'prodi' => 'Administrasi Publik, Manajemen'],
                    'Tim Kerja Keuangan' => ['jobdesk' => 'Koordinasi dan supervisi pengelolaan keuangan PU', 'prodi' => 'Akuntansi, Keuangan'],
                    'Tim Kerja Pengelolaan BMN dan Arsip' => ['jobdesk' => 'Supervisi kegiatan BMN & arsip', 'prodi' => 'Administrasi, Arsiparis'],
                    'Tim Kerja Monitoring dan Evaluasi' => ['jobdesk' => 'Koordinasi & supervisi kegiatan monitoring dan evaluasi', 'prodi' => 'Manajemen, Statistik'],
                    'Tim Kerja Sarana dan Prasarana Perkantoran' => ['jobdesk' => 'Pemenuhan dan pengelolaan sarana & prasarana perkantoran', 'prodi' => 'Teknik Sipil, Manajemen'],
                ];
            @endphp

            <div id="tim-kerja-container">
                @foreach($bidangOrder as $namaBidang)
                    @if(isset($timKerjaBidang[$namaBidang]) && $timKerjaBidang[$namaBidang]->count() > 0)
                        @php
                            $info = $bidangInfo[$namaBidang] ?? ['icon' => 'fa-building', 'color' => '#64748b', 'description' => ''];
                            $teams = $timKerjaBidang[$namaBidang];
                        @endphp
                        <div class="bidang-section">
                            <div class="bidang-header-bar">
                                <div class="bidang-icon-wrap" style="background:{{ $info['color'] }}">
                                    <i class="fas {{ $info['icon'] }}"></i>
                                </div>
                                <div class="bidang-header-text">
                                    <h3>{{ $namaBidang }}</h3>
                                    <p>{{ $info['description'] }}</p>
                                </div>
                            </div>

                            <div class="tim-grid">
                                @foreach($teams as $tim)
                                    @php
                                        $info2 = $timInfo[$tim->nama_tim] ?? ['jobdesk' => '-', 'prodi' => '-'];
                                        // Escaping for JS injection
                                        $safeJobdesk = htmlspecialchars($info2['jobdesk'], ENT_QUOTES);
                                        $safeProdi = htmlspecialchars($info2['prodi'], ENT_QUOTES);
                                        $safeNamaTim = htmlspecialchars($tim->nama_tim, ENT_QUOTES);
                                    @endphp
                                    <div class="tim-card"
                                        onclick="openTimModal('{{ $safeNamaTim }}', '{{ $safeJobdesk }}', '{{ $safeProdi }}', {{ $tim->id }}, {{ $tim->kuota_maksimal }})">
                                        <div class="tim-name-wrap">
                                            <div class="tim-name">{{ $tim->nama_tim }}</div>
                                            <div class="tim-preview-text">{{ $info2['jobdesk'] }}</div>
                                        </div>

                                        <div class="tim-card-footer">
                                            <div class="kuota-indicator" id="ind-{{ $tim->id }}" data-tim-id="{{ $tim->id }}"
                                                data-kuota="{{ $tim->kuota_maksimal }}">
                                                <div class="kuota-circle" id="circle-{{ $tim->id }}">{{ $tim->kuota_maksimal }}
                                                </div>
                                                <div class="kuota-text">Sisa<br>Kuota</div>
                                            </div>
                                            <button class="btn-detail">Lihat Detail <i class="fas fa-arrow-right"></i></button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

        </div>

        <div class="bottom-section">
            {{-- CTA --}}
            <section class="cta-section">
                <div class="cta-inner">
                    <h2 class="reveal-up">Wujudkan Potensimu Bersama Kami</h2>
                    <p class="reveal-up" style="transition-delay: 0.1s">Jadilah bagian dari inovasi teknologi infrastruktur Indonesia. Daftarkan dirimu sekarang dan dapatkan
                        pengalaman berharga di PUSDATIN Kementerian PUPR.</p>
                    <a href="{{ route('pendaftaran.create') }}" class="btn-nav btn-nav-accent reveal-up"
                        style="font-size:16px;padding:16px 32px; transition-delay: 0.2s">
                        <i class="fas fa-rocket"></i> Menuju Form Pendaftaran
                    </a>
                </div>
            </section>

            {{-- FOOTER --}}
            <footer>
                <div class="footer-links">
                    <a href="https://pusdatin.pu.go.id" target="_blank" class="reveal-up" style="transition-delay: 0.3s"><i class="fas fa-globe"></i> pusdatin.pu.go.id</a>
                    <a href="mailto:info@pusdatin.pu.go.id" class="reveal-up" style="transition-delay: 0.4s"><i class="fas fa-envelope"></i> info@pusdatin.pu.go.id</a>
                    <a href="{{ route('login') }}" class="reveal-up" style="transition-delay: 0.5s"><i class="fas fa-sign-in-alt"></i> Login Admin</a>
                </div>
                <div class="reveal-up" style="transition-delay: 0.6s">© {{ date('Y') }} PUSDATIN Kementerian Pekerjaan Umum · Sistem Manajemen Magang</div>
            </footer>
        </div>
    </div>

    {{-- MODAL DETAIL TIM --}}
    <div class="modal-overlay" id="timModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modalTimName">Nama Tim</div>
                <button class="btn-close" onclick="closeTimModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="detail-section">
                    <div class="detail-label"><i class="fas fa-tasks"></i> Deskripsi Pekerjaan (Jobdesk)</div>
                    <div class="detail-text" id="modalTimJobdesk">Deskripsi jobdesk...</div>
                </div>

                <div class="detail-section">
                    <div class="detail-label"><i class="fas fa-graduation-cap"></i> Rekomendasi Program Studi</div>
                    <div class="prodi-tags" id="modalTimProdi">
                        <!-- Prodi tags will be injected here -->
                    </div>
                </div>

                <div class="info-banner" style="margin-bottom:0;padding:12px 16px">
                    <i class="fas fa-chart-pie" style="font-size:16px"></i>
                    <div style="font-size:13px">
                        Sisa Kuota untuk tim ini pada periode yang dipilih adalah <strong id="modalTimKuota"
                            style="font-size:15px">0</strong> dari <span id="modalTimMax">0</span> slot.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('pendaftaran.create') }}" class="btn-primary">
                    Pilih Tim Ini di Pendaftaran <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <script>
        // ── PARALLAX SCROLL EFFECT ──
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY;
            const heroWrapper = document.querySelector('.hero-wrapper');
            const heroContent = document.querySelector('.hero-content');
            const navbar = document.getElementById('navbar');

            // Fade and parallax hero
            if (scrolled < window.innerHeight) {
                const opacity = 1 - (scrolled / window.innerHeight) * 1.5;
                heroContent.style.opacity = Math.max(0, opacity);
                heroContent.style.transform = `translateY(${scrolled * 0.4}px)`;
            }

            // Navbar glass effect
            if (scrolled > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // ── KUOTA LOGIC ──
        const activePeserta = @json($activePeserta);
        const triwulans = @json($triwulans);
        let currentTriwulanIndex = 0; // Menyimpan index triwulan aktif

        function hitungSisaKuota(timId, kuotaMaks, tMulai, tSelesai) {
            const terisi = activePeserta.filter(p => {
                if (parseInt(p.id_tim_kerja_1) !== parseInt(timId)) return false;
                const pMulai = p.tanggal_mulai;
                const pSelesai = p.tanggal_selesai;
                return pMulai <= tSelesai && pSelesai >= tMulai;
            }).length;
            return Math.max(0, kuotaMaks - terisi);
        }

        function renderKuota(tMulai, tSelesai) {
            document.querySelectorAll('.kuota-indicator').forEach(el => {
                const timId = el.dataset.timId;
                const kuotaMaks = parseInt(el.dataset.kuota);
                const sisa = hitungSisaKuota(timId, kuotaMaks, tMulai, tSelesai);

                const circleEl = document.getElementById('circle-' + timId);
                circleEl.textContent = sisa;

                // Update color class
                el.className = 'kuota-indicator ' + (sisa === 0 ? 'status-full' : sisa <= 1 ? 'status-warn' : 'status-ok');
            });
        }

        function setTriwulan(btn) {
            document.querySelectorAll('.tw-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentTriwulanIndex = parseInt(btn.dataset.index);
            renderKuota(btn.dataset.start, btn.dataset.end);
        }

        // ── MODAL LOGIC ──
        const modal = document.getElementById('timModal');

        function openTimModal(nama, jobdesk, prodiStr, timId, maxKuota) {
            document.getElementById('modalTimName').textContent = nama;
            document.getElementById('modalTimJobdesk').textContent = jobdesk;

            // Render prodi tags
            const prodis = prodiStr.split(',').map(p => p.trim()).filter(p => p);
            const prodiContainer = document.getElementById('modalTimProdi');
            prodiContainer.innerHTML = '';
            prodis.forEach(p => {
                const span = document.createElement('span');
                span.className = 'prodi-tag';
                span.innerHTML = `<i class="fas fa-check"></i> ${p}`;
                prodiContainer.appendChild(span);
            });

            // Render kuota untuk modal
            const twBtn = document.querySelector('.tw-btn.active');
            const sisa = hitungSisaKuota(timId, maxKuota, twBtn.dataset.start, twBtn.dataset.end);

            document.getElementById('modalTimKuota').textContent = sisa;
            document.getElementById('modalTimKuota').style.color = sisa === 0 ? '#dc2626' : (sisa <= 1 ? '#d97706' : '#16a34a');
            document.getElementById('modalTimMax').textContent = maxKuota;

            modal.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeTimModal() {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }

        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeTimModal();
        });

        // ── INIT ──
        document.addEventListener('DOMContentLoaded', () => {
            // ── INTERSECTION OBSERVER FOR REVEAL ANIMATIONS ──
            const revealElements = document.querySelectorAll('.reveal-up');
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        revealObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });

            revealElements.forEach(el => revealObserver.observe(el));

            const first = document.querySelector('.tw-btn.active');
            if (first) {
                currentTriwulanIndex = parseInt(first.dataset.index);
                renderKuota(first.dataset.start, first.dataset.end);
            }
        });
    </script>
</body>

</html>