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
        <div class="modal-header">
            <h3 class="modal-title">Proses Surat Keterangan Magang</h3>
            <button class="modal-close" onclick="closeSkModal()"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="modal-body" style="padding: 24px;">
            {{-- Stepper UI --}}
            <div class="stepper-container">
                <div class="step active" id="step-1-indicator">
                    <div class="step-icon"><i class="fas fa-file-pdf"></i></div>
                    <div class="step-text">1. Cetak & TTD</div>
                </div>
                <div class="step-divider"></div>
                <div class="step" id="step-2-indicator">
                    <div class="step-icon"><i class="fas fa-upload"></i></div>
                    <div class="step-text">2. Upload Berkas</div>
                </div>
                <div class="step-divider"></div>
                <div class="step" id="step-3-indicator">
                    <div class="step-icon"><i class="fas fa-paper-plane"></i></div>
                    <div class="step-text">3. Kirim Email</div>
                </div>
            </div>

            {{-- STEP 1: CETAK & PREVIEW --}}
            <div id="step-1-content" class="step-content">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">Nomor Surat</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" id="nomor_surat" placeholder="B.1234/5678" class="form-input" style="flex: 1;" value="B.1234/5678">
                        <button class="btn-secondary-custom" onclick="reloadPreview()" type="button">
                            <i class="fas fa-sync"></i> Refresh Preview
                        </button>
                    </div>
                </div>

                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                    <h4 style="margin: 0 0 10px 0; font-size: 14px; color: #334155;">Preview Surat Keterangan</h4>
                    <iframe id="skPreviewFrame" src="" style="width: 100%; height: 400px; border: 1px solid #cbd5e1; border-radius: 6px; background: #e2e8f0;"></iframe>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <a href="#" id="btnDownloadSk" class="btn-success-custom" style="text-decoration: none; display: inline-block;">
                        <i class="fas fa-download"></i> Download PDF untuk di-TTD
                    </a>
                    <button class="btn-primary-custom" onclick="nextStep(2)">
                        Lanjut ke Upload <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- STEP 2: UPLOAD --}}
            <div id="step-2-content" class="step-content hidden">
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                    <p style="margin: 0 0 10px 0; font-size: 13px; color: #1e3a8a;">
                        <i class="fas fa-info-circle"></i> Silakan upload Surat Keterangan Magang yang sudah ditandatangani basah beserta lampirannya (jika ada) dalam 1 file PDF.
                    </p>
                    <form id="formUploadSk">
                        <input type="hidden" id="peserta_id" name="peserta_id">
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">File Surat TTD + Lampiran (PDF)</label>
                            <input type="file" id="surat_ttd" name="surat_ttd" accept=".pdf" class="form-input" required>
                        </div>
                    </form>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <button class="btn-secondary-custom" onclick="nextStep(1)">
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
                    <button class="btn-secondary-custom" onclick="nextStep(2)">
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
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-file-signature" style="color: var(--primary); margin-right: 8px;"></i> Proses Surat Evaluasi Magang</h3>
            <button class="modal-close" onclick="closeEvaluasiModal()"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="modal-body" style="padding: 24px;">
            <input type="hidden" id="evaluasi_peserta_id" value="">
            
            <div class="stepper-container">
                <div class="step active" id="ev-step-1-indicator">
                    <div class="step-icon"><i class="fas fa-file-pdf"></i></div>
                    <div class="step-text">1. Form & Preview</div>
                </div>
                <div class="step-divider"></div>
                <div class="step" id="ev-step-2-indicator">
                    <div class="step-icon"><i class="fas fa-upload"></i></div>
                    <div class="step-text">2. Upload TTD</div>
                </div>
                <div class="step-divider"></div>
                <div class="step" id="ev-step-3-indicator">
                    <div class="step-icon"><i class="fas fa-paper-plane"></i></div>
                    <div class="step-text">3. Kirim Email</div>
                </div>
            </div>

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
                                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 4px;">Kepada Yth</label>
                                <textarea id="ev_kepada_yth" name="kepada_yth" class="form-input" rows="3" style="padding: 6px 10px; font-size: 13px;" placeholder="Bapak/Ibu ...&#10;Universitas/Sekolah ..."></textarea>
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
                            <button type="button" class="btn-secondary-custom" style="width: 100%; margin-top: 15px;" onclick="simpanDraftEvaluasi()" id="btnSimpanEvaluasi">
                                <i class="fas fa-save"></i> Simpan & Perbarui Preview
                            </button>
                        </form>
                    </div>

                    {{-- Preview Kanan --}}
                    <div style="flex: 2; min-width: 400px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 14px; color: #334155;">Preview Lembar Evaluasi</h4>
                        <iframe id="evaluasiPreviewFrame" src="" style="width: 100%; height: 500px; border: 1px solid #cbd5e1; border-radius: 6px; background: #e2e8f0;"></iframe>
                    </div>

                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                    <a href="#" id="btnDownloadEvaluasi" class="btn-success-custom" style="text-decoration: none; display: inline-block;">
                        <i class="fas fa-download"></i> Download PDF untuk di-TTD
                    </a>
                    <button class="btn-primary-custom" onclick="nextEvStep(2)">
                        Lanjut ke Upload <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- STEP 2: UPLOAD TTD --}}
            <div id="ev-step-2-content" class="step-content hidden">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                    <h4 style="margin: 0 0 15px 0; font-size: 14px; color: #334155;">Upload Evaluasi Bertanda Tangan & Cap</h4>
                    <form id="formUploadEvaluasi" enctype="multipart/form-data">
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">File Scan PDF (Maks. 5MB)</label>
                        <input type="file" id="ev_surat_ttd" class="form-input" accept=".pdf">
                    </form>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <button class="btn-secondary-custom" onclick="nextEvStep(1)">
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
                    <button class="btn-secondary-custom" onclick="nextEvStep(2)">
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
        <div class="modal-header">
            <h3 class="modal-title"><i class="fas fa-certificate" style="color: var(--primary); margin-right: 8px;"></i> Proses Sertifikat Magang</h3>
            <button class="modal-close" onclick="closeSertifikatModal()"><i class="fas fa-times"></i></button>
        </div>

        <div class="modal-body" style="padding: 24px;">
            <input type="hidden" id="sertifikat_peserta_id" value="">

            {{-- Stepper --}}
            <div class="stepper-container">
                <div class="step active" id="sert-step-1-indicator">
                    <div class="step-icon"><i class="fas fa-file-pdf"></i></div>
                    <div class="step-text">1. Form & Preview</div>
                </div>
                <div class="step-divider"></div>
                <div class="step" id="sert-step-2-indicator">
                    <div class="step-icon"><i class="fas fa-upload"></i></div>
                    <div class="step-text">2. Upload TTE</div>
                </div>
                <div class="step-divider"></div>
                <div class="step" id="sert-step-3-indicator">
                    <div class="step-icon"><i class="fas fa-paper-plane"></i></div>
                    <div class="step-text">3. Kirim Email</div>
                </div>
            </div>

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
                            <button type="button" class="btn-secondary-custom" style="width: 100%;" onclick="simpanDraftSertifikat()" id="btnSimpanSertifikat">
                                <i class="fas fa-sync"></i> Simpan & Perbarui Preview
                            </button>
                        </form>
                    </div>

                    {{-- Preview Kanan (landscape iframe) --}}
                    <div style="flex: 2.5; min-width: 420px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px;">
                        <h4 style="margin: 0 0 10px 0; font-size: 14px; color: #334155;">Preview Sertifikat</h4>
                        {{-- Rasio A4 landscape: 29.7/21 = 1.4143. padding-top trick = (21/29.7)*100 = 70.7% --}}
                        <div id="sertifikatIframeWrapper" style="
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
                                    width: 100%;
                                    height: 100%;
                                    border: none;
                                    border-radius: 6px;
                                "
                            ></iframe>
                        </div>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                    <a href="#" id="btnDownloadSertifikat" class="btn-success-custom" style="text-decoration: none; display: inline-block;">
                        <i class="fas fa-download"></i> Download PDF untuk di-TTE
                    </a>
                    <button class="btn-primary-custom" onclick="nextSertStep(2)">
                        Lanjut ke Upload <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            {{-- STEP 2: UPLOAD TTE --}}
            <div id="sert-step-2-content" class="step-content hidden">
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
                    <p style="margin: 0 0 12px 0; font-size: 13px; color: #1e3a8a;">
                        <i class="fas fa-info-circle"></i>
                        Silakan minta Tanda Tangan Elektronik (TTE) Kepala PUSDATIN pada draft PDF,
                        kemudian upload file yang sudah ber-TTE di bawah ini.
                    </p>
                    <form id="formUploadSertifikat" enctype="multipart/form-data">
                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px;">File Sertifikat ber-TTE (PDF, maks. 5MB)</label>
                        <input type="file" id="sert_ttd" class="form-input" accept=".pdf">
                    </form>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <button class="btn-secondary-custom" onclick="nextSertStep(1)">
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
                    <button class="btn-secondary-custom" onclick="nextSertStep(2)">
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
        max-height: 90vh; overflow-y: auto;
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

    // Modal SK Magang Functions
    function openSkModal(id) {
        document.getElementById('peserta_id').value = id;
        document.getElementById('skModal').classList.remove('hidden');
        
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
        // Update Content Visibility
        document.querySelectorAll('.step-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('step-' + step + '-content').classList.remove('hidden');

        // Update Indicators
        for(let i=1; i<=3; i++) {
            let ind = document.getElementById('step-' + i + '-indicator');
            if(i < step) {
                ind.className = 'step completed';
            } else if (i === step) {
                ind.className = 'step active';
            } else {
                ind.className = 'step';
            }
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
        // Update Content Visibility
        document.querySelectorAll('#evaluasiModal .step-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('ev-step-' + step + '-content').classList.remove('hidden');

        // Update Indicators
        for(let i=1; i<=3; i++) {
            let ind = document.getElementById('ev-step-' + i + '-indicator');
            if(i < step) {
                ind.className = 'step completed';
            } else if (i === step) {
                ind.className = 'step active';
            } else {
                ind.className = 'step';
            }
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
        document.querySelectorAll('#sertifikatModal .step-content').forEach(c => c.classList.add('hidden'));
        document.getElementById('sert-step-' + step + '-content').classList.remove('hidden');

        for (let i = 1; i <= 3; i++) {
            let ind = document.getElementById('sert-step-' + i + '-indicator');
            if (i < step) {
                ind.className = 'step completed';
            } else if (i === step) {
                ind.className = 'step active';
            } else {
                ind.className = 'step';
            }
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
</script>
@endpush

