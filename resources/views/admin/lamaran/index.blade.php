@extends('layouts.admin')
@section('title','Lamaran Masuk')
@section('content')

<div class="card-clean">
    <div class="card-header-clean">
        <h3><i class="fas fa-inbox" style="color:var(--accent);margin-right:8px;"></i>Menunggu Review</h3>
        <span class="badge-status badge-review">{{ $lamaran->count() }} Lamaran</span>
    </div>
    @if($lamaran->count()==0)
        <div class="empty-state">
            <i class="fas fa-check-circle" style="display:block;"></i>
            <p>Tidak ada lamaran baru yang menunggu review.</p>
        </div>
    @else
        <div style="overflow-x:auto;">
            <table class="table-clean">
                <thead><tr>
                    <th>No</th><th>Pelamar</th><th>Institusi</th><th>Periode</th><th>Pilihan Tim</th><th style="text-align:center;">Aksi</th>
                </tr></thead>
                <tbody>
                @foreach($lamaran as $i=>$item)
                <tr>
                    <td style="color:var(--text-muted);font-weight:500;">{{ $i+1 }}</td>
                    <td>
                        <div style="font-weight:600;color:var(--text-primary);">{{ $item->nama }}</div>
                        <div style="font-size:12px;color:var(--text-secondary);margin-top:2px;">{{ $item->email }} · {{ $item->nomor_telp }}</div>
                    </td>
                    <td>
                        <div style="font-weight:500;">{{ $item->nama_institusi }}</div>
                        <div style="font-size:12px;color:var(--text-secondary);">{{ $item->jurusan }} ({{ $item->tingkat_pendidikan }})</div>
                    </td>
                    <td style="white-space:nowrap;">
                        <div style="font-size:12.5px;">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</div>
                        <div style="font-size:12.5px;color:var(--text-secondary);">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:4px;">
                            <span class="badge-status badge-selesai" style="font-size:10.5px;width:fit-content;">① {{ $item->timKerja1->nama_tim ?? '-' }}</span>
                            <span class="badge-status badge-pending" style="font-size:10.5px;width:fit-content;">② {{ $item->timKerja2->nama_tim ?? '-' }}</span>
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <button class="btn-primary-custom btn-sm-custom open-review" data-id="{{ $item->id }}" style="cursor:pointer;">
                            <i class="fas fa-search"></i> Review
                        </button>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ═══ MODAL 1: REVIEW DATA ═══ --}}
