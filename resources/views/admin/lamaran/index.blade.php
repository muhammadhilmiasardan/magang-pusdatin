@extends('layouts.admin')

@section('title', 'Lamaran Masuk')

@section('content')
<div class="card-clean">
    <div class="card-header-clean">
        <h3><i class="fas fa-inbox" style="color: var(--accent); margin-right: 8px;"></i>Menunggu Review</h3>
        <span class="badge-status badge-review">{{ $lamaran->count() }} Lamaran</span>
    </div>

    @if($lamaran->count() == 0)
        <div class="empty-state">
            <i class="fas fa-check-circle" style="display: block;"></i>
            <p>Tidak ada lamaran baru yang menunggu review.</p>
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="table-clean">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pelamar</th>
                        <th>Institusi</th>
                        <th>Periode</th>
                        <th>Pilihan Tim Kerja</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lamaran as $index => $item)
                    <tr>
                        <td style="color: var(--text-muted); font-weight: 500;">{{ $index + 1 }}</td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-primary);">{{ $item->nama }}</div>
                            <div style="font-size: 12px; color: var(--text-secondary); margin-top: 2px;">
                                {{ $item->email }} · {{ $item->nomor_telp }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 500;">{{ $item->nama_institusi }}</div>
                            <div style="font-size: 12px; color: var(--text-secondary);">{{ $item->jurusan }} ({{ $item->tingkat_pendidikan }})</div>
                        </td>
                        <td style="white-space: nowrap;">
                            <div style="font-size: 12.5px;">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</div>
                            <div style="font-size: 12.5px; color: var(--text-secondary);">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</div>
                        </td>
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span class="badge-status badge-selesai" style="font-size: 10.5px; width: fit-content;">
                                    <i class="fas fa-1" style="font-size: 8px;"></i> {{ $item->timKerja1->nama_tim ?? '-' }}
                                </span>
                                <span class="badge-status badge-pending" style="font-size: 10.5px; width: fit-content;">
                                    <i class="fas fa-2" style="font-size: 8px;"></i> {{ $item->timKerja2->nama_tim ?? '-' }}
                                </span>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <button class="btn-outline-custom btn-sm-custom view-detail-lamaran"
                                    data-id="{{ $item->id }}"
                                    style="cursor: pointer;">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

{{-- ═══ MODAL DETAIL LAMARAN ═══ --}}
<div id="lamaranOverlay" style="
    display: none;
    position: fixed; inset: 0; z-index: 1000;
    background: rgba(15, 29, 61, 0.5);
    backdrop-filter: blur(4px);
    animation: fadeIn 0.2s ease;
">
    <div id="lamaranModal" style="
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        border-radius: 16px;
        width: 90%;
        max-width: 860px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        animation: scaleIn 0.2s ease;
    ">
        {{-- Header --}}
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0;">
                <i class="fas fa-user-clock" style="color: var(--accent); margin-right: 8px;"></i>
                Detail Lamaran Masuk
            </h3>
            <button onclick="closeLamaranModal()" style="
                background: none; border: none; cursor: pointer;
                width: 32px; height: 32px; border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                color: var(--text-secondary); font-size: 16px; transition: all 150ms ease;
            " onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Loading --}}
        <div id="lamaranLoadingSpinner" style="text-align: center; padding: 60px;">
            <div style="
                width: 36px; height: 36px; margin: 0 auto 14px;
                border: 3px solid var(--border); border-top-color: var(--primary);
                border-radius: 50%; animation: spin 0.8s linear infinite;
            "></div>
            <p style="color: var(--text-secondary); font-size: 13px;">Memuat data...</p>
        </div>

        {{-- Body --}}
        <div id="lamaranModalContent" style="display: none; padding: 24px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">

                {{-- Kiri: Biodata --}}
                <div>
                    {{-- Profile with Photo --}}
                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 24px;">
                        <div id="lm-foto-container" style="flex-shrink: 0;">
                            <img id="lm-foto" src="" alt="Pas Foto"
                                 style="width: 72px; height: 72px; border-radius: 14px; object-fit: cover; border: 2px solid var(--border); display: none;">
                            <div id="lm-foto-placeholder" style="
                                width: 72px; height: 72px; border-radius: 14px;
                                background: linear-gradient(135deg, var(--primary), var(--primary-light));
                                display: flex; align-items: center; justify-content: center;
                                color: #fff; font-size: 26px;
                            ">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div>
                            <h4 id="lm-nama" style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px;">-</h4>
                            <span id="lm-nim" style="font-size: 12px; color: var(--text-secondary); display: block; margin-bottom: 6px;">-</span>
                            <span class="badge-status badge-review"><i class="fas fa-hourglass-half"></i> Menunggu Review</span>
                        </div>
                    </div>

                    {{-- Kontak & Pendidikan --}}
                    <div style="margin-bottom: 24px;">
                        <h5 style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                            Informasi Kontak & Pendidikan
                        </h5>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 110px; flex-shrink: 0; font-size: 12.5px; color: var(--text-secondary);">Email</span>
                                <span id="lm-email" style="font-size: 13px; font-weight: 500;">-</span>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 110px; flex-shrink: 0; font-size: 12.5px; color: var(--text-secondary);">No. WhatsApp</span>
                                <span id="lm-telp" style="font-size: 13px; font-weight: 500;">-</span>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 110px; flex-shrink: 0; font-size: 12.5px; color: var(--text-secondary);">Institusi</span>
                                <span id="lm-institusi" style="font-size: 13px; font-weight: 500;">-</span>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 110px; flex-shrink: 0; font-size: 12.5px; color: var(--text-secondary);">Jurusan</span>
                                <span id="lm-jurusan" style="font-size: 13px; font-weight: 500;">-</span>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 110px; flex-shrink: 0; font-size: 12.5px; color: var(--text-secondary);">Email Institusi</span>
                                <span id="lm-email-institusi" style="font-size: 13px; font-weight: 500;">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Periode --}}
                    <h5 style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                        Periode Pelaksanaan
                    </h5>
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="background: var(--primary-lighter); padding: 10px 16px; border-radius: 8px; flex: 1; text-align: center;">
                            <div style="font-size: 10px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">Mulai</div>
                            <div id="lm-mulai" style="font-size: 13px; font-weight: 600; color: var(--primary);">-</div>
                        </div>
                        <i class="fas fa-arrow-right" style="color: var(--text-muted); font-size: 12px;"></i>
                        <div style="background: #fffbeb; padding: 10px 16px; border-radius: 8px; flex: 1; text-align: center;">
                            <div style="font-size: 10px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">Selesai</div>
                            <div id="lm-selesai" style="font-size: 13px; font-weight: 600; color: var(--accent-dark);">-</div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Penempatan & Dokumen --}}
                <div>
                    {{-- Penempatan --}}
                    <div style="margin-bottom: 24px;">
                        <h5 style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                            Pilihan Penempatan
                        </h5>
                        <div style="background: #f8fafc; border-radius: 10px; padding: 16px; border: 1px solid var(--border);">
                            <div style="margin-bottom: 12px;">
                                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 3px;">Pilihan 1 (Utama)</div>
                                <div id="lm-tim-1" style="font-size: 13px; font-weight: 600; color: var(--primary);">-</div>
                            </div>
                            <div>
                                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 3px;">Pilihan 2</div>
                                <div id="lm-tim-2" style="font-size: 13px; font-weight: 500; color: var(--text-secondary);">-</div>
                            </div>
                        </div>
                    </div>

                    {{-- Dokumen --}}
                    <div style="margin-bottom: 24px;">
                        <h5 style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                            Dokumen Pendaftaran
                        </h5>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <a href="#" id="lm-btn-rekom" target="_blank"
                               class="btn-primary-custom"
                               style="justify-content: center; text-decoration: none;">
                                <i class="fas fa-file-pdf"></i> Buka Surat Permohonan
                            </a>
                            <a href="#" id="lm-btn-cv" target="_blank"
                               class="btn-outline-custom"
                               style="justify-content: center; text-decoration: none; display: none;">
                                <i class="fas fa-file-pdf"></i> Buka CV
                            </a>
                            <a href="#" id="lm-btn-foto" target="_blank"
                               class="btn-outline-custom"
                               style="justify-content: center; text-decoration: none; display: none;">
                                <i class="fas fa-camera"></i> Lihat & Unduh Pas Foto
                            </a>
                        </div>
                    </div>

                    {{-- Keputusan Review --}}
                    <div style="margin-bottom: 0;">
                        <h5 style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                            Keputusan Review
                        </h5>

                        {{-- PENEMPATAN --}}
                        <div style="margin-bottom: 14px;">
                            <label style="font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.04em; display: block; margin-bottom: 8px;">Penempatan Resmi</label>
                            <div id="lm-penempatan-options" style="display: flex; flex-direction: column; gap: 8px;">
                                {{-- diisi oleh JS --}}
                            </div>
                        </div>

                        {{-- TOGGLE FORM SURAT --}}
                        <button type="button" id="lm-btn-buka-surat"
                            onclick="document.getElementById('lm-form-surat-wrapper').style.display = document.getElementById('lm-form-surat-wrapper').style.display === 'none' ? 'block' : 'none'; this.innerHTML = this.innerHTML.includes('Buka') ? '<i class=\'fas fa-chevron-up\'></i> Sembunyikan Form Surat' : '<i class=\'fas fa-file-alt\'></i> Buka Form Surat Penerimaan';"
                            class="btn-outline-custom" style="width: 100%; justify-content: center; margin-bottom: 8px;">
                            <i class="fas fa-file-alt"></i> Buka Form Surat Penerimaan
                        </button>

                        {{-- FORM SURAT (tersembunyi by default) --}}
                        <div id="lm-form-surat-wrapper" style="display: none; border: 1px solid var(--border); border-radius: 10px; padding: 16px; margin-bottom: 12px; background: #fafafa;">
                            <div style="font-size: 12px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 12px;">Data Surat Penerimaan</div>

                            <div style="margin-bottom: 10px;">
                                <label style="font-size: 12px; color: var(--text-secondary); display: block; margin-bottom: 4px;">Nomor Surat <span style="color: #ef4444;">*</span></label>
                                <input type="text" id="lm-nomor-surat" placeholder="Contoh: B.xxx/PUSDATIN/TU.01.02/05/2026"
                                    style="width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 12.5px; font-family: inherit; outline: none;">
                            </div>

                            <div style="margin-bottom: 10px;">
                                <label style="font-size: 12px; color: var(--text-secondary); display: block; margin-bottom: 4px;">Ditujukan Kepada (Yth.) <span style="color: #ef4444;">*</span></label>
                                <input type="text" id="lm-yth" placeholder="Contoh: Dekan Fakultas Teknik Universitas ..."
                                    style="width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 12.5px; font-family: inherit; outline: none;">
                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">Dipakai di: "Yth. ..." dan "Menindaklanjuti surat dari ..."</div>
                            </div>

                            <div style="margin-bottom: 10px;">
                                <label style="font-size: 12px; color: var(--text-secondary); display: block; margin-bottom: 4px;">Nomor Surat Permohonan Universitas/SLTA <span style="color: #ef4444;">*</span></label>
                                <input type="text" id="lm-nomor-surat-univ" placeholder="Contoh: B.123/UN10/T1.5/2026"
                                    style="width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 12.5px; font-family: inherit; outline: none;">
                            </div>

                            <div style="margin-bottom: 12px;">
                                <label style="font-size: 12px; color: var(--text-secondary); display: block; margin-bottom: 4px;">Tanggal Surat Permohonan <span style="color: #ef4444;">*</span></label>
                                <input type="text" id="lm-tanggal-surat-lamaran" placeholder="Contoh: 10 April 2026"
                                    style="width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 6px; font-size: 12.5px; font-family: inherit; outline: none;">
                            </div>

                            {{-- Tombol Preview --}}
                            <button type="button" onclick="previewSurat()"
                                class="btn-primary-custom" style="width: 100%; justify-content: center; margin-bottom: 8px;">
                                <i class="fas fa-eye"></i> Preview Surat Penerimaan
                            </button>

                            {{-- Preview Iframe --}}
                            <div id="lm-preview-surat-container" style="display: none; margin-top: 8px; border: 1px solid var(--border); border-radius: 8px; overflow: hidden;">
                                <div style="background: #f1f5f9; padding: 8px 12px; font-size: 12px; font-weight: 600; color: var(--text-secondary); display: flex; justify-content: space-between; align-items: center;">
                                    <span><i class="fas fa-eye" style="margin-right: 6px;"></i>Preview Surat</span>
                                    <button type="button" onclick="document.getElementById('lm-preview-surat-container').style.display='none'" style="background: none; border: none; cursor: pointer; color: var(--text-muted); font-size: 14px;"><i class="fas fa-times"></i></button>
                                </div>
                                <iframe id="lm-surat-iframe" src="" style="width: 100%; height: 480px; border: none; display: block;"></iframe>
                            </div>
                        </div>

                        {{-- AKSI FINAL --}}
                        <div style="display: flex; gap: 8px;">
                            {{-- Terima & Simpan Penempatan --}}
                            <form id="lm-form-terima" action="" method="POST" style="flex: 1;">
                                @csrf
                                <input type="hidden" name="id_tim_kerja_ditempatkan" id="lm-hidden-tim">
                                <button type="submit" class="btn-success-custom" style="width: 100%; justify-content: center;"
                                        onclick="return validateAndConfirmTerima()">
                                    <i class="fas fa-check-circle"></i> Terima Lamaran
                                </button>
                            </form>

                            {{-- Download Surat PDF --}}
                            <button type="button" onclick="downloadSurat()" title="Download Surat Penerimaan PDF Saja"
                                class="btn-accent-custom" style="white-space: nowrap;">
                                <i class="fas fa-file-download"></i> Download PDF
                            </button>
                        </div>

                        {{-- Tolak --}}
                        <form id="lm-form-tolak" action="" method="POST" style="margin-top: 8px;">
                            @csrf
                            <button type="submit" class="btn-danger-custom" style="width: 100%; justify-content: center; background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;"
                                    onclick="return confirm('Tolak lamaran ini?')"
                                    onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                                <i class="fas fa-times-circle"></i> Tolak Lamaran
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div style="padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
            <button onclick="closeLamaranModal()" class="btn-outline-custom">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @keyframes fadeIn  { from { opacity: 0; }  to { opacity: 1; } }
    @keyframes scaleIn { from { opacity: 0; transform: translate(-50%,-50%) scale(0.95); } to { opacity: 1; transform: translate(-50%,-50%) scale(1); } }
    @keyframes spin    { to { transform: rotate(360deg); } }
