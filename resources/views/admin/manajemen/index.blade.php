@extends('layouts.admin')

@section('title', 'Manajemen Magang')

@section('content')
@php
    $tabs = [
        'aktif'       => ['label' => 'Aktif',       'data' => $grouped['aktif'],       'icon' => 'fa-user-check'],
        'belum-aktif' => ['label' => 'Belum Aktif',  'data' => $grouped['belum_aktif'], 'icon' => 'fa-hourglass-half'],
        'selesai'     => ['label' => 'Selesai',      'data' => $grouped['selesai'],     'icon' => 'fa-graduation-cap'],
        'anulir'      => ['label' => 'Anulir',       'data' => $grouped['anulir'],      'icon' => 'fa-ban'],
        'ditolak'     => ['label' => 'Ditolak',      'data' => $grouped['ditolak'],     'icon' => 'fa-times-circle'],
    ];
@endphp

<div class="card-clean">
    {{-- Tab Navigation --}}
    <div class="tab-nav-clean" role="tablist">
        @foreach($tabs as $id => $tab)
        <a class="tab-item {{ $loop->first ? 'active' : '' }}"
           href="#tab-{{ $id }}" role="tab"
           data-tab="{{ $id }}"
           onclick="switchTab('{{ $id }}', event)">
            <i class="fas {{ $tab['icon'] }}" style="font-size: 13px;"></i>
            {{ $tab['label'] }}
            <span class="tab-count">{{ $tab['data']->count() }}</span>
        </a>
        @endforeach
    </div>

    {{-- Tab Contents --}}
    @foreach($tabs as $id => $tab)
    <div class="tab-panel {{ $loop->first ? '' : 'hidden' }}" id="tab-{{ $id }}" role="tabpanel">
        @if($tab['data']->count() == 0)
            <div class="empty-state">
                <i class="fas {{ $tab['icon'] }}" style="display: block;"></i>
                <p>Tidak ada data peserta dengan status {{ $tab['label'] }}.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Nama Peserta</th>
                            <th>Institusi</th>
                            <th>Penempatan</th>
                            <th>Tgl Selesai</th>
                            <th style="text-align: center;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tab['data'] as $item)
                        <tr>
                            <td>
                                <a href="#" class="link-name view-detail" data-id="{{ $item->id }}">
                                    {{ $item->nama }}
                                </a>
                            </td>
                            <td>{{ $item->nama_institusi }}</td>
                            <td>
                                <span style="font-size: 12.5px; color: var(--text-secondary);">
                                    {{ $item->timKerja1->nama_tim ?? 'Belum ditentukan' }}
                                </span>
                            </td>
                            <td style="white-space: nowrap; font-size: 13px;">
                                {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td style="text-align: center;">
                                <button class="btn-outline-custom btn-sm-custom view-detail" data-id="{{ $item->id }}" style="cursor: pointer;">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endforeach
</div>

{{-- ═══ MODAL DETAIL PESERTA ═══ --}}
<div id="detailOverlay" style="
    display: none;
    position: fixed; inset: 0; z-index: 1000;
    background: rgba(15, 29, 61, 0.5);
    backdrop-filter: blur(4px);
    animation: fadeIn 0.2s ease;
">
    <div id="detailModal" style="
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
        {{-- Modal Header --}}
        <div style="
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        ">
            <h3 style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0;">
                Detail Peserta Magang
            </h3>
            <button onclick="closeModal()" style="
                background: none; border: none; cursor: pointer;
                width: 32px; height: 32px; border-radius: 8px;
                display: flex; align-items: center; justify-content: center;
                color: var(--text-secondary); font-size: 16px;
                transition: all 150ms ease;
            " onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Loading --}}
        <div id="loadingSpinner" style="text-align: center; padding: 60px;">
            <div style="
                width: 36px; height: 36px; margin: 0 auto 14px;
                border: 3px solid var(--border); border-top-color: var(--primary);
                border-radius: 50%; animation: spin 0.8s linear infinite;
            "></div>
            <p style="color: var(--text-secondary); font-size: 13px;">Memuat data...</p>
        </div>

        {{-- Modal Body --}}
        <div id="modalContent" style="display: none; padding: 24px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                {{-- Left Column: Biodata --}}
                <div>
                    {{-- Profile --}}
                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 24px;">
                        <div style="
                            width: 56px; height: 56px; border-radius: 14px;
                            background: linear-gradient(135deg, var(--primary), var(--primary-light));
                            display: flex; align-items: center; justify-content: center;
                            color: #fff; font-size: 22px; flex-shrink: 0;
                        ">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h4 id="m-nama" style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px;">-</h4>
                            <span id="m-status" class="badge-status badge-aktif">-</span>
                        </div>
                    </div>

                    {{-- Contact & Education --}}
                    <div style="margin-bottom: 24px;">
                        <h5 style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                            Informasi Kontak & Pendidikan
                        </h5>
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 110px; flex-shrink: 0; font-size: 12.5px; color: var(--text-secondary);">Email</span>
                                <span id="m-email" style="font-size: 13px; font-weight: 500;">-</span>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 110px; flex-shrink: 0; font-size: 12.5px; color: var(--text-secondary);">No. WhatsApp</span>
                                <span id="m-telp" style="font-size: 13px; font-weight: 500;">-</span>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 110px; flex-shrink: 0; font-size: 12.5px; color: var(--text-secondary);">Institusi</span>
                                <span id="m-institusi" style="font-size: 13px; font-weight: 500;">-</span>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 110px; flex-shrink: 0; font-size: 12.5px; color: var(--text-secondary);">Jurusan</span>
                                <span id="m-jurusan" style="font-size: 13px; font-weight: 500;">-</span>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <span style="width: 110px; flex-shrink: 0; font-size: 12.5px; color: var(--text-secondary);">Email Institusi</span>
                                <span id="m-email-institusi" style="font-size: 13px; font-weight: 500;">-</span>
                            </div>
                        </div>
                    </div>

                    {{-- Period --}}
                    <h5 style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                        Periode Pelaksanaan
                    </h5>
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div style="
                            background: var(--primary-lighter); padding: 10px 16px; border-radius: 8px;
                            flex: 1; text-align: center;
                        ">
                            <div style="font-size: 10px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">Mulai</div>
                            <div id="m-mulai" style="font-size: 13px; font-weight: 600; color: var(--primary);">-</div>
                        </div>
                        <i class="fas fa-arrow-right" style="color: var(--text-muted); font-size: 12px;"></i>
                        <div style="
                            background: #fffbeb; padding: 10px 16px; border-radius: 8px;
                            flex: 1; text-align: center;
                        ">
                            <div style="font-size: 10px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px;">Selesai</div>
                            <div id="m-selesai" style="font-size: 13px; font-weight: 600; color: var(--accent-dark);">-</div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Berkas & Status --}}
                <div>
                    {{-- Penempatan --}}
                    <div style="margin-bottom: 24px;">
                        <h5 style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                            Penempatan
                        </h5>
                        <div style="background: #f8fafc; border-radius: 10px; padding: 16px; border: 1px solid var(--border);">
                            <div style="margin-bottom: 12px;">
                                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 3px;">Pilihan 1 (Utama)</div>
                                <div id="m-tim-1" style="font-size: 13px; font-weight: 600; color: var(--primary);">-</div>
                            </div>
                            <div>
                                <div style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 3px;">Pilihan 2</div>
                                <div id="m-tim-2" style="font-size: 13px; font-weight: 500; color: var(--text-secondary);">-</div>
                            </div>
                        </div>
                    </div>

                    {{-- Dokumen --}}
                    <div style="margin-bottom: 24px;">
                        <h5 style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                            Dokumen Pendaftaran
                        </h5>
                        <div style="display: flex; gap: 8px;">
                            <a href="#" id="m-btn-cv" class="btn-outline-custom" style="flex: 1; justify-content: center; text-decoration: none;">
                                <i class="fas fa-download"></i> Unduh CV
                            </a>
                            <a href="#" id="m-btn-rekom" class="btn-outline-custom" style="flex: 1; justify-content: center; text-decoration: none;">
                                <i class="fas fa-download"></i> Rekomendasi
                            </a>
                        </div>
                    </div>

                    {{-- Status Dokumen Akhir --}}
                    <h5 style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                        Status Pengiriman Dokumen
                    </h5>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                            <span style="font-size: 13px;">SK Magang</span>
                            <span id="m-sk-badge" class="badge-status badge-pending">
                                <i class="fas fa-clock"></i> Belum
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                            <span style="font-size: 13px;">Lembar Evaluasi</span>
                            <span id="m-eval-badge" class="badge-status badge-pending">
                                <i class="fas fa-clock"></i> Belum
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0;">
                            <span style="font-size: 13px;">Sertifikat</span>
                            <span id="m-cert-badge" class="badge-status badge-pending">
                                <i class="fas fa-clock"></i> Belum
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div style="padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <div id="m-resign-area" style="display: none;">
                <button onclick="confirmAnulir()" class="btn-sm-custom" style="
                    background: #fef2f2; color: #dc2626; border: 1px solid #fecaca;
                    padding: 8px 16px; border-radius: 8px; cursor: pointer;
                    font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 500;
                    display: inline-flex; align-items: center; gap: 6px;
                    transition: all 150ms ease;
                " onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                    <i class="fas fa-door-open"></i> Mengundurkan Diri
                </button>
            </div>
            <div style="margin-left: auto;">
                <button onclick="closeModal()" class="btn-outline-custom">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .hidden { display: none !important; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes scaleIn { from { opacity: 0; transform: translate(-50%, -50%) scale(0.95); } to { opacity: 1; transform: translate(-50%, -50%) scale(1); } }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>
@endpush

@push('scripts')
<script>
    // Tab switching
    function switchTab(tabId, e) {
        e.preventDefault();

        // Update tab active states
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
        e.currentTarget.classList.add('active');

        // Show/hide panels
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById('tab-' + tabId).classList.remove('hidden');
    }

    // Modal
    function closeModal() {
        document.getElementById('detailOverlay').style.display = 'none';
    }

    // Close on overlay click
    document.getElementById('detailOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });

    // Close on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

    $(document).ready(function() {
        $('.view-detail').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            $('#loadingSpinner').show();
            $('#modalContent').hide();
            document.getElementById('detailOverlay').style.display = 'block';

            $.get(`/admin/manajemen/${id}`, function(data) {
                // Store current ID for resign action
                window._currentPesertaId = data.id;
                window._currentPesertaNama = data.nama;

                // Populate
                $('#m-nama').text(data.nama);
                const statusEl = $('#m-status');
                statusEl.text(data.status_magang);
                statusEl.attr('class', 'badge-status');
                if (data.status_magang === 'Aktif') statusEl.addClass('badge-aktif');
                else if (data.status_magang === 'Belum Aktif') statusEl.addClass('badge-belum');
                else if (data.status_magang === 'Selesai') statusEl.addClass('badge-selesai');
                else if (data.status_magang === 'Anulir') statusEl.addClass('badge-anulir');
                else if (data.status_magang === 'Ditolak') statusEl.addClass('badge-anulir');

                $('#m-email').text(data.email);
                $('#m-telp').text(data.nomor_telp);
                $('#m-institusi').text(data.nama_institusi + ' (' + data.tingkat_pendidikan + ')');
                $('#m-jurusan').text(data.jurusan);
                $('#m-email-institusi').text(data.email_institusi);

                const opt = {day: 'numeric', month: 'long', year: 'numeric'};
                $('#m-mulai').text(new Date(data.tanggal_mulai).toLocaleDateString('id-ID', opt));
                $('#m-selesai').text(new Date(data.tanggal_selesai).toLocaleDateString('id-ID', opt));

                $('#m-tim-1').text(data.tim_kerja1 ? data.tim_kerja1.nama_tim : '-');
                $('#m-tim-2').text(data.tim_kerja2 ? data.tim_kerja2.nama_tim : '-');

                setDocBadge('#m-sk-badge', data.is_sk_sent);
                setDocBadge('#m-eval-badge', data.is_evaluasi_sent);
                setDocBadge('#m-cert-badge', data.is_sertifikat_sent);

                // Show resign button only for Belum Aktif / Aktif
                if (data.status_magang === 'Belum Aktif' || data.status_magang === 'Aktif') {
                    $('#m-resign-area').show();
                } else {
                    $('#m-resign-area').hide();
                }

                $('#loadingSpinner').hide();
                $('#modalContent').show();
            });
        });

        function setDocBadge(sel, isSent) {
            const el = $(sel);
            el.attr('class', 'badge-status');
            if (isSent) {
                el.addClass('badge-sent').html('<i class="fas fa-check"></i> Terkirim');
            } else {
                el.addClass('badge-pending').html('<i class="fas fa-clock"></i> Belum');
            }
        }
    });

    // Resign / Anulir action
    function confirmAnulir() {
        const nama = window._currentPesertaNama;
        if (!confirm(`Apakah Anda yakin ingin mengundurkan ${nama}?\nStatus akan berubah menjadi ANULIR dan tidak dapat dibatalkan.`)) return;

        $.ajax({
            url: `/admin/manajemen/${window._currentPesertaId}/anulir`,
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                alert(res.message);
                closeModal();
                location.reload();
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.error || 'Terjadi kesalahan.');
            }
        });
    }
</script>
@endpush