<div id="reviewOverlay" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(15,29,61,0.6);backdrop-filter:blur(4px);">
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border-radius:16px;width:90%;max-width:860px;max-height:90vh;overflow-y:auto;box-shadow:0 25px 50px -12px rgba(0,0,0,.25);">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Langkah 1 dari 4</div>
                <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);margin:0;"><i class="fas fa-user-clock" style="color:var(--accent);margin-right:8px;"></i>Review Data Pelamar</h3>
            </div>
            <button onclick="closeReviewModal()" style="background:none;border:none;cursor:pointer;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-size:16px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'"><i class="fas fa-times"></i></button>
        </div>
        <div id="reviewLoadingSpinner" style="text-align:center;padding:60px;">
            <div style="width:36px;height:36px;margin:0 auto 14px;border:3px solid var(--border);border-top-color:var(--primary);border-radius:50%;animation:spin .8s linear infinite;"></div>
            <p style="color:var(--text-secondary);font-size:13px;">Memuat data...</p>
        </div>
        <div id="reviewContent" style="display:none;padding:24px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;">
                {{-- Kiri: Biodata --}}
                <div>
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
                        <div style="flex-shrink:0;">
                            <img id="rv-foto" src="" style="width:72px;height:72px;border-radius:14px;object-fit:cover;border:2px solid var(--border);display:none;">
                            <div id="rv-foto-ph" style="width:72px;height:72px;border-radius:14px;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;color:#fff;font-size:26px;"><i class="fas fa-user"></i></div>
                        </div>
                        <div>
                            <h4 id="rv-nama" style="font-size:16px;font-weight:700;color:var(--text-primary);margin:0 0 4px;">-</h4>
                            <span id="rv-nim" style="font-size:12px;color:var(--text-secondary);display:block;margin-bottom:6px;">-</span>
                            <span class="badge-status badge-review"><i class="fas fa-hourglass-half"></i> Menunggu Review</span>
                        </div>
                    </div>
                    <h5 style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border);">Kontak &amp; Pendidikan</h5>
                    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;">
                        <div style="display:flex;gap:12px;"><span style="width:110px;flex-shrink:0;font-size:12px;color:var(--text-secondary);">Email</span><span id="rv-email" style="font-size:13px;font-weight:500;">-</span></div>
                        <div style="display:flex;gap:12px;"><span style="width:110px;flex-shrink:0;font-size:12px;color:var(--text-secondary);">WhatsApp</span><span id="rv-telp" style="font-size:13px;font-weight:500;">-</span></div>
                        <div style="display:flex;gap:12px;"><span style="width:110px;flex-shrink:0;font-size:12px;color:var(--text-secondary);">Institusi</span><span id="rv-institusi" style="font-size:13px;font-weight:500;">-</span></div>
                        <div style="display:flex;gap:12px;"><span style="width:110px;flex-shrink:0;font-size:12px;color:var(--text-secondary);">Jurusan</span><span id="rv-jurusan" style="font-size:13px;font-weight:500;">-</span></div>
                        <div style="display:flex;gap:12px;"><span style="width:110px;flex-shrink:0;font-size:12px;color:var(--text-secondary);">Email Institusi</span><span id="rv-email-inst" style="font-size:13px;font-weight:500;">-</span></div>
                    </div>
                    <h5 style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border);">Periode</h5>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="background:var(--primary-lighter);padding:10px 16px;border-radius:8px;flex:1;text-align:center;"><div style="font-size:10px;color:var(--text-secondary);text-transform:uppercase;margin-bottom:4px;">Mulai</div><div id="rv-mulai" style="font-size:13px;font-weight:600;color:var(--primary);">-</div></div>
                        <i class="fas fa-arrow-right" style="color:var(--text-muted);font-size:12px;"></i>
                        <div style="background:#fffbeb;padding:10px 16px;border-radius:8px;flex:1;text-align:center;"><div style="font-size:10px;color:var(--text-secondary);text-transform:uppercase;margin-bottom:4px;">Selesai</div><div id="rv-selesai" style="font-size:13px;font-weight:600;color:var(--accent-dark);">-</div></div>
                    </div>
                </div>
                {{-- Kanan: Pilihan Tim & Dokumen --}}
                <div>
                    <h5 style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border);">Pilihan Tim Kerja</h5>
                    <div style="background:#f8fafc;border-radius:10px;padding:16px;border:1px solid var(--border);margin-bottom:20px;">
                        <div style="margin-bottom:12px;"><div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;margin-bottom:3px;">Pilihan 1 (Utama)</div><div id="rv-tim1" style="font-size:13px;font-weight:600;color:var(--primary);">-</div></div>
                        <div><div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;margin-bottom:3px;">Pilihan 2</div><div id="rv-tim2" style="font-size:13px;font-weight:500;color:var(--text-secondary);">-</div></div>
                    </div>
                    <h5 style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border);">Dokumen Pendaftaran</h5>
                    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;">
                        <a id="rv-btn-surat" href="#" target="_blank" class="btn-primary-custom" style="justify-content:center;text-decoration:none;display:none;"><i class="fas fa-file-pdf"></i> Buka Surat Permohonan</a>
                        <a id="rv-btn-cv" href="#" target="_blank" class="btn-outline-custom" style="justify-content:center;text-decoration:none;display:none;"><i class="fas fa-file-pdf"></i> Buka CV</a>
                        <a id="rv-btn-foto" href="#" target="_blank" class="btn-outline-custom" style="justify-content:center;text-decoration:none;display:none;"><i class="fas fa-camera"></i> Lihat &amp; Unduh Pas Foto</a>
                    </div>
                    <div style="background:#fef2f2;border-radius:10px;padding:14px;border:1px solid #fecaca;">
                        <div style="font-size:11px;color:#991b1b;font-weight:600;text-transform:uppercase;margin-bottom:8px;">Tolak Lamaran</div>
                        <p style="font-size:12px;color:#7f1d1d;margin:0 0 10px;">Jika lamaran tidak memenuhi syarat, tolak sekarang.</p>
                        <form id="rv-form-tolak" action="" method="POST">@csrf
                            <button type="submit" class="btn-danger-custom" style="width:100%;justify-content:center;background:#fef2f2;color:#dc2626;border:1px solid #fecaca;" onclick="return confirm('Tolak lamaran ini? Tindakan tidak dapat dibatalkan.')" onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                                <i class="fas fa-times-circle"></i> Tolak Lamaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <button onclick="closeReviewModal()" class="btn-outline-custom">Tutup</button>
            <button onclick="openSuratModal()" class="btn-primary-custom"><i class="fas fa-arrow-right"></i> Lanjut: Tentukan Penempatan &amp; Buat Surat</button>
        </div>
    </div>
</div>