</style>
@endpush

@push('scripts')
<script>
// ── State ──────────────────────────────────────────────
let _currentLamaranId   = null;
let _currentLamaranData = null;

// ── Modal helpers ───────────────────────────────────────
function closeLamaranModal() {
    document.getElementById('lamaranOverlay').style.display = 'none';
    // Reset form surat
    document.getElementById('lm-form-surat-wrapper').style.display = 'none';
    document.getElementById('lm-preview-surat-container').style.display = 'none';
    document.getElementById('lm-btn-buka-surat').innerHTML = '<i class="fas fa-file-alt"></i> Buka Form Surat Penerimaan';
}

document.getElementById('lamaranOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeLamaranModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLamaranModal();
});

// ── Build pilihan penempatan radio buttons ──────────────
function buildPenempatanOptions(data) {
    const container = document.getElementById('lm-penempatan-options');
    container.innerHTML = '';

    const options = [];
    if (data.tim_kerja1) options.push({ id: data.id_tim_kerja_1, nama: data.tim_kerja1.nama_tim, label: 'Pilihan 1 (Utama)' });
    if (data.tim_kerja2) options.push({ id: data.id_tim_kerja_2, nama: data.tim_kerja2.nama_tim, label: 'Pilihan 2' });

    options.forEach((opt, i) => {
        const radioId = `lm-radio-tim-${i}`;
        const div = document.createElement('div');
        div.style.cssText = `
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border-radius: 8px; cursor: pointer;
            border: 2px solid var(--border); transition: all 150ms ease;
            background: #fff;
        `;
        div.innerHTML = `
            <input type="radio" name="lm_penempatan" id="${radioId}" value="${opt.id}"
                   style="accent-color: var(--primary); width: 16px; height: 16px; cursor: pointer;"
                   ${i === 0 ? 'checked' : ''}>
            <label for="${radioId}" style="cursor: pointer; flex: 1; margin: 0;">
                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em;">${opt.label}</div>
                <div style="font-size: 13px; font-weight: 600; color: var(--primary); margin-top: 2px;">${opt.nama}</div>
            </label>
        `;
        // Style selected state
        const radio = div.querySelector('input');
        const updateStyle = () => {
            div.style.borderColor = radio.checked ? 'var(--primary)' : 'var(--border)';
            div.style.background  = radio.checked ? 'var(--primary-lighter)' : '#fff';
        };
        radio.addEventListener('change', () => {
            container.querySelectorAll('div').forEach(d => {
                const r = d.querySelector('input');
                if (r) { d.style.borderColor = 'var(--border)'; d.style.background = '#fff'; }
            });
            updateStyle();
            // Sync hidden input
            document.getElementById('lm-hidden-tim').value = radio.value;
        });
        div.addEventListener('click', () => { radio.checked = true; radio.dispatchEvent(new Event('change')); });
        updateStyle();
        container.appendChild(div);
    });

    // Set initial hidden value
    const first = container.querySelector('input[type=radio]');
    if (first) document.getElementById('lm-hidden-tim').value = first.value;
}

