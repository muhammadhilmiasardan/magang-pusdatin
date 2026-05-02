@extends('layouts.admin')

@section('title', 'Manajemen Magang')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <ul class="nav nav-tabs card-header-tabs" id="manajemenTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="aktif-tab" data-bs-toggle="tab" href="#aktif" role="tab" aria-controls="aktif" aria-selected="true">
                    Aktif <span class="badge bg-success ms-1">{{ $grouped['aktif']->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="belum-aktif-tab" data-bs-toggle="tab" href="#belum-aktif" role="tab" aria-controls="belum-aktif" aria-selected="false">
                    Belum Aktif <span class="badge bg-warning ms-1">{{ $grouped['belum_aktif']->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="selesai-tab" data-bs-toggle="tab" href="#selesai" role="tab" aria-controls="selesai" aria-selected="false">
                    Selesai <span class="badge bg-primary ms-1">{{ $grouped['selesai']->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="anulir-tab" data-bs-toggle="tab" href="#anulir" role="tab" aria-controls="anulir" aria-selected="false">
                    Anulir <span class="badge bg-danger ms-1">{{ $grouped['anulir']->count() }}</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="manajemenTabsContent">
            
            @php
                $tabs = [
                    'aktif' => $grouped['aktif'],
                    'belum-aktif' => $grouped['belum_aktif'],
                    'selesai' => $grouped['selesai'],
                    'anulir' => $grouped['anulir']
                ];
            @endphp

            @foreach($tabs as $id => $collection)
            <div class="tab-pane fade {{ $id == 'aktif' ? 'show active' : '' }}" id="{{ $id }}" role="tabpanel" aria-labelledby="{{ $id }}-tab">
                @if($collection->count() == 0)
                    <p class="text-center text-muted py-4">Tidak ada data untuk status ini.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Institusi</th>
                                    <th>Penempatan</th>
                                    <th>Tgl Selesai</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($collection as $item)
                                <tr>
                                    <td>
                                        <a href="#" class="text-decoration-none fw-bold view-detail" data-id="{{ $item->id }}">
                                            {{ $item->nama }}
                                        </a>
                                    </td>
                                    <td>{{ $item->nama_institusi }}</td>
                                    <td>{{ $item->timKerja1->nama_tim ?? 'Belum ditentukan' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary view-detail" data-id="{{ $item->id }}">
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
    </div>
</div>

<!-- Modal Detail Peserta -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="detailModalLabel">Detail Peserta Magang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="text-center" id="loadingSpinner">
                    <div class="spinner-border text-primary my-5" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                <div class="row d-none" id="modalContent">
                    <!-- Sisi Kiri (Biodata) -->
                    <div class="col-md-6 border-end">
                        <div class="d-flex align-items-center mb-4">
                            <div class="bg-secondary rounded-circle d-flex justify-content-center align-items-center text-white" style="width: 80px; height: 80px; font-size: 2rem;">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="ms-3">
                                <h4 class="mb-1" id="m-nama">-</h4>
                                <span class="badge bg-info" id="m-status">-</span>
                            </div>
                        </div>

                        <h6 class="fw-bold border-bottom pb-2 mb-3">Informasi Kontak & Pendidikan</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td width="130" class="text-muted">Email</td>
                                <td id="m-email">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">No. WA</td>
                                <td id="m-telp">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Institusi</td>
                                <td id="m-institusi">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jurusan</td>
                                <td id="m-jurusan">-</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email Institusi</td>
                                <td id="m-email-institusi">-</td>
                            </tr>
                        </table>

                        <h6 class="fw-bold border-bottom pb-2 mb-3 mt-4">Periode Pelaksanaan</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <small class="text-muted d-block">Mulai</small>
                                <strong id="m-mulai">-</strong>
                            </div>
                            <div>
                                <i class="fas fa-arrow-right text-muted mt-3"></i>
                            </div>
                            <div class="text-end">
                                <small class="text-muted d-block">Selesai</small>
                                <strong id="m-selesai">-</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Sisi Kanan (Berkas & Status) -->
                    <div class="col-md-6 ps-md-4">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Penempatan</h6>
                        <div class="p-3 bg-light rounded mb-4">
                            <div class="mb-2">
                                <small class="text-muted d-block">Pilihan 1 (Utama)</small>
                                <strong id="m-tim-1">-</strong>
                            </div>
                            <div>
                                <small class="text-muted d-block">Pilihan 2</small>
                                <strong id="m-tim-2">-</strong>
                            </div>
                        </div>

                        <h6 class="fw-bold border-bottom pb-2 mb-3">Dokumen Pendaftaran</h6>
                        <div class="d-flex gap-2 mb-4">
                            <a href="#" class="btn btn-outline-primary btn-sm flex-fill" id="m-btn-cv"><i class="fas fa-download"></i> Unduh CV</a>
                            <a href="#" class="btn btn-outline-primary btn-sm flex-fill" id="m-btn-rekom"><i class="fas fa-download"></i> Unduh Rekomendasi</a>
                        </div>

                        <h6 class="fw-bold border-bottom pb-2 mb-3">Status Pengiriman Dokumen Akhir</h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                SK Magang (Peserta)
                                <span class="badge rounded-pill" id="m-sk-badge">Belum</span>
                            </li>
                            <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                Lembar Evaluasi (Institusi)
                                <span class="badge rounded-pill" id="m-eval-badge">Belum</span>
                            </li>
                            <li class="list-group-item px-0 border-bottom-0 d-flex justify-content-between align-items-center">
                                Sertifikat Kelulusan (Peserta)
                                <span class="badge rounded-pill" id="m-cert-badge">Belum</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('.view-detail').click(function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            
            $('#loadingSpinner').removeClass('d-none');
            $('#modalContent').addClass('d-none');
            modal.show();

            $.get(`/admin/manajemen/${id}`, function(data) {
                // Populate Data
                $('#m-nama').text(data.nama);
                $('#m-status').text(data.status_magang);
                $('#m-email').text(data.email);
                $('#m-telp').text(data.nomor_telp);
                $('#m-institusi').text(data.nama_institusi + ' (' + data.tingkat_pendidikan + ')');
                $('#m-jurusan').text(data.jurusan);
                $('#m-email-institusi').text(data.email_institusi);
                
                $('#m-mulai').text(new Date(data.tanggal_mulai).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}));
                $('#m-selesai').text(new Date(data.tanggal_selesai).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}));

                $('#m-tim-1').text(data.tim_kerja1 ? data.tim_kerja1.nama_tim : '-');
                $('#m-tim-2').text(data.tim_kerja2 ? data.tim_kerja2.nama_tim : '-');

                // Status Badges
                setDocumentBadge('#m-sk-badge', data.is_sk_sent);
                setDocumentBadge('#m-eval-badge', data.is_evaluasi_sent);
                setDocumentBadge('#m-cert-badge', data.is_sertifikat_sent);

                $('#loadingSpinner').addClass('d-none');
                $('#modalContent').removeClass('d-none').addClass('d-flex');
            });
        });

        function setDocumentBadge(selector, isSent) {
            const el = $(selector);
            if (isSent) {
                el.removeClass('bg-secondary text-white').addClass('bg-success text-white').html('<i class="fas fa-check"></i> Terkirim');
            } else {
                el.removeClass('bg-success text-white').addClass('bg-secondary text-white').html('<i class="fas fa-clock"></i> Belum');
            }
        }
    });
</script>
@endpush
