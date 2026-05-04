@extends('layouts.admin')

@section('title', 'Pusat Dokumen')

@section('content')
<div class="card-clean">
    {{-- Tab Navigation --}}
    <div class="tab-nav-clean" role="tablist">
        <a class="tab-item active" href="#tab-sk" data-tab="sk" onclick="switchTab('sk', event)">
            <i class="fas fa-file-contract" style="font-size: 13px;"></i>
            SK Magang
            <span class="tab-count">{{ $skMagang->count() }}</span>
        </a>
        <a class="tab-item" href="#tab-evaluasi" data-tab="evaluasi" onclick="switchTab('evaluasi', event)">
            <i class="fas fa-paper-plane" style="font-size: 13px;"></i>
            Evaluasi (H-7)
            <span class="tab-count">{{ $evaluasi->count() }}</span>
        </a>
        <a class="tab-item" href="#tab-sertifikat" data-tab="sertifikat" onclick="switchTab('sertifikat', event)">
            <i class="fas fa-certificate" style="font-size: 13px;"></i>
            Sertifikat
            <span class="tab-count">{{ $sertifikat->count() }}</span>
        </a>
    </div>

    {{-- ═══ TAB 1: SK MAGANG ═══ --}}
    <div class="tab-panel" id="tab-sk" role="tabpanel">
        <div style="padding: 16px 22px 8px;">
            <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">
                Daftar peserta aktif untuk penerbitan Surat Keterangan (SK) Magang.
            </p>
        </div>

        @if($skMagang->count() == 0)
            <div class="empty-state">
                <i class="fas fa-file-contract" style="display: block;"></i>
                <p>Tidak ada data peserta aktif.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Nama Peserta</th>
                            <th>Institusi</th>
                            <th>Periode Magang</th>
                            <th>Status SK</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($skMagang as $item)
                        <tr>
                            <td style="font-weight: 600;">{{ $item->nama }}</td>
                            <td>{{ $item->nama_institusi }}</td>
                            <td style="font-size: 12.5px; white-space: nowrap;">
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}
                                <span style="color: var(--text-muted);"> s/d </span>
                                {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}
                            </td>
                            <td>
                                @if($item->is_sk_sent)
                                    <span class="badge-status badge-sent"><i class="fas fa-check"></i> Terkirim</span>
                                @else
                                    <span class="badge-status badge-pending"><i class="fas fa-clock"></i> Belum Dikirim</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button class="btn-primary-custom btn-sm-custom">
                                    <i class="fas fa-envelope"></i> Kirim SK
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ═══ TAB 2: EVALUASI H-7 ═══ --}}
    <div class="tab-panel hidden" id="tab-evaluasi" role="tabpanel">
        <div style="
            margin: 16px 22px 0;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
        ">
            <div style="
                width: 36px; height: 36px; border-radius: 8px;
                background: var(--accent); color: var(--primary-dark);
                display: flex; align-items: center; justify-content: center;
                font-size: 15px; flex-shrink: 0;
            ">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div>
                <div style="font-size: 13px; font-weight: 600; color: #92400e;">Perhatian</div>
                <div style="font-size: 12.5px; color: #a16207;">
                    Menampilkan peserta yang masa magangnya akan berakhir dalam <strong>7 s/d 14 hari ke depan</strong>.
                    Segera kirim Lembar Evaluasi ke email Penanggung Jawab Institusi.
                </div>
            </div>
        </div>

        @if($evaluasi->count() == 0)
            <div class="empty-state">
                <i class="fas fa-calendar-check" style="display: block;"></i>
                <p>Tidak ada peserta yang mendekati akhir masa magang (H-7 s/d H-14).</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Nama Peserta</th>
                            <th>Institusi</th>
                            <th>Tgl Selesai</th>
                            <th>Sisa Waktu</th>
                            <th>Status</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluasi as $item)
                        @php
                            $sisaHari = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($item->tanggal_selesai), false);
                        @endphp
                        <tr>
                            <td style="font-weight: 600;">{{ $item->nama }}</td>
                            <td>
                                <div>{{ $item->nama_institusi }}</div>
                                <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 2px;">
                                    <i class="fas fa-envelope" style="font-size: 10px;"></i> {{ $item->email_institusi }}
                                </div>
                            </td>
                            <td style="font-size: 13px; white-space: nowrap;">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</td>
                            <td>
                                <span style="
                                    font-weight: 700; font-size: 14px;
                                    color: {{ $sisaHari <= 7 ? '#ef4444' : '#f59e0b' }};
                                ">{{ $sisaHari }} Hari</span>
                            </td>
                            <td>
                                @if($item->is_evaluasi_sent)
                                    <span class="badge-status badge-sent"><i class="fas fa-check"></i> Terkirim</span>
                                @else
                                    <span class="badge-status badge-pending"><i class="fas fa-clock"></i> Belum</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button class="btn-accent-custom btn-sm-custom">
                                    <i class="fas fa-paper-plane"></i> Blast Evaluasi
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ═══ TAB 3: SERTIFIKAT ═══ --}}
    <div class="tab-panel hidden" id="tab-sertifikat" role="tabpanel">
        <div style="padding: 16px 22px 8px;">
            <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">
                Daftar alumni magang (Selesai) yang belum mendapatkan sertifikat kelulusan.
            </p>
        </div>

        @if($sertifikat->count() == 0)
            <div class="empty-state">
                <i class="fas fa-award" style="display: block;"></i>
                <p>Semua alumni telah menerima sertifikat. 🎉</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="table-clean">
                    <thead>
                        <tr>
                            <th>Nama Alumni</th>
                            <th>Institusi</th>
                            <th>Selesai Pada</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sertifikat as $item)
                        <tr>
                            <td>
                                <div style="font-weight: 600;">{{ $item->nama }}</div>
                                <div style="font-size: 11.5px; color: var(--text-secondary); margin-top: 2px;">
                                    <i class="fas fa-envelope" style="font-size: 10px;"></i> {{ $item->email }}
                                </div>
                            </td>
                            <td>{{ $item->nama_institusi }}</td>
                            <td style="font-size: 13px; white-space: nowrap;">{{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</td>
                            <td style="text-align: center;">
                                <button class="btn-success-custom btn-sm-custom">
                                    <i class="fas fa-certificate"></i> Generate & Kirim
                                </button>
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

@push('styles')
<style>
    .hidden { display: none !important; }
</style>
@endpush

@push('scripts')
<script>
    function switchTab(tabId, e) {
        e.preventDefault();
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
        e.currentTarget.classList.add('active');
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById('tab-' + tabId).classList.remove('hidden');
    }
</script>
@endpush