// ── Get selected tim ID ─────────────────────────────────
function getSelectedTimId() {
    const selected = document.querySelector('input[name="lm_penempatan"]:checked');
    return selected ? selected.value : null;
}

// ── Collect form surat data ─────────────────────────────
function getSuratFormData() {
    return {
        id_tim_kerja_ditempatkan: getSelectedTimId(),
        nomor_surat:              document.getElementById('lm-nomor-surat').value.trim(),
        yth:                      document.getElementById('lm-yth').value.trim(),
        nomor_surat_univ:         document.getElementById('lm-nomor-surat-univ').value.trim(),
        tanggal_surat_lamaran:    document.getElementById('lm-tanggal-surat-lamaran').value.trim(),
    };
}

// ── Preview surat via POST into iframe ──────────────────
function previewSurat() {
    const form = getSuratFormData();
    if (!form.nomor_surat || !form.yth || !form.nomor_surat_univ || !form.tanggal_surat_lamaran) {
        alert('Harap lengkapi semua field data surat terlebih dahulu.');
        return;
    }
    if (!form.id_tim_kerja_ditempatkan) {
        alert('Harap pilih penempatan terlebih dahulu.');
        return;
    }

    // Submit to preview endpoint, result loads in iframe via hidden form
    const previewUrl = `/admin/lamaran/${_currentLamaranId}/surat/preview`;
    const hiddenForm = document.createElement('form');
    hiddenForm.method  = 'POST';
    hiddenForm.action  = previewUrl;
    hiddenForm.target  = 'lm-surat-iframe';
    hiddenForm.style.display = 'none';

    const csrfInput = document.createElement('input');
    csrfInput.type  = 'hidden';
    csrfInput.name  = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    hiddenForm.appendChild(csrfInput);

    Object.entries(form).forEach(([key, val]) => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = key;
        input.value = val;
        hiddenForm.appendChild(input);
    });

    document.body.appendChild(hiddenForm);
    hiddenForm.submit();
    document.body.removeChild(hiddenForm);

    document.getElementById('lm-preview-surat-container').style.display = 'block';
}

