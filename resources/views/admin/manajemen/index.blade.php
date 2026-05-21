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

    {{-- Controls: Search & Sort --}}
    <div style="padding: 16px 22px; border-bottom: 1px solid var(--border); display: flex; gap: 16px; flex-wrap: wrap; align-items: center; justify-content: space-between; background: #fafbfc;">
        {{-- Search Bar --}}
        <div style="width: 100%; max-width: 340px; position: relative;">
            <i class="fas fa-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 13px;"></i>
            <input type="text" id="globalSearchInput" placeholder="Cari nama peserta atau institusi..." 
                   style="width: 100%; padding: 10px 14px 10px 38px; border-radius: 8px; background: #ffffff; border: 1px solid #cbd5e1; font-size: 13px; color: var(--text-primary); transition: all 0.2s; outline: none;"
                   onfocus="this.style.borderColor='var(--primary)';"
                   onblur="this.style.borderColor='#cbd5e1';">
        </div>

        {{-- Action Controls --}}
        <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
            {{-- Sort Dropdown --}}
            <div style="display: flex; align-items: center; gap: 10px;">
                <label style="font-size: 11.5px; font-weight: 600; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em;"><i class="fas fa-sort-amount-down" style="margin-right: 4px;"></i> Urutkan</label>
                <select id="globalSortSelect" style="padding: 6px 28px 6px 10px; border-radius: 6px; border: 1px solid #cbd5e1; background: #fff; font-size: 12px; color: var(--text-primary); outline: none; cursor: pointer; appearance: auto; font-family: 'Inter', sans-serif;">
                    <option value="name_asc">Sesuai Abjad (A - Z)</option>
                    <option value="name_desc">Sesuai Abjad (Z - A)</option>
                    <option value="date_nearest">Segera Berakhir (Tanggal Terdekat)</option>
                    <option value="date_farthest">Waktu Tersisa Paling Lama</option>
                </select>
            </div>
            
            {{-- Export Button --}}
            <button type="button" onclick="openExportModal()" style="padding: 9px 18px; font-size: 13px; font-weight: 600; font-family: 'Inter', sans-serif; background: linear-gradient(135deg, var(--primary), #2548a8); color: #fff; border: 1.5px solid transparent; border-radius: 10px; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 12px rgba(30,58,138,0.3); transition: all 0.2s ease;" onmouseover="this.style.background='#fff'; this.style.color='var(--primary)'; this.style.borderColor='var(--primary)'; this.style.boxShadow='0 4px 14px rgba(30,58,138,0.15)';" onmouseout="this.style.background='linear-gradient(135deg, var(--primary), #2548a8)'; this.style.color='#fff'; this.style.borderColor='transparent'; this.style.boxShadow='0 4px 12px rgba(30,58,138,0.3)';">
                <i class="fas fa-file-download"></i> Unduh Laporan
            </button>
        </div>
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
                        <tr class="manajemen-row" 
                            data-name="{{ strtolower($item->nama) }}" 
                            data-institusi="{{ strtolower($item->nama_institusi) }}" 
                            data-date="{{ \Carbon\Carbon::parse($item->tanggal_selesai)->timestamp }}">
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
                            <td style="white-space: nowrap; font-size: 13px;" class="cell-date">
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
        max-height: 85vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        animation: scaleIn 0.2s ease;
    ">
        {{-- Modal Header --}}
        <div style="
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
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
        <div id="modalContent" style="display: none; padding: 20px 24px; overflow-y: auto;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                {{-- Left Column: Biodata --}}
                <div>
                    {{-- Profile with Photo --}}
                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 20px;">
                        <div style="flex-shrink: 0; position: relative;">
                            <img id="m-foto" src="" alt="Pas Foto"
                                 style="width: 72px; height: 72px; border-radius: 14px; object-fit: cover; border: 2px solid var(--border); display: none;">
                            <div id="m-foto-placeholder" style="
                                width: 72px; height: 72px; border-radius: 14px;
                                background: linear-gradient(135deg, var(--primary), var(--primary-light));
                                display: flex; align-items: center; justify-content: center;
                                color: #fff; font-size: 26px;
                            ">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        <div>
                            <h4 id="m-nama" style="font-size: 16px; font-weight: 700; color: var(--text-primary); margin: 0 0 4px;">-</h4>
                            <span id="m-nim" style="font-size: 12px; color: var(--text-secondary); display: block; margin-bottom: 4px;">-</span>
                            <span id="m-status" class="badge-status badge-aktif">-</span>
                        </div>
                    </div>

                    {{-- Contact & Education --}}
                    <div style="margin-bottom: 20px;">
                        <h5 style="font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
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
                    <h5 style="font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
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
                    <div style="margin-bottom: 20px;">
                        <h5 style="font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
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
                    <div style="margin-bottom: 20px;">
                        <h5 style="font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                            Dokumen Pendaftaran
                        </h5>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <a href="#" id="m-btn-rekom" target="_blank"
                               class="btn-primary-custom"
                               style="justify-content: center; text-decoration: none;">
                                <i class="fas fa-file-pdf"></i> Buka Surat Permohonan
                            </a>
                            <div style="display: flex; gap: 8px;">
                                <a href="#" id="m-btn-cv" target="_blank"
                                   class="btn-outline-custom"
                                   style="flex:1; justify-content: center; text-decoration: none; display: none;">
                                    <i class="fas fa-file-pdf"></i> Buka CV
                                </a>
                                <a href="#" id="m-btn-foto" target="_blank"
                                   class="btn-outline-custom"
                                   style="flex:1; justify-content: center; text-decoration: none; display: none;">
                                    <i class="fas fa-camera"></i> Unduh Pas Foto
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Status Dokumen Akhir --}}
                    <h5 style="font-size: 11.5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary); margin-bottom: 10px; padding-bottom: 8px; border-bottom: 1px solid var(--border);">
                        Status Pengiriman Dokumen
                    </h5>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
                            <span style="font-size: 13px;">Surat Penerimaan</span>
                            <span id="m-penerimaan-badge" class="badge-status badge-pending">
                                <i class="fas fa-clock"></i> Belum
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
                            <span style="font-size: 13px;">SK Magang</span>
                            <span id="m-sk-badge" class="badge-status badge-pending">
                                <i class="fas fa-clock"></i> Belum
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
                            <span style="font-size: 13px;">Surat Evaluasi</span>
                            <span id="m-eval-badge" class="badge-status badge-pending">
                                <i class="fas fa-clock"></i> Belum
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0;">
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
        <div style="padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
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

