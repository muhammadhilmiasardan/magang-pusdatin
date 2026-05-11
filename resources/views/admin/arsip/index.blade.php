@extends('layouts.admin')

@section('title', 'Arsip Dokumen')

@section('content')
<div class="card-clean">
    <div class="card-header-clean" style="flex-direction: column; align-items: flex-start; gap: 16px;">
        <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
            <div>
                <h3 style="margin-bottom: 4px;">Arsip Seluruh Dokumen</h3>
                <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">Kelola dan unduh dokumen final (TTE/TTD) milik peserta magang.</p>
            </div>
            <button form="bulkDownloadForm" type="submit" class="btn-primary-custom" id="btnDownloadBulk" disabled>
                <i class="fas fa-file-archive"></i> Unduh Terpilih (.zip)
            </button>
        </div>

        {{-- Search Bar --}}
        <div style="width: 100%; max-width: 360px; position: relative; margin-top: 8px;">
            <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px;"></i>
            <input type="text" id="searchInput" placeholder="Cari nama peserta atau asal institusi..." 
                   style="width: 100%; padding: 12px 16px 12px 42px; border-radius: 10px; background: #ffffff; border: 1px solid #cbd5e1; font-size: 14px; color: var(--text-primary); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none;"
                   onfocus="this.style.borderColor='var(--primary)';"
                   onblur="this.style.borderColor='#cbd5e1';">
        </div>
    </div>

    <div class="card-body-clean" style="padding: 0;">
        @if($peserta->isEmpty())
            <div class="empty-state" style="padding: 60px 20px;">
                <i class="fas fa-archive" style="font-size: 48px; color: var(--text-muted); margin-bottom: 16px; display: block;"></i>
                <h4 style="font-size: 16px; color: var(--text-primary); margin-bottom: 8px;">Belum Ada Arsip</h4>
                <p style="color: var(--text-secondary); font-size: 14px;">Dokumen akan muncul di sini setelah diunggah pada tahap Penerimaan atau di menu Pusat Dokumen.</p>
            </div>
        @else
            <form id="bulkDownloadForm" action="{{ route('admin.arsip-dokumen.download-bulk') }}" method="POST">
                @csrf
                <div style="overflow-x: auto;">
                    <table class="table-clean" id="arsipTable">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">
                                    <input type="checkbox" id="selectAll" style="cursor: pointer;">
                                </th>
                                <th>Nama Peserta</th>
                                <th>Institusi</th>
                                <th>Periode Magang</th>
                                <th>Kelengkapan Dokumen (Final)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peserta as $p)
                            <tr class="arsip-row">
                                <td style="text-align: center;">
                                    <input type="checkbox" name="peserta_ids[]" value="{{ $p->id }}" class="row-checkbox" style="cursor: pointer;" onchange="updateDownloadBtn()">
                                </td>
                                <td>
                                    <div class="peserta-nama" style="font-weight: 600; color: var(--text-primary);">{{ $p->nama }}</div>
                                    <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 2px;">
                                        <i class="fas fa-envelope" style="font-size: 10px;"></i> {{ $p->email }}
                                    </div>
                                </td>
                                <td class="peserta-institusi">{{ $p->nama_institusi }}</td>
                                <td style="font-size: 12.5px; white-space: nowrap;">
                                    {{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}
                                    <span style="color: var(--text-muted);"> s/d </span>
                                    {{ \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') }}
                                </td>
                                <td>
                                    <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                        {{-- Surat Penerimaan --}}
                                        @if($p->surat_penerimaan_final)
                                            <button type="button" class="doc-badge doc-available" onclick="previewDoc('{{ asset('storage/' . $p->surat_penerimaan_final) }}', 'Surat Penerimaan - {{ $p->nama }}')" title="Surat Penerimaan Tersedia">
                                                <i class="fas fa-envelope-open-text"></i> Penerimaan
                                            </button>
                                        @else
                                            <span class="doc-badge doc-unavailable" title="Surat Penerimaan Belum Ada">
                                                <i class="fas fa-times"></i> Penerimaan
                                            </span>
                                        @endif

                                        {{-- SK Magang --}}
                                        @if($p->surat_keterangan)
                                            <button type="button" class="doc-badge doc-available" onclick="previewDoc('{{ asset('storage/' . $p->surat_keterangan) }}', 'SK Magang - {{ $p->nama }}')" title="SK Magang Tersedia">
                                                <i class="fas fa-file-contract"></i> SK Magang
                                            </button>
                                        @else
                                            <span class="doc-badge doc-unavailable" title="SK Magang Belum Ada">
                                                <i class="fas fa-times"></i> SK Magang
                                            </span>
                                        @endif

                                        {{-- Evaluasi --}}
                                        @if($p->surat_evaluasi)
                                            <button type="button" class="doc-badge doc-available" onclick="previewDoc('{{ asset('storage/' . $p->surat_evaluasi) }}', 'Lembar Evaluasi - {{ $p->nama }}')" title="Evaluasi Tersedia">
                                                <i class="fas fa-clipboard-check"></i> Evaluasi
                                            </button>
                                        @else
                                            <span class="doc-badge doc-unavailable" title="Evaluasi Belum Ada">
                                                <i class="fas fa-times"></i> Evaluasi
                                            </span>
                                        @endif

                                        {{-- Sertifikat --}}
                                        @if($p->surat_sertifikat)
                                            <button type="button" class="doc-badge doc-available" onclick="previewDoc('{{ asset('storage/' . $p->surat_sertifikat) }}', 'Sertifikat - {{ $p->nama }}')" title="Sertifikat Tersedia">
                                                <i class="fas fa-certificate"></i> Sertifikat
                                            </button>
                                        @else
                                            <span class="doc-badge doc-unavailable" title="Sertifikat Belum Ada">
                                                <i class="fas fa-times"></i> Sertifikat
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
        @endif
    </div>
</div>

{{-- MODAL PREVIEW DOKUMEN --}}
<div id="previewModal" class="modal-overlay hidden" style="z-index: 1050;">
    <div class="modal-content" style="max-width: 900px; width: 95%; height: 90vh; display: flex; flex-direction: column;">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid var(--border);">
            <h3 id="previewTitle" style="margin: 0; font-size: 16px; font-weight: 600; color: var(--text-primary);">Preview Dokumen</h3>
            <div style="display: flex; gap: 10px;">
                <a href="#" id="previewDownloadBtn" download class="btn-outline-custom" style="padding: 6px 12px; text-decoration: none;">
                    <i class="fas fa-download"></i> Unduh
                </a>
                <button onclick="closePreviewModal()" style="background: none; border: none; cursor: pointer; font-size: 20px; color: var(--text-secondary);">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="modal-body" style="flex: 1; padding: 0; background: #e2e8f0;">
            <iframe id="previewFrame" src="" style="width: 100%; height: 100%; border: none;"></iframe>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .hidden { display: none !important; }
    
    .doc-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        border: none;
    }
    
    .doc-available {
        background: var(--primary-lighter);
        color: var(--primary-dark);
        cursor: pointer;
        transition: all var(--transition);
        border: 1px solid transparent;
    }
    
    .doc-available:hover {
        background: var(--primary);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .doc-unavailable {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
    }

    /* Modal Styles (in case not fully defined in layout) */
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.6);
        display: flex; justify-content: center; align-items: center;
    }
    .modal-content {
        background: #fff; border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script>
    // Fitur Search Client-Side
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.arsip-row');
        
        rows.forEach(row => {
            const nama = row.querySelector('.peserta-nama').textContent.toLowerCase();
            const institusi = row.querySelector('.peserta-institusi').textContent.toLowerCase();
            
            if (nama.includes(searchTerm) || institusi.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Checkbox Logic
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const btnDownloadBulk = document.getElementById('btnDownloadBulk');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            let hasVisibleChecked = false;

            rowCheckboxes.forEach(cb => {
                // Only check visible rows (respecting search filter)
                const row = cb.closest('tr');
                if (row.style.display !== 'none') {
                    cb.checked = isChecked;
                    if(isChecked) hasVisibleChecked = true;
                }
            });
            
            updateDownloadBtn();
        });
    }

    function updateDownloadBtn() {
        const anyChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
        btnDownloadBulk.disabled = !anyChecked;
        
        // Update opacity to visually indicate disabled state
        if(!anyChecked) {
            btnDownloadBulk.style.opacity = '0.5';
            btnDownloadBulk.style.cursor = 'not-allowed';
        } else {
            btnDownloadBulk.style.opacity = '1';
            btnDownloadBulk.style.cursor = 'pointer';
        }
    }
    
    // Initial call to set correct state
    if (btnDownloadBulk) updateDownloadBtn();

    // Modal Preview Logic
    function previewDoc(url, title) {
        document.getElementById('previewTitle').textContent = title;
        document.getElementById('previewFrame').src = url;
        document.getElementById('previewDownloadBtn').href = url;
        document.getElementById('previewModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closePreviewModal() {
        document.getElementById('previewModal').classList.add('hidden');
        document.getElementById('previewFrame').src = ''; // Clear iframe to stop loading
        document.body.style.overflow = '';
    }

    // Close modal on escape key or clicking outside
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePreviewModal();
    });
    
    document.getElementById('previewModal')?.addEventListener('click', function(e) {
        if (e.target === this) closePreviewModal();
    });
</script>
@endpush