// ── Download Surat PDF ─────────────────────────────
function downloadSurat() {
    const form = getSuratFormData();
    if (!form.nomor_surat || !form.yth || !form.nomor_surat_univ || !form.tanggal_surat_lamaran) {
        // Buka form surat kalau belum diisi
        document.getElementById('lm-form-surat-wrapper').style.display = 'block';
        alert('Harap lengkapi semua field data surat terlebih dahulu, lalu klik Download PDF.');
        return;
    }
    if (!form.id_tim_kerja_ditempatkan) {
        alert('Harap pilih penempatan terlebih dahulu.');
        return;
    }

    // Submit to download endpoint (auto-downloads PDF)
    const downloadUrl = `/admin/lamaran/${_currentLamaranId}/surat/download`;
    const hiddenForm  = document.createElement('form');
    hiddenForm.method  = 'POST';
    hiddenForm.action  = downloadUrl;
    hiddenForm.style.display = 'none';

    const csrfInput = document.createElement('input');
    csrfInput.type  = 'hidden';
    csrfInput.name  = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    hiddenForm.appendChild(csrfInput);

    Object.entries(form).forEach(([key, val]) => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = key;
        input.value = val;
        hiddenForm.appendChild(input);
    });

    document.body.appendChild(hiddenForm);
    hiddenForm.submit();
    document.body.removeChild(hiddenForm);
}