{{-- ═══ MODAL EXPORT LAPORAN (REDESIGNED) ═══ --}}
<div id="exportModalOverlay" style="
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(10, 20, 50, 0.55);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    z-index: 1050;
    align-items: center;
    justify-content: center;
">
    <div style="
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        width: calc(100% - 40px);
        max-width: 480px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 32px 64px rgba(0,0,0,0.18), 0 8px 24px rgba(0,0,0,0.1);
        animation: exportModalIn 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    ">
        {{-- Header --}}
        <div style="
            background: linear-gradient(135deg, var(--primary) 0%, #2548a8 100%);
            padding: 22px 24px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        ">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="
                    width: 38px; height: 38px;
                    background: rgba(255,255,255,0.15);
                    border-radius: 10px;
                    display: flex; align-items: center; justify-content: center;
                    font-size: 16px; color: #fff;
                ">
                    <i class="fas fa-file-export"></i>
                </div>
                <div>
                    <div style="font-size: 16px; font-weight: 700; color: #fff; line-height: 1.2;">Unduh Laporan Magang</div>
                    <div style="font-size: 12px; color: rgba(255,255,255,0.65); margin-top: 2px;">Format Excel (.xls)</div>
                </div>
            </div>
            <button type="button" onclick="closeExportModal()" style="
                width: 32px; height: 32px;
                background: rgba(255,255,255,0.15);
                border: none; border-radius: 8px;
                color: #fff; cursor: pointer;
                display: flex; align-items: center; justify-content: center;
                font-size: 14px;
                transition: background 0.15s ease;
            " onmouseover="this.style.background='rgba(255,255,255,0.25)'"
               onmouseout="this.style.background='rgba(255,255,255,0.15)'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Form Body --}}
        <form action="{{ route('admin.manajemen.export') }}" method="GET">
            <div style="padding: 24px;">

                {{-- Section: Status Peserta --}}
                <div style="margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <div style="
                            width: 4px; height: 16px;
                            background: var(--primary);
                            border-radius: 2px;
                        "></div>
                        <span style="font-size: 12.5px; font-weight: 700; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em;">
                            Filter Status Peserta
                        </span>
                    </div>
                    <p style="font-size: 12px; color: var(--text-muted); margin-bottom: 12px;">Pilih satu atau lebih status yang ingin disertakan dalam laporan.</p>
                    <div style="display: flex; flex-wrap: wrap; gap: 8px;" id="statusCheckboxGroup">
                        <label class="export-status-pill" style="--pill-color: #92400e; --pill-bg: #fffbeb; --pill-border: #fcd34d;">
                            <input type="checkbox" name="statuses[]" value="Belum Aktif" checked hidden>
                            <span><i class="fas fa-hourglass-half" style="font-size: 10px;"></i> Belum Aktif</span>
                        </label>
                        <label class="export-status-pill" style="--pill-color: #065f46; --pill-bg: #ecfdf5; --pill-border: #6ee7b7;">
                            <input type="checkbox" name="statuses[]" value="Aktif" checked hidden>
                            <span><i class="fas fa-circle" style="font-size: 8px;"></i> Aktif</span>
                        </label>
                        <label class="export-status-pill" style="--pill-color: #1e3a8a; --pill-bg: #dbeafe; --pill-border: #93c5fd;">
                            <input type="checkbox" name="statuses[]" value="Selesai" checked hidden>
                            <span><i class="fas fa-graduation-cap" style="font-size: 10px;"></i> Selesai</span>
                        </label>
                        <label class="export-status-pill" style="--pill-color: #991b1b; --pill-bg: #fef2f2; --pill-border: #fca5a5;">
                            <input type="checkbox" name="statuses[]" value="Ditolak" hidden>
                            <span><i class="fas fa-times-circle" style="font-size: 10px;"></i> Ditolak</span>
                        </label>
                        <label class="export-status-pill" style="--pill-color: #6b21a8; --pill-bg: #f5f3ff; --pill-border: #c4b5fd;">
                            <input type="checkbox" name="statuses[]" value="Anulir" hidden>
                            <span><i class="fas fa-ban" style="font-size: 10px;"></i> Anulir</span>
                        </label>
                    </div>
                </div>

                {{-- Divider --}}
                <div style="height: 1px; background: var(--border); margin-bottom: 24px;"></div>

                {{-- Section: Rentang Waktu --}}
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <div style="
                            width: 4px; height: 16px;
                            background: var(--accent);
                            border-radius: 2px;
                        "></div>
                        <span style="font-size: 12.5px; font-weight: 700; color: var(--text-primary); text-transform: uppercase; letter-spacing: 0.05em;">
                            Rentang Waktu
                        </span>
                    </div>
                    <div style="position: relative;">
                        <i class="fas fa-calendar-alt" style="
                            position: absolute; left: 14px; top: 50%;
                            transform: translateY(-50%);
                            color: var(--text-muted); font-size: 13px;
                            pointer-events: none;
                        "></i>
                        <select id="exportRentang" name="rentang_waktu" onchange="toggleExportFields()" style="
                            width: 100%;
                            padding: 11px 14px 11px 38px;
                            border-radius: 10px;
                            border: 1.5px solid var(--border);
                            font-size: 13px;
                            font-family: 'Inter', sans-serif;
                            font-weight: 500;
                            background: #f8fafc;
                            color: var(--text-primary);
                            outline: none;
                            cursor: pointer;
                            transition: border-color 0.15s ease;
                            appearance: auto;
                        " onfocus="this.style.borderColor='var(--primary)'"
                           onblur="this.style.borderColor='var(--border)'">
                            <option value="semua">Semua Waktu</option>
                            <option value="triwulan">Per Triwulan</option>
                            <option value="tahunan">Per Tahun</option>
                        </select>
                    </div>
                </div>

                {{-- Sub-filters (Triwulan) --}}
                <div id="exportTriwulanWrapper" style="display: none; margin-bottom: 16px; animation: fadeInDown 0.2s ease;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.04em;">Pilih Triwulan</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <label class="export-tw-option">
                            <input type="radio" name="triwulan" value="1" hidden>
                            <span>Triwulan 1 <small>Jan – Mar</small></span>
                        </label>
                        <label class="export-tw-option">
                            <input type="radio" name="triwulan" value="2" hidden>
                            <span>Triwulan 2 <small>Apr – Jun</small></span>
                        </label>
                        <label class="export-tw-option">
                            <input type="radio" name="triwulan" value="3" hidden>
                            <span>Triwulan 3 <small>Jul – Sep</small></span>
                        </label>
                        <label class="export-tw-option">
                            <input type="radio" name="triwulan" value="4" hidden>
                            <span>Triwulan 4 <small>Okt – Des</small></span>
                        </label>
                    </div>
                </div>

                {{-- Sub-filters (Tahun) --}}
                <div id="exportTahunWrapper" style="display: none; margin-bottom: 16px; animation: fadeInDown 0.2s ease;">
                    <label style="display: block; font-size: 12px; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.04em;">Tahun</label>
                    <div style="position: relative;">
                        <i class="fas fa-hashtag" style="
                            position: absolute; left: 14px; top: 50%;
                            transform: translateY(-50%);
                            color: var(--text-muted); font-size: 13px;
                            pointer-events: none;
                        "></i>
                        <input type="number" name="tahun" value="{{ date('Y') }}"
                               min="2020" max="2099"
                               style="
                                   width: 100%;
                                   padding: 11px 14px 11px 38px;
                                   border-radius: 10px;
                                   border: 1.5px solid var(--border);
                                   font-size: 13px;
                                   font-family: 'Inter', sans-serif;
                                   font-weight: 600;
                                   color: var(--primary);
                                   background: #f8fafc;
                                   outline: none;
                                   text-align: left;
                               "
                               onfocus="this.style.borderColor='var(--primary)'"
                               onblur="this.style.borderColor='var(--border)'">
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div style="
                padding: 16px 24px;
                border-top: 1px solid var(--border);
                background: #fafbfc;
                display: flex;
                gap: 10px;
                justify-content: flex-end;
                align-items: center;
            ">
                <button type="button" onclick="closeExportModal()" style="
                    padding: 10px 20px;
                    font-size: 13px;
                    font-weight: 500;
                    font-family: 'Inter', sans-serif;
                    background: #fff;
                    border: 1.5px solid var(--border);
                    border-radius: 10px;
                    color: var(--text-secondary);
                    cursor: pointer;
                    transition: all 0.15s ease;
                " onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                   onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-secondary)'">
                    Batal
                </button>
                <button type="submit" style="
                    padding: 10px 22px;
                    font-size: 13px;
                    font-weight: 600;
                    font-family: 'Inter', sans-serif;
                    background: linear-gradient(135deg, var(--primary), #2548a8);
                    color: #fff;
                    border: none;
                    border-radius: 10px;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    box-shadow: 0 4px 12px rgba(30,58,138,0.3);
                    transition: all 0.15s ease;
                " onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 16px rgba(30,58,138,0.4)'"
                   onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 12px rgba(30,58,138,0.3)'">
                    <i class="fas fa-file-download"></i>
                    Unduh Laporan
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    /* Export Modal Animation */
    @keyframes exportModalIn {
        from { opacity: 0; transform: translate(-50%, -48%) scale(0.96); }
        to   { opacity: 1; transform: translate(-50%, -50%) scale(1); }
    }
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* Pill-style status checkbox */
    .export-status-pill {
        display: inline-flex;
        align-items: center;
        cursor: pointer;
    }

    .export-status-pill span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 13px;
        border-radius: 999px;
        font-size: 12.5px;
        font-weight: 500;
        border: 1.5px solid #e2e8f0;
        background: #f1f5f9;
        color: #64748b;
        transition: all 0.15s ease;
        user-select: none;
    }

    .export-status-pill input:checked + span {
        background: var(--pill-bg, #dbeafe);
        color: var(--pill-color, #1e3a8a);
        border-color: var(--pill-border, #93c5fd);
        font-weight: 600;
    }

    .export-status-pill:hover span {
        border-color: var(--pill-border, #93c5fd);
        color: var(--pill-color, #1e3a8a);
    }

    /* Triwulan radio options */
    .export-tw-option {
        display: flex;
        cursor: pointer;
    }

    .export-tw-option span {
        display: flex;
        align-items: center;
        gap: 6px;
        width: 100%;
        padding: 10px 14px;
        border-radius: 10px;
        border: 1.5px solid var(--border);
        font-size: 12.5px;
        font-weight: 500;
        color: var(--text-secondary);
        background: #fff;
        transition: all 0.15s ease;
        user-select: none;
    }

    .export-tw-option span small {
        display: block;
        font-size: 10.5px;
        color: var(--text-muted);
        font-weight: 400;
        margin-top: 1px;
    }

    .export-tw-option input:checked + span {
        background: var(--primary-lighter);
        border-color: var(--primary);
        color: var(--primary);
        font-weight: 600;
    }

    .export-tw-option:hover span {
        border-color: var(--primary);
        color: var(--primary);
    }
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

    // Modal Export
    function openExportModal() {
        document.getElementById('exportModalOverlay').style.display = 'block';
    }
    function closeExportModal() {
        document.getElementById('exportModalOverlay').style.display = 'none';
    }
    function toggleExportFields() {
        const val = document.getElementById('exportRentang').value;
        const triwulanWrap = document.getElementById('exportTriwulanWrapper');
        const tahunWrap = document.getElementById('exportTahunWrapper');
        
        if (val === 'semua') {
            triwulanWrap.style.display = 'none';
            tahunWrap.style.display = 'none';
        } else if (val === 'triwulan') {
            triwulanWrap.style.display = 'block';
            tahunWrap.style.display = 'block';
        } else if (val === 'tahunan') {
            triwulanWrap.style.display = 'none';
            tahunWrap.style.display = 'block';
        }
    }

    // Modal Detail

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

    // Fitur Search dan Sort Client-Side
    const globalSearchInput = document.getElementById('globalSearchInput');
    const globalSortSelect = document.getElementById('globalSortSelect');

    function filterAndSort() {
        const searchTerm = globalSearchInput.value.toLowerCase();
        const sortValue = globalSortSelect.value;
        
        document.querySelectorAll('.table-clean tbody').forEach(tbody => {
            let rows = Array.from(tbody.querySelectorAll('.manajemen-row'));
            
            // 1. Filter
            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const institusi = row.getAttribute('data-institusi');
                
                if (name.includes(searchTerm) || institusi.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // 2. Sort
            rows.sort((a, b) => {
                const nameA = a.getAttribute('data-name');
                const nameB = b.getAttribute('data-name');
                const dateA = parseInt(a.getAttribute('data-date')) || 0;
                const dateB = parseInt(b.getAttribute('data-date')) || 0;
                
                if (sortValue === 'name_asc') return nameA.localeCompare(nameB);
                if (sortValue === 'name_desc') return nameB.localeCompare(nameA);
                if (sortValue === 'date_nearest') return dateA - dateB;
                if (sortValue === 'date_farthest') return dateB - dateA;
                return 0;
            });
            
            // 3. Re-append ke DOM sesuai urutan
            rows.forEach(row => tbody.appendChild(row));
        });
    }

    if (globalSearchInput) globalSearchInput.addEventListener('input', filterAndSort);
    if (globalSortSelect) globalSortSelect.addEventListener('change', filterAndSort);
    
    // Inisialisasi awal agar urut
    if (globalSortSelect) filterAndSort();

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

                // Nama & NIM
                $('#m-nim').text(data.nim_nis ? 'NIM/NIS: ' + data.nim_nis : '');

                // Foto
                if (data.pas_foto) {
                    const fotoUrl = '/storage/' + data.pas_foto;
                    $('#m-foto').attr('src', fotoUrl).show();
                    $('#m-foto-placeholder').hide();
                    $('#m-btn-foto').attr('href', fotoUrl).attr('download', data.nama + '_pas_foto').show();
                } else {
                    $('#m-foto').hide();
                    $('#m-foto-placeholder').show();
                    $('#m-btn-foto').hide();
                }

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

                // Dokumen links
                if (data.surat_rekomendasi) {
                    $('#m-btn-rekom').attr('href', '/storage/' + data.surat_rekomendasi).show();
                } else {
                    $('#m-btn-rekom').hide();
                }
                if (data.cv) {
                    $('#m-btn-cv').attr('href', '/storage/' + data.cv).show();
                } else {
                    $('#m-btn-cv').hide();
                }

                // Dokumen status
                const isPenerimaanSent = (data.status_magang !== 'Pending' && data.status_magang !== 'Ditolak');
                setDocBadge('#m-penerimaan-badge', isPenerimaanSent);
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
