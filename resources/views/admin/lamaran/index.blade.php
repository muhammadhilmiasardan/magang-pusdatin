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

                    {{-- Aksi Cepat --}}
                    <h5 style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                        Keputusan Review
                    </h5>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <form id="lm-form-terima" action="" method="POST">
                            @csrf
                            <button type="submit" class="btn-success-custom" style="width: 100%; justify-content: center;"
                                    onclick="return confirm('Terima lamaran ini? Status akan menjadi Belum Aktif.')">
                                <i class="fas fa-check-circle"></i> Terima Lamaran
                            </button>
                        </form>
                        <form id="lm-form-tolak" action="" method="POST">
                            @csrf
                            <button type="submit" class="btn-danger-custom" style="width: 100%; justify-content: center;"
                                    onclick="return confirm('Tolak lamaran ini?')">
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
function closeLamaranModal() {
    document.getElementById('lamaranOverlay').style.display = 'none';
}

document.getElementById('lamaranOverlay').addEventListener('click', function(e) {
    if (e.target === this) closeLamaranModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLamaranModal();
});

$(document).ready(function() {
    $('.view-detail-lamaran').click(function(e) {
        e.preventDefault();
        const id = $(this).data('id');

        $('#lamaranLoadingSpinner').show();
        $('#lamaranModalContent').hide();
        document.getElementById('lamaranOverlay').style.display = 'block';

        $.get(`/admin/lamaran/${id}`, function(data) {
            // Nama & NIM
            $('#lm-nama').text(data.nama);
            $('#lm-nim').text((data.nim_nis ? 'NIM/NIS: ' + data.nim_nis : ''));

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

            // Tim
            $('#lm-tim-1').text(data.tim_kerja1 ? data.tim_kerja1.nama_tim : '-');
            $('#lm-tim-2').text(data.tim_kerja2 ? data.tim_kerja2.nama_tim : '-');

            // Dokumen
            if (data.surat_rekomendasi) {
                $('#lm-btn-rekom').attr('href', '/storage/' + data.surat_rekomendasi).show();
            } else {
                $('#lm-btn-rekom').hide();
            }
            if (data.cv) {
                $('#lm-btn-cv').attr('href', '/storage/' + data.cv).show();
            } else {
                $('#lm-btn-cv').hide();
            }

            // Form actions
            $('#lm-form-terima').attr('action', `/admin/lamaran/${data.id}/terima`);
            $('#lm-form-tolak').attr('action', `/admin/lamaran/${data.id}/tolak`);

            $('#lamaranLoadingSpinner').hide();
            $('#lamaranModalContent').show();
        });
    });
});
</script>
@endpush