// ── Validate sebelum submit Terima ──────────────────────
function validateAndConfirmTerima() {
    const timId = document.getElementById('lm-hidden-tim').value;
    if (!timId) {
        alert('Harap pilih penempatan resmi terlebih dahulu.');
        return false;
    }
    return confirm('Terima lamaran ini? Status akan berubah menjadi "Belum Aktif".');
}

// ── Load modal data ─────────────────────────────────────
$(document).ready(function() {
    $('.view-detail-lamaran').click(function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        _currentLamaranId = id;

        $('#lamaranLoadingSpinner').show();
        $('#lamaranModalContent').hide();
        document.getElementById('lamaranOverlay').style.display = 'block';

        $.get(`/admin/lamaran/${id}`, function(data) {
            _currentLamaranData = data;

            // Nama & NIM
            $('#lm-nama').text(data.nama);
            $('#lm-nim').text(data.nim_nis ? 'NIM/NIS: ' + data.nim_nis : '');

            // Foto
            if (data.pas_foto) {
                const fotoUrl = '/storage/' + data.pas_foto;
                $('#lm-foto').attr('src', fotoUrl).show();
                $('#lm-foto-placeholder').hide();
                $('#lm-btn-foto').attr('href', fotoUrl).show();
            } else {
                $('#lm-foto').hide();
                $('#lm-foto-placeholder').show();
                $('#lm-btn-foto').hide();
            }

            // Kontak
            $('#lm-email').text(data.email);
            $('#lm-telp').text(data.nomor_telp);
            $('#lm-institusi').text(data.nama_institusi + ' (' + data.tingkat_pendidikan + ')');
            $('#lm-jurusan').text(data.jurusan);
            $('#lm-email-institusi').text(data.email_institusi);

            // Periode
            const opt = {day: 'numeric', month: 'long', year: 'numeric'};
            $('#lm-mulai').text(new Date(data.tanggal_mulai).toLocaleDateString('id-ID', opt));
            $('#lm-selesai').text(new Date(data.tanggal_selesai).toLocaleDateString('id-ID', opt));

            // Tim pilihan (info saja)
            $('#lm-tim-1').text(data.tim_kerja1 ? data.tim_kerja1.nama_tim : '-');
            $('#lm-tim-2').text(data.tim_kerja2 ? data.tim_kerja2.nama_tim : '-');

            // Dokumen
            if (data.surat_rekomendasi) {
                $('#lm-btn-rekom').attr('href', '/storage/' + data.surat_rekomendasi).show();
            } else { $('#lm-btn-rekom').hide(); }
            if (data.cv) {
                $('#lm-btn-cv').attr('href', '/storage/' + data.cv).show();
            } else { $('#lm-btn-cv').hide(); }

            // Build penempatan radio
            buildPenempatanOptions(data);

            // Set form actions
            $('#lm-form-terima').attr('action', `/admin/lamaran/${data.id}/terima`);
            $('#lm-form-tolak').attr('action',  `/admin/lamaran/${data.id}/tolak`);

            $('#lamaranLoadingSpinner').hide();
            $('#lamaranModalContent').show();
        });
    });
});
</script>
@endpush
