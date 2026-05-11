<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pendaftaran Magang — PUSDATIN PUPR</title>
    <meta name="description" content="Formulir pendaftaran magang di Pusat Data dan Teknologi Informasi Kementerian PUPR">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #2548a8;
            --primary-lighter: #dbeafe;
            --accent: #fbbf24;
            --accent-dark: #d97706;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --radius: 10px;
            --radius-sm: 6px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            font-size: 14px;
            color: var(--text-primary);
            background: #f8fafc;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* Header Bar */
        .form-header {
            background: var(--primary);
            color: #fff;
            padding: 20px 0;
        }

        .form-header .header-inner {
            max-width: 720px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .form-header .header-icon {
            width: 44px; height: 44px; border-radius: 10px;
            background: rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
        }

        .form-header h1 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .form-header p {
            font-size: 12.5px;
            opacity: 0.75;
        }

        /* Form Container */
        .form-container {
            max-width: 720px;
            margin: 0 auto;
            padding: 28px 24px 48px;
        }

        .form-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .form-card .form-body {
            padding: 28px 28px 32px;
        }

        /* Section Headers */
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--primary);
            padding-bottom: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary-lighter);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title .section-icon {
            width: 28px; height: 28px; border-radius: 6px;
            background: var(--primary-lighter);
            color: var(--primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 12px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-group label .required {
            color: #ef4444;
            margin-left: 2px;
        }

        .form-group label .optional {
            color: var(--text-muted);
            font-weight: 400;
            font-size: 12px;
        }

        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'Inter', sans-serif;
            font-size: 13.5px;
            color: var(--text-primary);
            background: #fff;
            transition: all 150ms ease;
            outline: none;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30,58,138,0.1);
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .form-input.is-invalid {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
        }

        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 18px;
            padding-right: 36px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-error {
            font-size: 12px;
            color: #ef4444;
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Radio Group */
        .radio-group {
            display: flex;
            gap: 8px;
        }

        .radio-option {
            flex: 1;
        }

        .radio-option input[type="radio"] {
            display: none;
        }

        .radio-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 150ms ease;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 0;
        }

        .radio-option input[type="radio"]:checked + label {
            background: var(--primary-lighter);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Info Alert */
        .info-alert {
            background: var(--primary-lighter);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            font-size: 12.5px;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        /* File Upload */
        .file-upload {
            border: 2px dashed var(--border);
            border-radius: var(--radius);
            padding: 18px;
            text-align: center;
            transition: all 150ms ease;
            cursor: pointer;
            position: relative;
        }

        .file-upload:hover {
            border-color: var(--primary);
            background: rgba(30,58,138,0.02);
        }

        .file-upload input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload i {
            font-size: 24px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .file-upload .file-text {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .file-upload .file-hint {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 150ms ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 24px;
        }

        .btn-submit:hover {
            background: var(--primary-light);
            box-shadow: 0 4px 14px rgba(30,58,138,0.3);
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Accent divider */
        .accent-bar {
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
        }

        @media (max-width: 640px) {
            .form-row { grid-template-columns: 1fr; }
            .form-card .form-body { padding: 20px 18px 24px; }
        }
    </style>
</head>
<body>

{{-- Accent Bar --}}
<div class="accent-bar"></div>

{{-- Header --}}
<div class="form-header">
    <div class="header-inner">
        <img src="/images/logo-pu.png" alt="Logo PUPR" style="width: 44px; height: 44px; flex-shrink: 0; object-fit: cover;">
        <div>
            <h1>Formulir Pendaftaran Magang</h1>
            <p>Pusat Data dan Teknologi Informasi — Kementerian PUPR</p>
        </div>
    </div>
</div>

{{-- Form --}}
<div class="form-container">
    <div class="form-card">
        <form action="{{ route('pendaftaran.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-body">

                {{-- ═══ SECTION A ═══ --}}
                <div class="section-title">
                    <span class="section-icon"><i class="fas fa-user"></i></span>
                    Data Diri & Institusi
                </div>

                <div class="form-group">
                    <label>Nama Lengkap <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-input @error('nama') is-invalid @enderror" value="{{ old('nama') }}" placeholder="Masukkan nama lengkap" required>
                    @error('nama') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Tingkat Pendidikan <span class="required">*</span></label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" name="tingkat_pendidikan" value="Universitas" id="univ" {{ old('tingkat_pendidikan') == 'Universitas' ? 'checked' : '' }} required onchange="toggleFormLabels()">
                            <label for="univ"><i class="fas fa-university"></i> Universitas</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" name="tingkat_pendidikan" value="SLTA" id="slta" {{ old('tingkat_pendidikan') == 'SLTA' ? 'checked' : '' }} onchange="toggleFormLabels()">
                            <label for="slta"><i class="fas fa-school"></i> SMK</label>
                        </div>
                    </div>
                    @error('tingkat_pendidikan') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label id="label-institusi">Nama Institusi <span class="required">*</span></label>
                        <input type="text" name="nama_institusi" id="input-institusi" class="form-input @error('nama_institusi') is-invalid @enderror" value="{{ old('nama_institusi') }}" placeholder="Pilih tingkat pendidikan dahulu" required {{ old('tingkat_pendidikan') ? '' : 'disabled' }}>
                    </div>
                    <div class="form-group">
                        <label id="label-jurusan">Jurusan / Program Studi <span class="required">*</span></label>
                        <input type="text" name="jurusan" id="input-jurusan" class="form-input @error('jurusan') is-invalid @enderror" value="{{ old('jurusan') }}" placeholder="Pilih tingkat pendidikan dahulu" required {{ old('tingkat_pendidikan') ? '' : 'disabled' }}>
                    </div>
                </div>

                {{-- NIM / NIS (dynamic) --}}
                <div class="form-group">
                    <label id="label-nim-nis">NIM / NIS <span class="required">*</span></label>
                    <input type="text" name="nim_nis" id="input-nim-nis" class="form-input @error('nim_nis') is-invalid @enderror" value="{{ old('nim_nis') }}" placeholder="Pilih tingkat pendidikan dahulu" required {{ old('tingkat_pendidikan') ? '' : 'disabled' }}>
                    @error('nim_nis') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                <div class="section-title" style="margin-top: 32px;">
                    <span class="section-icon"><i class="fas fa-calendar-alt"></i></span>
                    Periode & Waktu Magang
                </div>

                <div class="form-group">
                    <label>Pilih Periode (Triwulan) <span class="required">*</span></label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 8px;">
                        @foreach($triwulans as $tw)
                        <label style="display:flex; align-items:center; gap:8px; padding:10px 14px; border:1px solid var(--border); border-radius:var(--radius-sm); cursor:pointer;" class="periode-label">
                            <input type="checkbox" name="periode_magang[]" value="{{ $tw['value'] }}" class="periode-checkbox" onchange="handlePeriodeChange()" {{ is_array(old('periode_magang')) && in_array($tw['value'], old('periode_magang')) ? 'checked' : '' }}> 
                            <span>{{ $tw['label'] }} {{ $tw['year'] }} <small style="color:var(--text-muted);display:block;">{{ $tw['bulan'] }}</small></span>
                        </label>
                        @endforeach
                    </div>
                    <div id="periode-error" class="form-error" style="display:none;"><i class="fas fa-exclamation-circle"></i> Pilihan Triwulan harus berurutan.</div>
                    @error('periode_magang') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Mulai Magang <span class="required">*</span></label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-input @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}" onchange="updateTimKerja()" required disabled>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Selesai Magang <span class="required">*</span></label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-input @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}" onchange="updateTimKerja()" required disabled>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nomor WhatsApp <span class="required">*</span></label>
                    <input type="tel" name="nomor_telp" class="form-input @error('nomor_telp') is-invalid @enderror" value="{{ old('nomor_telp') }}" placeholder="08xxxxxxxxxx" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email Pribadi <span class="required">*</span></label>
                        <input type="email" name="email" class="form-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="email@gmail.com" required>
                    </div>
                    <div class="form-group">
                        <label>Email Institusi <span class="required">*</span></label>
                        <input type="email" name="email_institusi" class="form-input @error('email_institusi') is-invalid @enderror" value="{{ old('email_institusi') }}" placeholder="nama@kampus.ac.id" required>
                    </div>
                </div>

                {{-- ═══ SECTION B ═══ --}}
                <div class="section-title" style="margin-top: 32px;">
                    <span class="section-icon"><i class="fas fa-briefcase"></i></span>
                    Penempatan & Dokumen
                </div>

                <div class="form-group">
                    <label>Pilih Bidang <span class="required">*</span></label>
                    <select id="select-bidang" name="bidang" class="form-input @error('bidang') is-invalid @enderror" onchange="updateTimKerja()" required disabled>
                        <option value="" disabled selected>— Pilih Periode Dahulu —</option>
                        @foreach($groupedTimKerja as $bidang => $tims)
                            <option value="{{ $bidang }}" {{ old('bidang') == $bidang ? 'selected' : '' }}>{{ $bidang }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Pilihan Tim Kerja 1 <span class="required">*</span></label>
                        <select id="select-tim-kerja-1" name="id_tim_kerja_1" class="form-input @error('id_tim_kerja_1') is-invalid @enderror" onchange="syncTimKerjaDropdowns()" required disabled>
                            <option value="" disabled selected>— Pilih Bidang Dahulu —</option>
                        </select>
                        @error('id_tim_kerja_1') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Pilihan Tim Kerja 2 <span class="required">*</span></label>
                        <select id="select-tim-kerja-2" name="id_tim_kerja_2" class="form-input @error('id_tim_kerja_2') is-invalid @enderror" onchange="syncTimKerjaDropdowns()" required disabled>
                            <option value="" disabled selected>— Pilih Bidang Dahulu —</option>
                        </select>
                        @error('id_tim_kerja_2') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="info-alert">
                    <i class="fas fa-info-circle"></i>
                    Pilih 2 tim kerja yang berbeda sebagai prioritas penempatan Anda.
                </div>

                {{-- Surat Permohonan Magang (wajib, urutan pertama) --}}
                <div class="form-group">
                    <label>Surat Permohonan Magang <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="surat_rekomendasi" accept="application/pdf" id="rekom-input" required>
                        <i class="fas fa-cloud-upload-alt" style="display: block;"></i>
                        <div class="file-text" id="rekom-label">Klik atau seret file ke sini</div>
                        <div class="file-hint">Format .pdf, maksimal 2MB — <strong>Wajib dilampirkan</strong></div>
                    </div>
                    @error('surat_rekomendasi') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                {{-- Curriculum Vitae (opsional, urutan kedua) --}}
                <div class="form-group">
                    <label>Curriculum Vitae (CV) <span class="optional">— Opsional</span></label>
                    <div class="file-upload">
                        <input type="file" name="cv" accept="application/pdf" id="cv-input">
                        <i class="fas fa-cloud-upload-alt" style="display: block;"></i>
                        <div class="file-text" id="cv-label">Klik atau seret file ke sini</div>
                        <div class="file-hint">Format .pdf, maksimal 2MB</div>
                    </div>
                    @error('cv') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                {{-- Pas Foto --}}
                <div class="form-group">
                    <label>Pas Foto <span class="required">*</span></label>
                    <div class="file-upload">
                        <input type="file" name="pas_foto" accept="image/jpeg,image/png,image/jpg" id="foto-input" required>
                        <i class="fas fa-camera" style="display: block;"></i>
                        <div class="file-text" id="foto-label">Klik atau seret foto ke sini</div>
                        <div class="file-hint">Format .jpg / .png, maksimal 1MB — <strong>Untuk foto profil dashboard</strong></div>
                    </div>
                    @error('pas_foto') <div class="form-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    Kirim Lamaran Magang
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Real-time quota calculation data from backend
    const activePeserta = @json($activePeserta);
    const timKerjaList = @json($timKerjaList);
    const groupedTimKerja = @json($groupedTimKerja);
    
    // Map triwulan ke index dinamis untuk validasi berurutan lintas tahun
    const triwulanMap = {
        @foreach($triwulans as $tw)
        "{{ $tw['value'] }}": {{ $tw['index'] }},
        @endforeach
    };
    
    // Map triwulan ke rentang tanggal
    const triwulanDates = {
        @foreach($triwulans as $tw)
        @php
            $s_m = ""; $e_m = "";
            if ($tw['q'] == 1) { $s_m = "01-01"; $e_m = "03-31"; }
            elseif ($tw['q'] == 2) { $s_m = "04-01"; $e_m = "06-30"; }
            elseif ($tw['q'] == 3) { $s_m = "07-01"; $e_m = "09-30"; }
            elseif ($tw['q'] == 4) { $s_m = "10-01"; $e_m = "12-31"; }
        @endphp
        {{ $tw['index'] }}: { start: "{{ $tw['year'] }}-{{ $s_m }}", end: "{{ $tw['year'] }}-{{ $e_m }}" },
        @endforeach
    };

    // ── Toggle labels based on tingkat pendidikan ──
    function toggleFormLabels() {
        const isUniv = document.getElementById('univ').checked;
        const isSmk = document.getElementById('slta').checked;
        const labelInstitusi = document.getElementById('label-institusi');
        const inputInstitusi = document.getElementById('input-institusi');
        const labelJurusan = document.getElementById('label-jurusan');
        const inputJurusan = document.getElementById('input-jurusan');
        const labelNimNis = document.getElementById('label-nim-nis');
        const inputNimNis = document.getElementById('input-nim-nis');

        if (isUniv || isSmk) {
            inputInstitusi.disabled = false;
            inputJurusan.disabled = false;
            inputNimNis.disabled = false;
        }

        if (isUniv) {
            labelInstitusi.innerHTML = 'Nama Universitas <span class="required">*</span>';
            inputInstitusi.placeholder = 'Contoh: Universitas Indonesia';
            labelJurusan.innerHTML = 'Program Studi <span class="required">*</span>';
            inputJurusan.placeholder = 'Contoh: Teknik Informatika';
            labelNimNis.innerHTML = 'NIM <span class="required">*</span>';
            inputNimNis.placeholder = 'Masukkan Nomor Induk Mahasiswa';
        } else if (isSmk) {
            labelInstitusi.innerHTML = 'Nama Sekolah <span class="required">*</span>';
            inputInstitusi.placeholder = 'Contoh: SMK Negeri 1 Jakarta';
            labelJurusan.innerHTML = 'Jurusan <span class="required">*</span>';
            inputJurusan.placeholder = 'Contoh: Teknik Komputer Jaringan';
            labelNimNis.innerHTML = 'NIS <span class="required">*</span>';
            inputNimNis.placeholder = 'Masukkan Nomor Induk Siswa';
        }
    }

    function handlePeriodeChange() {
        const checkboxes = document.querySelectorAll('.periode-checkbox:checked');
        const selected = Array.from(checkboxes).map(cb => triwulanMap[cb.value]).sort((a,b) => a - b);
        const errorMsg = document.getElementById('periode-error');
        const tMulai = document.getElementById('tanggal_mulai');
        const tSelesai = document.getElementById('tanggal_selesai');
        const sBidang = document.getElementById('select-bidang');
        
        let isValid = true;
        if (selected.length > 0) {
            // Check if consecutive
            for(let i=0; i<selected.length-1; i++) {
                if (selected[i+1] - selected[i] !== 1) {
                    isValid = false; break;
                }
            }
        } else {
            isValid = false;
        }

        if (selected.length > 0 && !isValid) {
            errorMsg.style.display = 'block';
            tMulai.disabled = true; tSelesai.disabled = true;
            sBidang.disabled = true;
        } else if (selected.length > 0) {
            errorMsg.style.display = 'none';
            tMulai.disabled = false; tSelesai.disabled = false;
            sBidang.disabled = false;
            
            // Setup Min Max Dates using dynamically generated years in triwulanDates
            const minDate = triwulanDates[selected[0]].start;
            const maxDate = triwulanDates[selected[selected.length - 1]].end;
            
            tMulai.min = minDate;
            tMulai.max = maxDate;
            tSelesai.min = minDate;
            tSelesai.max = maxDate;
            
        } else {
            errorMsg.style.display = 'none';
            tMulai.disabled = true; tSelesai.disabled = true;
            sBidang.disabled = true;
        }
        
        updateTimKerja();
    }

    function getSisaKuotaForTim(timId, fallbackStart, fallbackEnd) {
        let tMulai = document.getElementById('tanggal_mulai').value;
        let tSelesai = document.getElementById('tanggal_selesai').value;
        
        // Use fallback if dates are not completely filled by user yet
        if (!tMulai || !tSelesai) {
            if (!fallbackStart || !fallbackEnd) return 0;
            tMulai = fallbackStart;
            tSelesai = fallbackEnd;
        }

        const tim = timKerjaList.find(t => t.id == timId);
        if (!tim) return 0;

        let overlapCount = 0;
        activePeserta.forEach(p => {
            if (p.id_tim_kerja_1 == timId) {
                // Check if active participant date range overlaps with user's selected date range
                if (p.tanggal_mulai <= tSelesai && p.tanggal_selesai >= tMulai) {
                    overlapCount++;
                }
            }
        });

        return Math.max(0, tim.kuota_maksimal - overlapCount);
    }

    function updateTimKerja() {
        const selectedBidang = document.getElementById('select-bidang').value;
        const checkboxes = document.querySelectorAll('.periode-checkbox:checked');
        const selectedTw = Array.from(checkboxes).map(cb => cb.value);
        
        const t1 = document.getElementById('select-tim-kerja-1');
        const t2 = document.getElementById('select-tim-kerja-2');
        const oldTim1 = "{{ old('id_tim_kerja_1') }}";
        const oldTim2 = "{{ old('id_tim_kerja_2') }}";

        t1.innerHTML = '<option value="" disabled selected>— Pilih Tim Kerja 1 —</option>';
        t2.innerHTML = '<option value="" disabled selected>— Pilih Tim Kerja 2 —</option>';

        if (selectedBidang && selectedTw.length > 0 && document.getElementById('periode-error').style.display === 'none') {
            t1.disabled = false;
            t2.disabled = false;
            
            // Calculate fallback dates from selected Triwulans
            const sortedTw = Array.from(checkboxes).map(cb => triwulanMap[cb.value]).sort((a,b) => a - b);
            const fallbackStart = triwulanDates[sortedTw[0]].start;
            const fallbackEnd = triwulanDates[sortedTw[sortedTw.length - 1]].end;

            if (groupedTimKerja[selectedBidang]) {
                groupedTimKerja[selectedBidang].forEach(timBase => {
                    const realSisa = getSisaKuotaForTim(timBase.id, fallbackStart, fallbackEnd);
                    const isFull = realSisa <= 0;
                    const label = isFull ? timBase.nama_tim + ' (Penuh)' : timBase.nama_tim + ' (Sisa: ' + realSisa + ')';

                    let o1 = new Option(label, timBase.id, false, oldTim1 == timBase.id);
                    o1.disabled = isFull;
                    t1.add(o1);

                    let o2 = new Option(label, timBase.id, false, oldTim2 == timBase.id);
                    o2.disabled = isFull;
                    t2.add(o2);
                });
            }
            syncTimKerjaDropdowns();
        } else {
            t1.disabled = true;
            t2.disabled = true;
        }
    }

    function syncTimKerjaDropdowns() {
        const t1 = document.getElementById('select-tim-kerja-1');
        const t2 = document.getElementById('select-tim-kerja-2');
        const v1 = t1.value, v2 = t2.value;
        const bidang = document.getElementById('select-bidang').value;
        const checkboxes = document.querySelectorAll('.periode-checkbox:checked');
        const selectedTw = Array.from(checkboxes).map(cb => cb.value);
        if (!bidang || selectedTw.length === 0) return;
        
        // Get fallback dates
        const sortedTw = Array.from(checkboxes).map(cb => triwulanMap[cb.value]).sort((a,b) => a - b);
        const fallbackStart = triwulanDates[sortedTw[0]].start;
        const fallbackEnd = triwulanDates[sortedTw[sortedTw.length - 1]].end;

        Array.from(t2.options).forEach(opt => {
            if (opt.value === "") return;
            const realSisa = getSisaKuotaForTim(opt.value, fallbackStart, fallbackEnd);
            opt.disabled = (opt.value === v1) || (realSisa <= 0);
        });

        Array.from(t1.options).forEach(opt => {
            if (opt.value === "") return;
            const realSisa = getSisaKuotaForTim(opt.value, fallbackStart, fallbackEnd);
            opt.disabled = (opt.value === v2) || (realSisa <= 0);
        });
    }

    // File upload labels
    document.getElementById('cv-input').addEventListener('change', function() {
        document.getElementById('cv-label').textContent = this.files[0] ? this.files[0].name : 'Klik atau seret file ke sini';
    });
    document.getElementById('rekom-input').addEventListener('change', function() {
        document.getElementById('rekom-label').textContent = this.files[0] ? this.files[0].name : 'Klik atau seret file ke sini';
    });
    document.getElementById('foto-input').addEventListener('change', function() {
        document.getElementById('foto-label').textContent = this.files[0] ? this.files[0].name : 'Klik atau seret foto ke sini';
    });

    // Restore on load
    document.addEventListener("DOMContentLoaded", function() {
        handlePeriodeChange(); // initialize periods & date inputs
        if (document.getElementById('select-bidang').value !== "") {
            updateTimKerja();
        }
        // Trigger label toggle if radio was restored via old()
        if (document.querySelector('input[name="tingkat_pendidikan"]:checked')) {
            toggleFormLabels();
        }
    });
</script>
</body>
</html>