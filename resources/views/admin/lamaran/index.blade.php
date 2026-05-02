@extends('layouts.admin')

@section('title', 'Lamaran Masuk (Review Pipeline)')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-inbox me-1"></i> Menunggu Review</h6>
        <span class="badge bg-primary">{{ $lamaran->count() }} Lamaran</span>
    </div>
    <div class="card-body">
        @if($lamaran->count() == 0)
            <div class="text-center py-5 text-muted">
                <i class="fas fa-check-circle fs-1 mb-3"></i>
                <p>Tidak ada lamaran baru yang menunggu review.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Pelamar</th>
                            <th>Institusi</th>
                            <th>Periode Magang</th>
                            <th>Pilihan Tim Kerja</th>
                            <th>Dokumen</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lamaran as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->nama }}</strong><br>
                                <small class="text-muted">{{ $item->email }} | {{ $item->nomor_telp }}</small>
                            </td>
                            <td>
                                {{ $item->nama_institusi }}<br>
                                <small class="text-muted">{{ $item->jurusan }} ({{ $item->tingkat_pendidikan }})</small>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }} - <br>
                                {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td>
                                <span class="badge bg-secondary mb-1">1: {{ $item->timKerja1->nama_tim ?? '-' }}</span><br>
                                <span class="badge bg-secondary">2: {{ $item->timKerja2->nama_tim ?? '-' }}</span>
                            </td>
                            <td>
                                @if($item->cv)
                                    <a href="#" class="btn btn-sm btn-outline-info mb-1"><i class="fas fa-file-pdf"></i> CV</a><br>
                                @endif
                                <a href="#" class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf"></i> Surat Rekomendasi</a>
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.lamaran.terima', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Terima & Kirim Surat" onclick="return confirm('Terima lamaran ini? Sistem akan mengirimkan Surat Penerimaan.')">
                                        <i class="fas fa-check"></i> Terima
                                    </button>
                                </form>
                                <form action="{{ route('admin.lamaran.tolak', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" title="Tolak" onclick="return confirm('Tolak lamaran ini?')">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
