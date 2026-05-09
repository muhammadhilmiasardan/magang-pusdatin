@extends('layouts.admin')

@section('title', 'Pusat Dokumen')

@section('content')
<div class="card-clean">
    {{-- Tab Navigation --}}
    <div class="tab-nav-clean" role="tablist">
        <a class="tab-item active" href="#tab-sk" data-tab="sk" onclick="switchTab('sk', event)">
            <i class="fas fa-file-contract" style="font-size: 13px;"></i>
            SK Magang
            <span class="tab-count">{{ $skMagang->count() }}</span>
        </a>
        <a class="tab-item" href="#tab-evaluasi" data-tab="evaluasi" onclick="switchTab('evaluasi', event)">
            <i class="fas fa-paper-plane" style="font-size: 13px;"></i>
            Evaluasi (H-7)
            <span class="tab-count">{{ $evaluasi->count() }}</span>
        </a>
        <a class="tab-item" href="#tab-sertifikat" data-tab="sertifikat" onclick="switchTab('sertifikat', event)">
            <i class="fas fa-certificate" style="font-size: 13px;"></i>
            Sertifikat
            <span class="tab-count">{{ $sertifikat->count() }}</span>
        </a>
    </div>

    {{-- ═══ TAB 1: SK MAGANG ═══ --}}
    <div class="tab-panel" id="tab-sk" role="tabpanel">
        <div style="padding: 16px 22px 8px;">
            <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">
                Daftar peserta aktif untuk penerbitan Surat Keterangan (SK) Magang.
            </p>
        </div>

        @if($skMagang->count() == 0)
            <div class="empty-state">
                <i class="fas fa-file-contract" style="display: block;"></i>
                <p>Tidak ada data peserta aktif.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Nama Peserta</th>
                            <th>Institusi</th>
                            <th>Periode Magang</th>
                            <th>Status SK</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($skMagang as $item)
                        <tr>
                            <td style="font-weight: 600;">{{ $item->nama }}</td>
                            <td>{{ $item->nama_institusi }}</td>
                            <td style="font-size: 12.5px; white-space: nowrap;">
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                <span style="color: var(--text-muted);"> s/d </span>
                                {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td>
                                @if($item->is_sk_sent)
                                    <span class="badge-status badge-sent"><i class="fas fa-check"></i> Terkirim</span>
                                @else
                                    <span class="badge-status badge-pending"><i class="fas fa-clock"></i> Belum Dikirim</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button class="btn-primary-custom btn-sm-custom" onclick="openSkModal({{ $item->id }})">
                                    <i class="fas fa-file-signature"></i> Proses SK
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ═══ TAB 2: EVALUASI H-7 ═══ --}}
    <div class="tab-panel hidden" id="tab-evaluasi" role="tabpanel">
        <div style="
            margin: 16px 22px 0;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
        ">
            <div style="
                width: 36px; height: 36px; border-radius: 8px;
                background: var(--accent); color: var(--primary-dark);
                display: flex; align-items: center; justify-content: center;
                font-size: 15px; flex-shrink: 0;
            ">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div style="font-size: 13px; font-weight: 600; color: #92400e;">Perhatian</div>
                <div style="font-size: 12.5px; color: #a16207;">
                    Menampilkan peserta berstatus Aktif yang masa magangnya <strong>telah selesai</strong> hari ini atau sebelumnya.
                    Admin dapat memproses Lembar Evaluasi dengan meminta nilai dari Pembimbing Lapangan.
                </div>
            </div>
        </div>

        @if($evaluasi->count() == 0)
            <div class="empty-state">
                <i class="fas fa-calendar-check" style="display: block;"></i>
                <p>Tidak ada peserta yang mendekati akhir masa magang (H-7 s/d H-14).</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Nama Peserta</th>
                            <th>Institusi</th>
                            <th>Tgl Selesai</th>
                            <th>Sisa Waktu</th>
                            <th>Status</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluasi as $item)
                        @php
                            $sisaHari = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($item->tanggal_selesai), false);
                        @endphp
                        <tr>
                            <td style="font-weight: 600;">{{ $item->nama }}</td>
                            <td>
                                <div>{{ $item->nama_institusi }}</div>
                                <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 2px;">
                                    <i class="fas fa-envelope" style="font-size: 10px;"></i> {{ $item->email_institusi }}
                                </div>
                            </td>
                            <td style="font-size: 13px; white-space: nowrap;">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</td>
                            <td>
                                <span style="
                                    font-weight: 700; font-size: 14px;
                                    color: {{ $sisaHari <= 7 ? '#ef4444' : '#f59e0b' }};
                                ">{{ $sisaHari }} Hari</span>
                            </td>
                            <td>
                                @if($item->is_evaluasi_sent)
                                    <span class="badge-status badge-sent"><i class="fas fa-check"></i> Terkirim</span>
                                @else
                                    <span class="badge-status badge-pending"><i class="fas fa-clock"></i> Belum</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button class="btn-accent-custom btn-sm-custom" onclick="openEvaluasiModal({{ $item->id }})">
                                    <i class="fas fa-file-signature"></i> Proses Evaluasi
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ═══ TAB 3: SERTIFIKAT ═══ --}}
    <div class="tab-panel hidden" id="tab-sertifikat" role="tabpanel">
        <div style="padding: 16px 22px 8px;">
            <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">
                Daftar peserta aktif yang masa magangnya telah berakhir dan belum menerima sertifikat.
            </p>
        </div>

        @if($sertifikat->count() == 0)
            <div class="empty-state">
                <i class="fas fa-award" style="display: block;"></i>
                <p>Semua peserta telah menerima sertifikat. 🎉</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Nama Peserta</th>
                            <th>Institusi</th>
                            <th>Periode Magang</th>
                            <th>Status</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sertifikat as $item)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $item->nama }}</div>
                                <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 2px;">
                                    <i class="fas fa-envelope" style="font-size: 10px;"></i> {{ $item->email }}
                                </div>
                            </td>
                            <td>{{ $item->nama_institusi }}</td>
                            <td style="font-size: 12.5px; white-space: nowrap;">
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                <span style="color: var(--text-muted);"> s/d </span>
                                {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td>
                                @if($item->is_sertifikat_sent)
                                    <span class="badge-status badge-sent"><i class="fas fa-check"></i> Terkirim</span>
                                @else
                                    <span class="badge-status badge-pending"><i class="fas fa-clock"></i> Belum Dikirim</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button class="btn-success-custom btn-sm-custom" onclick="openSertifikatModal({{ $item->id }})">
                                    <i class="fas fa-certificate"></i> Proses Sertifikat
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
</div>

{{-- ═══ MODAL SK MAGANG (3 STEP) ═══ --}}
<div id="skModal" class="modal-overlay hidden">
    <div class="modal-content" style="max-width: 900px; width: 95%;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div id="sk-step-text" style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">LANGKAH 1 DARI 3</div>
                <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);margin:0;">
                    <i id="sk-step-icon" class="fas fa-file-pdf" style="color:var(--accent);margin-right:8px;"></i>
                    <span id="sk-step-title-text">Cetak &amp; TTD</span>
                </h3>
            </div>
            <button onclick="closeSkModal()" style="background:none;border:none;cursor:pointer;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-size:16px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="modal-body" style="padding: 24px;">

            {{-- STEP 1: CETAK & PREVIEW --}}
            <div id="step-1-content" class="step-content">
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    
                    {{-- Form Kiri --}}
                    <div style="flex: 1; min-width: 260px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                        <h4 style="margin: 0 0 14px 0; font-size: 14px; color: #334155;">Input Data Surat</h4>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Nomor Surat</label>
                            <input type="text" id="nomor_surat" placeholder="B.1234/5678" class="form-input" style="padding: 6px 10px; font-size: 13px;" value="B.1234/5678">
                        </div>
                        <button type="button" class="btn-outline-custom" style="width: 100%; margin-top: 5px; justify-content: center;" onclick="reloadPreview()">
                            <i class="fas fa-save"></i> Simpan Data SK Magang
                        </button>
                    </div>

                    {{-- Preview Kanan --}}
                    <div id="skPreviewWrapper" style="flex: 2; min-width: 400px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 14px; color: #334155;">Preview Surat Keterangan</h4>
                        <div class="iframe-fit-container" style="position: relative; width: 100%; padding-top: 141.42%; overflow: hidden; border: 1px solid #cbd5e1; border-radius: 6px; background: #64748b;">
                            <iframe id="skPreviewFrame" src="" style="position: absolute; top: 0; left: 0; width: 794px; height: 1123px; border: none; transform-origin: top left;" onload="fitIframe(this)"></iframe>
                        </div>
                    </div>

                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                    <a href="#" id="btnDownloadSk" class="btn-success-custom" style="text-decoration: none; display: inline-block;" onclick="this.dataset.downloaded = 'true'">
                        <i class="fas fa-download"></i> Download PDF untuk di-TTD
                    </a>
                    <button class="btn-primary-custom" onclick="nextStep(2)">
                        Lanjut ke Upload <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- STEP 2: UPLOAD --}}
            <div id="step-2-content" class="step-content hidden">
                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:12.5px;color:#92400e;line-height:1.6;">
                    <strong><i class="fas fa-info-circle"></i> Petunjuk:</strong><br>
                    1. Download draft PDF dari Langkah 1<br>
                    2. Lakukan Cetak &amp; TTD basah, tambahkan lampiran secara manual (jika ada)<br>
                    3. Upload file final di sini (PDF, maks 5MB)
                </div>
                <form id="formUploadSk" style="margin-bottom:20px;">
                    <input type="hidden" id="peserta_id" name="peserta_id">
                    <div id="upload-dropzone-surat_ttd" style="border:2px dashed var(--border);border-radius:10px;padding:32px;text-align:center;cursor:pointer;transition:all 200ms ease;background:#fafafa;" onclick="document.getElementById('surat_ttd').click()" ondragover="event.preventDefault();this.style.borderColor='var(--primary)';this.style.background='var(--primary-lighter)'" ondragleave="this.style.borderColor='var(--border)';this.style.background='#fafafa'" ondrop="handleDrop(event, 'surat_ttd')">
                        <i class="fas fa-cloud-upload-alt" style="font-size:32px;color:var(--text-muted);margin-bottom:10px;display:block;"></i>
                        <div style="font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:4px;">Klik atau drag &amp; drop file di sini</div>
                        <div style="font-size:11px;color:var(--text-muted);">PDF — Maksimal 5MB</div>
                        <input type="file" id="surat_ttd" name="surat_ttd" accept=".pdf" style="display:none;" onchange="handleFileSelect(this, 'surat_ttd')" required>
                    </div>
                    <div id="upload-file-preview-surat_ttd" style="display:none;margin-top:12px;padding:12px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;align-items:center;gap:10px;">
                        <i class="fas fa-file-check" style="color:#16a34a;font-size:18px;flex-shrink:0;"></i>
                        <div style="flex:1;overflow:hidden;">
                            <div id="upload-file-name-surat_ttd" style="font-size:13px;font-weight:600;color:#15803d;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">-</div>
                            <div id="upload-file-size-surat_ttd" style="font-size:11px;color:#16a34a;">-</div>
                        </div>
                        <button type="button" onclick="clearFile('surat_ttd')" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:14px;flex-shrink:0;"><i class="fas fa-times"></i></button>
                    </div>
                </form>
                <div style="display: flex; justify-content: space-between;">
                    <button class="btn-outline-custom" onclick="nextStep(1)" style="padding-left: 20px; padding-right: 20px;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                    <button class="btn-primary-custom" onclick="nextStep(3)">
                        Lanjut ke Email <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- STEP 3: KIRIM EMAIL --}}
            <div id="step-3-content" class="step-content hidden">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Pesan Email Tambahan (Opsional)</label>
                    <textarea id="pesan_email" form="formUploadSk" class="form-input" rows="4" placeholder="Terlampir adalah Surat Keterangan Magang..."></textarea>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <button class="btn-outline-custom" onclick="nextStep(2)" style="padding-left: 20px; padding-right: 20px;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                    <button class="btn-primary-custom" onclick="submitSkMagang()" id="btnSubmitSk">
                        <i class="fas fa-paper-plane"></i> Kirim Surat via Email
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL EVALUASI --}}
<div id="evaluasiModal" class="modal-overlay hidden">
    <div class="modal-content" style="max-width: 900px; width: 95%;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div id="ev-step-text" style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">LANGKAH 1 DARI 3</div>
                <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);margin:0;">
                    <i id="ev-step-icon" class="fas fa-file-pdf" style="color:var(--accent);margin-right:8px;"></i>
                    <span id="ev-step-title-text">Form &amp; Preview</span>
                </h3>
            </div>
            <button onclick="closeEvaluasiModal()" style="background:none;border:none;cursor:pointer;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-size:16px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="modal-body" style="padding: 24px;">
            <input type="hidden" id="evaluasi_peserta_id" value="">

            {{-- STEP 1: FORM & PREVIEW --}}
            <div id="ev-step-1-content" class="step-content">
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                    
                    {{-- Form Kiri --}}
                    <div style="flex: 1; min-width: 300px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 14px; color: #334155;">Input Data Evaluasi</h4>
                        <form id="formEvaluasiData">
                            <div style="margin-bottom: 12px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Nomor Surat</label>
                                <input type="text" id="ev_nomor_surat" name="nomor_surat" class="form-input" style="padding: 6px 10px; font-size: 13px;" placeholder="Contoh: B.1234/5678">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Jabatan Tujuan (Setelah Bapak/Ibu)</label>
                                <input type="text" id="ev_kepada_yth" name="kepada_yth" class="form-input" style="padding: 6px 10px; font-size: 13px;" placeholder="Contoh: Kepala Sekolah / Rektor">
                            </div>
                            
                            <div style="font-size: 12px; font-weight: 600; margin-bottom: 8px; color: #475569;">Nilai (Skala 8.5 s/d 10)</div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <div>
                                    <label style="font-size: 11px;">1. Kedisiplinan</label>
                                    <input type="number" step="0.1" name="nilai_1" id="ev_nilai_1" class="form-input" style="padding: 4px 8px; font-size: 12px;">
                                </div>
                                <div>
                                    <label style="font-size: 11px;">2. Tanggung Jawab</label>
                                    <input type="number" step="0.1" name="nilai_2" id="ev_nilai_2" class="form-input" style="padding: 4px 8px; font-size: 12px;">
                                </div>
                                <div>
                                    <label style="font-size: 11px;">3. Kejujuran</label>
                                    <input type="number" step="0.1" name="nilai_3" id="ev_nilai_3" class="form-input" style="padding: 4px 8px; font-size: 12px;">
                                </div>
                                <div>
                                    <label style="font-size: 11px;">4. Etika</label>
                                    <input type="number" step="0.1" name="nilai_4" id="ev_nilai_4" class="form-input" style="padding: 4px 8px; font-size: 12px;">
                                </div>
                                <div>
                                    <label style="font-size: 11px;">5. Kualitas Kerja</label>
                                    <input type="number" step="0.1" name="nilai_5" id="ev_nilai_5" class="form-input" style="padding: 4px 8px; font-size: 12px;">
                                </div>
                                <div>
                                    <label style="font-size: 11px;">6. Kemampuan Teknis</label>
                                    <input type="number" step="0.1" name="nilai_6" id="ev_nilai_6" class="form-input" style="padding: 4px 8px; font-size: 12px;">
                                </div>
                                <div>
                                    <label style="font-size: 11px;">7. Inisiatif</label>
                                    <input type="number" step="0.1" name="nilai_7" id="ev_nilai_7" class="form-input" style="padding: 4px 8px; font-size: 12px;">
                                </div>
                                <div>
                                    <label style="font-size: 11px;">8. Kerjasama Tim</label>
                                    <input type="number" step="0.1" name="nilai_8" id="ev_nilai_8" class="form-input" style="padding: 4px 8px; font-size: 12px;">
                                </div>
                                <div>
                                    <label style="font-size: 11px;">9. Komunikasi</label>
                                    <input type="number" step="0.1" name="nilai_9" id="ev_nilai_9" class="form-input" style="padding: 4px 8px; font-size: 12px;">
                                </div>
                                <div>
                                    <label style="font-size: 11px;">10. Kemauan Belajar</label>
                                    <input type="number" step="0.1" name="nilai_10" id="ev_nilai_10" class="form-input" style="padding: 4px 8px; font-size: 12px;">
                                </div>
                            </div>
                            <button type="button" class="btn-outline-custom" style="width: 100%; margin-top: 15px; justify-content: center;" onclick="simpanDraftEvaluasi()" id="btnSimpanEvaluasi">
                                <i class="fas fa-save"></i> Simpan Data Evaluasi
                            </button>
                        </form>
                    </div>

                    {{-- Preview Kanan (portrait iframe) --}}
                    <div id="evaluasiPreviewWrapper" style="flex: 2; min-width: 400px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 14px; color: #334155;">Preview Lembar Evaluasi</h4>
                        {{-- Rasio A4 Portrait: 21/29.7 = 0.707. padding-top trick = (29.7/21)*100 = 141.42% --}}
                        <div class="iframe-fit-container" style="
                            position: relative;
                            width: 100%;
                            padding-top: 141.42%;
                            background: #64748b;
                            border-radius: 6px;
                            overflow: hidden;
                            border: 1px solid #cbd5e1;
                        ">
                            <iframe id="evaluasiPreviewFrame" src=""
                                style="
                                    position: absolute;
                                    top: 0; left: 0;
                                    width: 794px; height: 1123px;
                                    border: none;
                                    transform-origin: top left;
                                " onload="fitIframe(this)"
                            ></iframe>
                        </div>
                    </div>

                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                    <a href="#" id="btnDownloadEvaluasi" class="btn-success-custom" style="text-decoration: none; display: inline-block;" onclick="this.dataset.downloaded = 'true'">
                        <i class="fas fa-download"></i> Download PDF untuk di-TTD
                    </a>
                    <button class="btn-primary-custom" onclick="nextEvStep(2)">
                        Lanjut ke Upload <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- STEP 2: UPLOAD TTD --}}
            <div id="ev-step-2-content" class="step-content hidden">
                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:12.5px;color:#92400e;line-height:1.6;">
                    <strong><i class="fas fa-info-circle"></i> Petunjuk:</strong><br>
                    1. Download draft PDF Lembar Evaluasi dari Langkah 1<br>
                    2. Lakukan Cetak &amp; minta TTD serta Cap Instansi secara manual<br>
                    3. Upload file scan final di sini (PDF, maks 5MB)
                </div>
                <form id="formUploadEvaluasi" enctype="multipart/form-data" style="margin-bottom:20px;">
                    <div id="upload-dropzone-ev_surat_ttd" style="border:2px dashed var(--border);border-radius:10px;padding:32px;text-align:center;cursor:pointer;transition:all 200ms ease;background:#fafafa;" onclick="document.getElementById('ev_surat_ttd').click()" ondragover="event.preventDefault();this.style.borderColor='var(--primary)';this.style.background='var(--primary-lighter)'" ondragleave="this.style.borderColor='var(--border)';this.style.background='#fafafa'" ondrop="handleDrop(event, 'ev_surat_ttd')">
                        <i class="fas fa-cloud-upload-alt" style="font-size:32px;color:var(--text-muted);margin-bottom:10px;display:block;"></i>
                        <div style="font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:4px;">Klik atau drag &amp; drop file di sini</div>
                        <div style="font-size:11px;color:var(--text-muted);">PDF — Maksimal 5MB</div>
                        <input type="file" id="ev_surat_ttd" name="ev_surat_ttd" accept=".pdf" style="display:none;" onchange="handleFileSelect(this, 'ev_surat_ttd')">
                    </div>
                    <div id="upload-file-preview-ev_surat_ttd" style="display:none;margin-top:12px;padding:12px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;align-items:center;gap:10px;">
                        <i class="fas fa-file-check" style="color:#16a34a;font-size:18px;flex-shrink:0;"></i>
                        <div style="flex:1;overflow:hidden;">
                            <div id="upload-file-name-ev_surat_ttd" style="font-size:13px;font-weight:600;color:#15803d;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">-</div>
                            <div id="upload-file-size-ev_surat_ttd" style="font-size:11px;color:#16a34a;">-</div>
                        </div>
                        <button type="button" onclick="clearFile('ev_surat_ttd')" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:14px;flex-shrink:0;"><i class="fas fa-times"></i></button>
                    </div>
                </form>
                <div style="display: flex; justify-content: space-between;">
                    <button class="btn-outline-custom" onclick="nextEvStep(1)" style="padding-left: 20px; padding-right: 20px;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                    <button class="btn-primary-custom" onclick="nextEvStep(3)">
                        Lanjut ke Email <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- STEP 3: KIRIM EMAIL --}}
            <div id="ev-step-3-content" class="step-content hidden">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Pesan Email Tambahan (Opsional)</label>
                    <textarea id="ev_pesan_email" form="formUploadEvaluasi" class="form-input" rows="4" placeholder="Terlampir adalah Lembar Evaluasi Magang Anda..."></textarea>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <button class="btn-outline-custom" onclick="nextEvStep(2)" style="padding-left: 20px; padding-right: 20px;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                    <button class="btn-primary-custom" onclick="submitEvaluasi()" id="btnSubmitEvaluasi">
                        <i class="fas fa-paper-plane"></i> Kirim Evaluasi via Email
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ MODAL SERTIFIKAT (3 STEP) ═══ --}}
<div id="sertifikatModal" class="modal-overlay hidden">
    <div class="modal-content" style="max-width: 1000px; width: 96%;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div id="sert-step-text" style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">LANGKAH 1 DARI 3</div>
                <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);margin:0;">
                    <i id="sert-step-icon" class="fas fa-file-pdf" style="color:var(--accent);margin-right:8px;"></i>
                    <span id="sert-step-title-text">Form &amp; Preview</span>
                </h3>
            </div>
            <button onclick="closeSertifikatModal()" style="background:none;border:none;cursor:pointer;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-size:16px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'"><i class="fas fa-times"></i></button>
        </div>

        <div class="modal-body" style="padding: 24px;">
            <input type="hidden" id="sertifikat_peserta_id" value="">

            {{-- STEP 1: FORM & PREVIEW --}}
            <div id="sert-step-1-content" class="step-content">
                <div style="display: flex; gap: 20px; flex-wrap: wrap;">

                    {{-- Form Kiri --}}
                    <div style="flex: 1; min-width: 260px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                        <h4 style="margin: 0 0 14px 0; font-size: 14px; color: #334155;">Data Sertifikat</h4>
                        <form id="formSertifikatData">
                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">Nomor Sertifikat</label>
                                <input type="text" id="sert_nomor" name="nomor_sertifikat" class="form-input" style="padding: 7px 10px; font-size: 13px;" placeholder="KP0902/B/Sd/2026/...">
                            </div>
                            <div style="margin-bottom: 14px;">
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 5px;">Predikat</label>
                                <select id="sert_predikat" name="predikat" class="form-input" style="padding: 7px 10px; font-size: 13px;">
                                    <option value="">-- Pilih Predikat --</option>
                                    <option value="Sangat Baik">Sangat Baik</option>
                                    <option value="Baik">Baik</option>
                                    <option value="Cukup">Cukup</option>
                                </select>
                            </div>
                            <button type="button" class="btn-outline-custom" style="width: 100%; margin-top: 15px; justify-content: center;" onclick="simpanDraftSertifikat()" id="btnSimpanSertifikat">
                                <i class="fas fa-save"></i> Simpan Data Sertifikat
                            </button>
                        </form>
                    </div>

                    {{-- Preview Kanan (landscape iframe) --}}
                    <div id="sertifikatPreviewWrapper" style="flex: 2.5; min-width: 420px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 14px; color: #334155;">Preview Sertifikat</h4>
                        {{-- Rasio A4 landscape: 29.7/21 = 1.4143. padding-top trick = (21/29.7)*100 = 70.7% --}}
                        <div class="iframe-fit-container" style="
                            position: relative;
                            width: 100%;
                            padding-top: 70.7%;
                            background: #64748b;
                            border-radius: 6px;
                            overflow: hidden;
                            border: 1px solid #cbd5e1;
                        ">
                            <iframe id="sertifikatPreviewFrame" src=""
                                style="
                                    position: absolute;
                                    top: 0; left: 0;
                                    width: 1123px; height: 794px; /* Landscape A4 */
                                    border: none;
                                    transform-origin: top left;
                                " onload="fitIframe(this)"
                            ></iframe>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                    <a href="#" id="btnDownloadSertifikat" class="btn-success-custom" style="text-decoration: none; display: inline-block;" onclick="this.dataset.downloaded = 'true'">
                        <i class="fas fa-download"></i> Download PDF untuk di-TTE
                    </a>
                    <button class="btn-primary-custom" onclick="nextSertStep(2)">
                        Lanjut ke Upload <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- STEP 2: UPLOAD TTE --}}
            <div id="sert-step-2-content" class="step-content hidden">
                <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:12.5px;color:#92400e;line-height:1.6;">
                    <strong><i class="fas fa-info-circle"></i> Petunjuk:</strong><br>
                    1. Download draft PDF Sertifikat dari Langkah 1<br>
                    2. Lakukan proses Tanda Tangan Elektronik (TTE) Kepala PUSDATIN<br>
                    3. Upload file sertifikat ber-TTE di sini (PDF, maks 5MB)
                </div>
                <form id="formUploadSertifikat" enctype="multipart/form-data" style="margin-bottom:20px;">
                    <div id="upload-dropzone-sert_ttd" style="border:2px dashed var(--border);border-radius:10px;padding:32px;text-align:center;cursor:pointer;transition:all 200ms ease;background:#fafafa;" onclick="document.getElementById('sert_ttd').click()" ondragover="event.preventDefault();this.style.borderColor='var(--primary)';this.style.background='var(--primary-lighter)'" ondragleave="this.style.borderColor='var(--border)';this.style.background='#fafafa'" ondrop="handleDrop(event, 'sert_ttd')">
                        <i class="fas fa-cloud-upload-alt" style="font-size:32px;color:var(--text-muted);margin-bottom:10px;display:block;"></i>
                        <div style="font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:4px;">Klik atau drag &amp; drop file di sini</div>
                        <div style="font-size:11px;color:var(--text-muted);">PDF — Maksimal 5MB</div>
                        <input type="file" id="sert_ttd" name="sert_ttd" accept=".pdf" style="display:none;" onchange="handleFileSelect(this, 'sert_ttd')">
                    </div>
                    <div id="upload-file-preview-sert_ttd" style="display:none;margin-top:12px;padding:12px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;align-items:center;gap:10px;">
                        <i class="fas fa-file-check" style="color:#16a34a;font-size:18px;flex-shrink:0;"></i>
                        <div style="flex:1;overflow:hidden;">
                            <div id="upload-file-name-sert_ttd" style="font-size:13px;font-weight:600;color:#15803d;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">-</div>
                            <div id="upload-file-size-sert_ttd" style="font-size:11px;color:#16a34a;">-</div>
                        </div>
                        <button type="button" onclick="clearFile('sert_ttd')" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:14px;flex-shrink:0;"><i class="fas fa-times"></i></button>
                    </div>
                </form>
                <div style="display: flex; justify-content: space-between;">
                    <button class="btn-outline-custom" onclick="nextSertStep(1)" style="padding-left: 20px; padding-right: 20px;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                    <button class="btn-primary-custom" onclick="nextSertStep(3)">
                        Lanjut ke Email <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- STEP 3: KIRIM EMAIL --}}
            <div id="sert-step-3-content" class="step-content hidden">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Pesan Email Tambahan (Opsional)</label>
                    <textarea id="sert_pesan_email" class="form-input" rows="4" placeholder="Terlampir adalah Sertifikat Magang Anda dari PUSDATIN PUPR..."></textarea>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <button class="btn-outline-custom" onclick="nextSertStep(2)" style="padding-left: 20px; padding-right: 20px;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </button>
                    <button class="btn-primary-custom" onclick="submitSertifikat()" id="btnSubmitSertifikat">
                        <i class="fas fa-paper-plane"></i> Kirim Sertifikat via Email
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .hidden { display: none !important; }
    
    /* MODAL STYLES */
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); z-index: 1000;
        display: flex; justify-content: center; align-items: center;
    }
    .modal-content {
        background: #fff; border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        max-height: 90vh; overflow: hidden;
        display: flex; flex-direction: column;
    }
    .modal-body {
        flex: 1; overflow-y: auto;
    }
    .modal-header {
        padding: 16px 24px; border-bottom: 1px solid #f1f5f9;
        display: flex; justify-content: space-between; align-items: center;
    }
    .modal-title { font-size: 18px; font-weight: 600; color: #0f172a; margin: 0; }
    .modal-close {
        background: none; border: none; font-size: 20px; color: #94a3b8;
        cursor: pointer; padding: 4px; border-radius: 4px;
    }
    .modal-close:hover { background: #f1f5f9; color: #0f172a; }

    /* STEPPER STYLES */
    .stepper-container {
        display: flex; align-items: center; margin-bottom: 30px;
        background: #f8fafc; padding: 16px; border-radius: 8px;
    }
    .step {
        display: flex; align-items: center; gap: 10px; opacity: 0.5; transition: all 0.3s;
    }
    .step.active { opacity: 1; }
    .step.completed { opacity: 1; color: var(--success); }
    .step-icon {
        width: 32px; height: 32px; border-radius: 50%;
        background: #e2e8f0; color: #64748b;
        display: flex; align-items: center; justify-content: center;
        font-weight: bold;
    }
    .step.active .step-icon { background: var(--primary); color: white; }
    .step.completed .step-icon { background: var(--success); color: white; }
    .step-text { font-size: 14px; font-weight: 600; }
    .step-divider {
        flex-grow: 1; height: 2px; background: #e2e8f0; margin: 0 15px;
    }

    .form-input {
        width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1;
        border-radius: 6px; font-size: 14px; outline: none; transition: border-color 0.2s;
    }
    .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
</style>
@endpush

@push('scripts')
<script>
    function switchTab(tabId, e) {
        e.preventDefault();
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
        e.currentTarget.classList.add('active');
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById('tab-' + tabId).classList.remove('hidden');
    }

    // Iframe Scaling to fit without scrolling
    function fitIframe(iframe) {
        if (!iframe || iframe.offsetParent === null) return; // if hidden
        const parentWidth = iframe.parentElement.offsetWidth;
        const iframeWidth = parseFloat(iframe.style.width);
        if(parentWidth > 0 && iframeWidth > 0) {
            const scale = parentWidth / iframeWidth;
            iframe.style.transform = `scale(${scale})`;
        }
    }

    window.addEventListener('resize', () => {
        document.querySelectorAll('.iframe-fit-container iframe').forEach(fitIframe);
    });

    // Modal SK Magang Functions
    function openSkModal(id) {
        document.getElementById('peserta_id').value = id;
        document.getElementById('skModal').classList.remove('hidden');
        
        // Reset download status
        document.getElementById('btnDownloadSk').dataset.downloaded = 'false';
        
        // Use default nomor surat initially
        reloadPreview(id);

        // Reset Steps
        nextStep(1);
        document.getElementById('formUploadSk').reset();
    }

    function reloadPreview(forceId = null) {
        const id = forceId || document.getElementById('peserta_id').value;
        const nomor = document.getElementById('nomor_surat').value;
        
        // Load Iframe
        document.getElementById('skPreviewFrame').src = `/admin/dokumen/sk-magang/${id}/preview?nomor_surat=${encodeURIComponent(nomor)}`;
        
        // Set Download Link
        document.getElementById('btnDownloadSk').href = `/admin/dokumen/sk-magang/${id}/download?nomor_surat=${encodeURIComponent(nomor)}`;
    }

    function closeSkModal() {
        document.getElementById('skModal').classList.add('hidden');
        document.getElementById('skPreviewFrame').src = '';
    }

    function nextStep(step) {
        // VALIDASI SK MAGANG
        if (step === 2) {
            const noSurat = document.getElementById('nomor_surat').value.trim();
            if (!noSurat) {
                alert('Silakan isi Nomor Surat terlebih dahulu.');
                return;
            }
            if (document.getElementById('btnDownloadSk').dataset.downloaded !== 'true') {
                alert('Silakan Download PDF terlebih dahulu sebelum lanjut ke tahap Upload.');
                return;
            }
        } else if (step === 3) {
            const fileUpload = document.getElementById('surat_ttd');
            if (!fileUpload.files || fileUpload.files.length === 0) {
                alert('Silakan upload file Surat Keterangan yang sudah di-TTD terlebih dahulu.');
                return;
            }
        }

        // Update Content Visibility
        document.querySelectorAll('#skModal .step-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('step-' + step + '-content').classList.remove('hidden');

        // Update Dynamic Header
        document.getElementById('sk-step-text').textContent = 'Langkah ' + step + ' dari 3';
        const titleText = document.getElementById('sk-step-title-text');
        const icon = document.getElementById('sk-step-icon');
        if(step === 1) {
            titleText.textContent = 'Cetak & TTD';
            icon.className = 'fas fa-file-pdf';
        } else if(step === 2) {
            titleText.textContent = 'Upload Berkas';
            icon.className = 'fas fa-upload';
        } else if(step === 3) {
            titleText.textContent = 'Kirim Email';
            icon.className = 'fas fa-paper-plane';
        }
    }

    function submitSkMagang() {
        const id = document.getElementById('peserta_id').value;
        const fileInput = document.getElementById('surat_ttd');
        const pesan = document.getElementById('pesan_email').value;

        if (!fileInput.files.length) {
            alert('Silakan pilih file PDF surat yang sudah ditandatangani.');
            return;
        }

        const formData = new FormData();
        formData.append('surat_ttd', fileInput.files[0]);
        formData.append('pesan_email', pesan);
        // Laravel CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const btn = document.getElementById('btnSubmitSk');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
        btn.disabled = true;

        fetch(`/admin/dokumen/sk-magang/${id}/upload-kirim`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Gagal: ' + data.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan pada server.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    // ==========================================
    // EVALUASI MAGANG FUNCTIONS
    // ==========================================

    function openEvaluasiModal(id) {
        document.getElementById('evaluasi_peserta_id').value = id;
        document.getElementById('evaluasiModal').classList.remove('hidden');
        
        // Reset download status
        document.getElementById('btnDownloadEvaluasi').dataset.downloaded = 'false';
        
        // Reset form inputs
        document.getElementById('formEvaluasiData').reset();
        
        // Load default preview
        reloadEvaluasiPreview(id);

        // Reset Steps
        nextEvStep(1);
        document.getElementById('formUploadEvaluasi').reset();
    }

    function closeEvaluasiModal() {
        document.getElementById('evaluasiModal').classList.add('hidden');
        document.getElementById('evaluasiPreviewFrame').src = '';
    }

    function nextEvStep(step) {
        // VALIDASI EVALUASI
        if (step === 2) {
            const evNoSurat = document.getElementById('ev_nomor_surat').value.trim();
            const evKepada = document.getElementById('ev_kepada_yth').value.trim();
            if (!evNoSurat || !evKepada) {
                alert('Silakan lengkapi Nomor Surat dan Jabatan Tujuan terlebih dahulu.');
                return;
            }
            // Validasi nilai
            for(let i=1; i<=10; i++) {
                const nilai = document.getElementById('ev_nilai_' + i).value.trim();
                if(!nilai) {
                    alert('Silakan lengkapi semua nilai (1-10) terlebih dahulu.');
                    return;
                }
            }
            if (document.getElementById('btnDownloadEvaluasi').dataset.downloaded !== 'true') {
                alert('Silakan Download PDF terlebih dahulu sebelum lanjut ke tahap Upload.');
                return;
            }
        } else if (step === 3) {
            const evUpload = document.getElementById('ev_surat_ttd');
            if (!evUpload.files || evUpload.files.length === 0) {
                alert('Silakan upload file Lembar Evaluasi yang sudah di-TTD terlebih dahulu.');
                return;
            }
        }

        // Update Content Visibility
        document.querySelectorAll('#evaluasiModal .step-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('ev-step-' + step + '-content').classList.remove('hidden');

        // Update Dynamic Header
        document.getElementById('ev-step-text').textContent = 'Langkah ' + step + ' dari 3';
        const titleText = document.getElementById('ev-step-title-text');
        const icon = document.getElementById('ev-step-icon');
        if(step === 1) {
            titleText.textContent = 'Form & Preview';
            icon.className = 'fas fa-file-pdf';
        } else if(step === 2) {
            titleText.textContent = 'Upload TTD';
            icon.className = 'fas fa-upload';
        } else if(step === 3) {
            titleText.textContent = 'Kirim Email';
            icon.className = 'fas fa-paper-plane';
        }
    }

    function simpanDraftEvaluasi() {
        const id = document.getElementById('evaluasi_peserta_id').value;
        const form = document.getElementById('formEvaluasiData');
        const formData = new FormData(form);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const btn = document.getElementById('btnSimpanEvaluasi');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        btn.disabled = true;

        fetch(`/admin/dokumen/evaluasi/${id}/simpan-draft`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                reloadEvaluasiPreview(id);
            } else {
                alert('Gagal menyimpan draft: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat menyimpan draft.');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    function reloadEvaluasiPreview(forceId = null) {
        const id = forceId || document.getElementById('evaluasi_peserta_id').value;
        
        // Cache buster to force iframe reload
        const cb = new Date().getTime();
        
        // Load Iframe
        document.getElementById('evaluasiPreviewFrame').src = `/admin/dokumen/evaluasi/${id}/preview?cb=${cb}`;
        
        // Set Download Link
        document.getElementById('btnDownloadEvaluasi').href = `/admin/dokumen/evaluasi/${id}/download?cb=${cb}`;
    }

    function submitEvaluasi() {
        const id = document.getElementById('evaluasi_peserta_id').value;
        const fileInput = document.getElementById('ev_surat_ttd');
        const pesan = document.getElementById('ev_pesan_email').value;

        if (!fileInput.files.length) {
            alert('Silakan pilih file PDF evaluasi yang sudah ditandatangani.');
            return;
        }

        const formData = new FormData();
        formData.append('surat_ttd', fileInput.files[0]);
        formData.append('pesan_email', pesan);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const btn = document.getElementById('btnSubmitEvaluasi');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
        btn.disabled = true;

        fetch(`/admin/dokumen/evaluasi/${id}/upload-kirim`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Gagal: ' + data.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan pada server.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    // ==========================================
    // SERTIFIKAT MAGANG FUNCTIONS
    // ==========================================

    function openSertifikatModal(id) {
        document.getElementById('sertifikat_peserta_id').value = id;
        document.getElementById('sertifikatModal').classList.remove('hidden');
        
        // Reset download status
        document.getElementById('btnDownloadSertifikat').dataset.downloaded = 'false';
        
        document.getElementById('formSertifikatData').reset();
        document.getElementById('formUploadSertifikat').reset();
        nextSertStep(1);
        reloadSertifikatPreview(id);
    }

    function closeSertifikatModal() {
        document.getElementById('sertifikatModal').classList.add('hidden');
        document.getElementById('sertifikatPreviewFrame').src = '';
    }

    function nextSertStep(step) {
        // VALIDASI SERTIFIKAT
        if (step === 2) {
            const sertNomor = document.getElementById('sert_nomor').value.trim();
            const sertPredikat = document.getElementById('sert_predikat').value;
            if (!sertNomor || !sertPredikat) {
                alert('Silakan lengkapi Nomor Sertifikat dan Predikat terlebih dahulu.');
                return;
            }
            if (document.getElementById('btnDownloadSertifikat').dataset.downloaded !== 'true') {
                alert('Silakan Download PDF terlebih dahulu sebelum lanjut ke tahap Upload.');
                return;
            }
        } else if (step === 3) {
            const sertUpload = document.getElementById('sert_ttd');
            if (!sertUpload.files || sertUpload.files.length === 0) {
                alert('Silakan upload file Sertifikat yang sudah di-TTE terlebih dahulu.');
                return;
            }
        }

        document.querySelectorAll('#sertifikatModal .step-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('sert-step-' + step + '-content').classList.remove('hidden');

        // Update Dynamic Header
        document.getElementById('sert-step-text').textContent = 'Langkah ' + step + ' dari 3';
        const titleText = document.getElementById('sert-step-title-text');
        const icon = document.getElementById('sert-step-icon');
        if(step === 1) {
            titleText.textContent = 'Form & Preview';
            icon.className = 'fas fa-file-pdf';
        } else if(step === 2) {
            titleText.textContent = 'Upload TTE';
            icon.className = 'fas fa-upload';
        } else if(step === 3) {
            titleText.textContent = 'Kirim Email';
            icon.className = 'fas fa-paper-plane';
        }
    }

    function reloadSertifikatPreview(forceId = null) {
        const id = forceId || document.getElementById('sertifikat_peserta_id').value;
        const cb = new Date().getTime();
        document.getElementById('sertifikatPreviewFrame').src = `/admin/dokumen/sertifikat/${id}/preview?cb=${cb}`;
        document.getElementById('btnDownloadSertifikat').href = `/admin/dokumen/sertifikat/${id}/download?cb=${cb}`;
    }

    function simpanDraftSertifikat() {
        const id = document.getElementById('sertifikat_peserta_id').value;
        const form = document.getElementById('formSertifikatData');
        const formData = new FormData(form);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const btn = document.getElementById('btnSimpanSertifikat');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        btn.disabled = true;

        fetch(`/admin/dokumen/sertifikat/${id}/simpan-draft`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                reloadSertifikatPreview(id);
            } else {
                alert('Gagal menyimpan draft: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat menyimpan draft.');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    function submitSertifikat() {
        const id = document.getElementById('sertifikat_peserta_id').value;
        const fileInput = document.getElementById('sert_ttd');
        const pesan = document.getElementById('sert_pesan_email').value;

        if (!fileInput.files.length) {
            alert('Silakan pilih file PDF sertifikat yang sudah ber-TTE.');
            return;
        }

        const formData = new FormData();
        formData.append('surat_ttd', fileInput.files[0]);
        formData.append('pesan_email', pesan);
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        const btn = document.getElementById('btnSubmitSertifikat');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
        btn.disabled = true;

        fetch(`/admin/dokumen/sertifikat/${id}/upload-kirim`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Gagal: ' + data.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan pada server.');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    // ==========================================
    // UPLOAD UI FUNCTIONS (DRAG & DROP)
    // ==========================================

    function handleFileSelect(input, idPrefix) {
        if(input.files && input.files[0]) {
            showFilePreview(input.files[0], idPrefix);
        }
    }

    function handleDrop(e, idPrefix) {
        e.preventDefault();
        const dropzone = document.getElementById('upload-dropzone-' + idPrefix);
        dropzone.style.borderColor = 'var(--border)';
        dropzone.style.background = '#fafafa';
        
        if(e.dataTransfer.files && e.dataTransfer.files[0]) {
            const fileInput = document.getElementById(idPrefix);
            fileInput.files = e.dataTransfer.files; // Set the file to input
            showFilePreview(e.dataTransfer.files[0], idPrefix);
        }
    }

    function showFilePreview(f, idPrefix) {
        if(f.size > 5 * 1024 * 1024) { 
            alert('Ukuran file melebihi 5MB.'); 
            clearFile(idPrefix);
            return; 
        }
        document.getElementById('upload-file-name-' + idPrefix).textContent = f.name;
        document.getElementById('upload-file-size-' + idPrefix).textContent = (f.size / 1024).toFixed(1) + ' KB';
        document.getElementById('upload-file-preview-' + idPrefix).style.display = 'flex';
        document.getElementById('upload-dropzone-' + idPrefix).style.display = 'none';
    }

    function clearFile(idPrefix) {
        document.getElementById(idPrefix).value = '';
        document.getElementById('upload-file-preview-' + idPrefix).style.display = 'none';
        document.getElementById('upload-dropzone-' + idPrefix).style.display = 'block';
    }
</script>
@endpush