{{-- ═══ MODAL 2: GENERATE SURAT ═══ --}}
<div id="suratOverlay" style="display:none;position:fixed;inset:0;z-index:1010;background:rgba(15,29,61,0.6);backdrop-filter:blur(4px);">
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border-radius:16px;width:90%;max-width:760px;max-height:90vh;overflow-y:auto;box-shadow:0 25px 50px -12px rgba(0,0,0,.25);">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Langkah 2 dari 4</div>
                <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);margin:0;"><i class="fas fa-file-alt" style="color:var(--accent);margin-right:8px;"></i>Tentukan Penempatan &amp; Generate Surat</h3>
            </div>
            <button onclick="closeSuratModal()" style="background:none;border:none;cursor:pointer;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-size:16px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'"><i class="fas fa-times"></i></button>
        </div>
        <div style="padding:24px;">
            <h5 style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border);">Penempatan Resmi</h5>
            <div id="lm-penempatan-options" style="display:flex;flex-direction:column;gap:8px;margin-bottom:20px;"></div>
            <input type="hidden" id="lm-hidden-tim">
            <h5 style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:var(--text-secondary);margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border);">Data Surat Penerimaan</h5>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
                <div>
                    <label style="font-size:12px;color:var(--text-secondary);display:block;margin-bottom:4px;">Nomor Surat <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="lm-nomor-surat" placeholder="B.xxx/PUSDATIN/TU.01.02/05/2026" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:12.5px;font-family:inherit;outline:none;box-sizing:border-box;">
                </div>
                <div>
                    <label style="font-size:12px;color:var(--text-secondary);display:block;margin-bottom:4px;">Nomor Surat Permohonan <span style="color:#ef4444;">*</span></label>
                    <input type="text" id="lm-nomor-surat-univ" placeholder="B.123/UN10/T1.5/2026" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:12.5px;font-family:inherit;outline:none;box-sizing:border-box;">
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label style="font-size:12px;color:var(--text-secondary);display:block;margin-bottom:4px;">Ditujukan Kepada (Yth.) <span style="color:#ef4444;">*</span></label>
                <input type="text" id="lm-yth" placeholder="Dekan Fakultas Teknik Universitas ..." style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:12.5px;font-family:inherit;outline:none;box-sizing:border-box;">
                <div style="font-size:11px;color:var(--text-muted);margin-top:3px;">Dipakai di: "Yth. ..." dan "Menindaklanjuti surat dari ..."</div>
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:12px;color:var(--text-secondary);display:block;margin-bottom:4px;">Tanggal Surat Permohonan <span style="color:#ef4444;">*</span></label>
                <input type="text" id="lm-tanggal-surat-lamaran" placeholder="10 April 2026" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:12.5px;font-family:inherit;outline:none;box-sizing:border-box;">
            </div>
            <div style="display:flex;gap:8px;margin-bottom:12px;">
                <button onclick="previewSurat()" class="btn-outline-custom" style="flex:1;justify-content:center;"><i class="fas fa-eye"></i> Preview Surat</button>
                <button onclick="downloadSurat()" class="btn-accent-custom" style="white-space:nowrap;"><i class="fas fa-file-download"></i> Download PDF Draft</button>
            </div>
            <div id="lm-preview-surat-container" style="display:none;border:1px solid var(--border);border-radius:8px;overflow:hidden;">
                <div style="background:#f1f5f9;padding:8px 12px;font-size:12px;font-weight:600;color:var(--text-secondary);display:flex;justify-content:space-between;align-items:center;">
                    <span><i class="fas fa-eye" style="margin-right:6px;"></i>Preview Surat</span>
                    <button onclick="document.getElementById('lm-preview-surat-container').style.display='none'" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:14px;"><i class="fas fa-times"></i></button>
                </div>
                <iframe id="lm-surat-iframe" name="lm-surat-iframe" src="" style="width:100%;height:480px;border:none;display:block;"></iframe>
            </div>
            <div style="background:#eff6ff;border-left:3px solid var(--primary);border-radius:0 8px 8px 0;padding:10px 14px;font-size:12px;color:#1e40af;margin-top:12px;">
                <strong><i class="fas fa-info-circle"></i> Setelah download:</strong> Lakukan TTE dan tambahkan lampiran secara manual, lalu klik "Lanjut Upload Surat".
            </div>
        </div>
        <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:space-between;">
            <button onclick="closeSuratModal();document.getElementById('reviewOverlay').style.display='block';" class="btn-outline-custom"><i class="fas fa-arrow-left"></i> Kembali</button>
            <button onclick="openUploadModal()" class="btn-primary-custom"><i class="fas fa-arrow-right"></i> Lanjut: Upload Surat Final</button>
        </div>
    </div>
</div>

{{-- Modal 3 & 4 --}}
@include('admin.lamaran._modal_upload')
@include('admin.lamaran._modal_email')

@endsection

@push('styles')
<style>
    @keyframes fadeIn{from{opacity:0}to{opacity:1}}
    @keyframes scaleIn{from{opacity:0;transform:translate(-50%,-50%) scale(.95)}to{opacity:1;transform:translate(-50%,-50%) scale(1)}}
    @keyframes spin{to{transform:rotate(360deg)}}
    #reviewOverlay > div,#suratOverlay > div,#uploadOverlay > div,#emailOverlay > div{animation:scaleIn .2s ease}
</style>
@endpush

@push('scripts')
@include('admin.lamaran._scripts')
@endpush
