@extends('layouts.admin')

@section('title', 'Pusat Dokumen (Persuratan)')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <ul class="nav nav-tabs card-header-tabs" id="dokumenTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="sk-tab" data-bs-toggle="tab" href="#sk" role="tab">
                    SK Magang <span class="badge bg-secondary ms-1">{{ $skMagang->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="evaluasi-tab" data-bs-toggle="tab" href="#evaluasi" role="tab">
                    Pengiriman Evaluasi (H-7) <span class="badge bg-warning text-dark ms-1">{{ $evaluasi->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="sertifikat-tab" data-bs-toggle="tab" href="#sertifikat" role="tab">
                    Sertifikat Kelulusan <span class="badge bg-primary ms-1">{{ $sertifikat->count() }}</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            
            <!-- Tab SK Magang -->
            <div class="tab-pane fade show active" id="sk" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0">Daftar peserta aktif untuk penerbitan SK Magang.</p>
                </div>
                @if($skMagang->count() == 0)
                    <div class="alert alert-light text-center py-4">Tidak ada data.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Peserta</th>
                                    <th>Institusi</th>
                                    <th>Periode Magang</th>
                                    <th>Status SK</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($skMagang as $item)
                                <tr>
                                    <td><strong>{{ $item->nama }}</strong></td>
                                    <td>{{ $item->nama_institusi }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</td>
                                    <td>
                                        @if($item->is_sk_sent)
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Terkirim</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-clock"></i> Belum Dikirim</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary"><i class="fas fa-envelope"></i> Kirim SK</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Tab Evaluasi -->
            <div class="tab-pane fade" id="evaluasi" role="tabpanel">
                <div class="alert alert-warning border-0 shadow-sm mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i> 
                    Menampilkan peserta yang masa magangnya akan berakhir dalam <strong>7 hingga 14 hari ke depan</strong>. Segera kirim Lembar Evaluasi ke email Penanggung Jawab Institusi.
                </div>
                @if($evaluasi->count() == 0)
                    <div class="alert alert-light text-center py-4">Tidak ada peserta yang mendekati akhir masa magang (H-7 s/d H-14).</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Peserta</th>
                                    <th>Institusi</th>
                                    <th>Tgl Selesai</th>
                                    <th>Sisa Waktu</th>
                                    <th>Status Evaluasi</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evaluasi as $item)
                                @php
                                    $sisaHari = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($item->tanggal_selesai), false);
                                @endphp
                                <tr>
                                    <td><strong>{{ $item->nama }}</strong></td>
                                    <td>
                                        {{ $item->nama_institusi }}<br>
                                        <small class="text-muted"><i class="fas fa-envelope"></i> {{ $item->email_institusi }}</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</td>
                                    <td><span class="fw-bold text-danger">{{ $sisaHari }} Hari</span></td>
                                    <td>
                                        @if($item->is_evaluasi_sent)
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Terkirim</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="fas fa-clock"></i> Belum Dikirim</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning text-dark"><i class="fas fa-paper-plane"></i> Blast Evaluasi</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Tab Sertifikat -->
            <div class="tab-pane fade" id="sertifikat" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <p class="text-muted mb-0">Daftar alumni magang (Selesai) yang belum dikirimkan sertifikat kelulusan.</p>
                </div>
                @if($sertifikat->count() == 0)
                    <div class="alert alert-light text-center py-4">Semua alumni telah menerima sertifikat.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Alumni</th>
                                    <th>Institusi</th>
                                    <th>Selesai Pada</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sertifikat as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->nama }}</strong><br>
                                        <small class="text-muted"><i class="fas fa-envelope"></i> {{ $item->email }}</small>
                                    </td>
                                    <td>{{ $item->nama_institusi }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-success"><i class="fas fa-certificate"></i> Generate & Kirim</button>
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
</div>
@endsection
