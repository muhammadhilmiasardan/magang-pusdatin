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
                        <th>Dokumen</th>
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
                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                @if($item->cv)
                                    <a href="#" class="btn-outline-custom btn-sm-custom" style="width: fit-content; text-decoration: none;">
                                        <i class="fas fa-file-pdf" style="color: #ef4444;"></i> CV
                                    </a>
                                @endif
                                <a href="#" class="btn-outline-custom btn-sm-custom" style="width: fit-content; text-decoration: none;">
                                    <i class="fas fa-file-pdf" style="color: var(--primary);"></i> Rekomendasi
                                </a>
                            </div>
                        </td>
                        <td>
                            <div style="display: flex; gap: 6px; justify-content: center;">
                                <form action="{{ route('admin.lamaran.terima', $item->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-success-custom btn-sm-custom" title="Terima & Kirim Surat" onclick="return confirm('Terima lamaran ini? Sistem akan mengirimkan Surat Penerimaan.')">
                                        <i class="fas fa-check"></i> Terima
                                    </button>
                                </form>
                                <form action="{{ route('admin.lamaran.tolak', $item->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn-danger-custom btn-sm-custom" title="Tolak" onclick="return confirm('Tolak lamaran ini?')">
                                        <i class="fas fa-times"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
