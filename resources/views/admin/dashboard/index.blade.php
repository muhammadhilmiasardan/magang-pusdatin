@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
{{-- KPI Cards --}}
<div class="kpi-grid" style="grid-template-columns: repeat(4, 1fr);">
    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Belum Aktif</h4>
            <div class="kpi-value">{{ $kpi['belum_aktif'] }}</div>
        </div>
        <div class="kpi-icon" style="background: #fffbeb; color: #d97706;">
            <i class="fas fa-hourglass-half"></i>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Aktif</h4>
            <div class="kpi-value">{{ $kpi['aktif'] }}</div>
        </div>
        <div class="kpi-icon" style="background: #ecfdf5; color: #059669;">
            <i class="fas fa-user-check"></i>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Selesai</h4>
            <div class="kpi-value">{{ $kpi['selesai'] }}</div>
        </div>
        <div class="kpi-icon" style="background: var(--primary-lighter); color: var(--primary);">
            <i class="fas fa-graduation-cap"></i>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-info">
            <h4>Anulir</h4>
            <div class="kpi-value">{{ $kpi['anulir'] }}</div>
        </div>
        <div class="kpi-icon" style="background: #fff7ed; color: #ea580c;">
            <i class="fas fa-ban"></i>
        </div>
    </div>
</div>

{{-- Notification --}}
@if($kpi['menunggu_review'] > 0)
<div style="
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    border-radius: var(--radius);
    padding: 18px 22px;
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 24px;
    color: #fff;
">
    <div style="
        width: 42px; height: 42px; border-radius: 10px;
        background: rgba(255,255,255,0.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; flex-shrink: 0;
    ">
        <i class="fas fa-bell"></i>
    </div>
    <div style="flex: 1;">
        <div style="font-weight: 600; font-size: 14px; margin-bottom: 2px;">{{ $kpi['menunggu_review'] }} Lamaran Baru</div>
        <div style="font-size: 12.5px; opacity: 0.85;">Ada lamaran yang menunggu untuk direview oleh admin.</div>
    </div>
    <a href="{{ route('admin.lamaran.index') }}" style="
        background: var(--accent); color: var(--primary-dark);
        padding: 8px 18px; border-radius: 8px;
        font-size: 13px; font-weight: 600; text-decoration: none;
        transition: all 150ms ease;
    ">
        <i class="fas fa-arrow-right" style="margin-left: 4px;"></i>
        Lihat Semua
    </a>
</div>
@endif

{{-- Kuota Tim Kerja --}}
<div class="card-clean">
    <div class="card-header-clean">
        <h3><i class="fas fa-chart-pie" style="color: var(--accent); margin-right: 8px;"></i>Pemantauan Kuota Tim Kerja</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="table-clean">
            <thead>
                <tr>
                    <th>Bidang</th>
                    <th>Tim Kerja</th>
                    <th style="text-align: center;">Kuota</th>
                    <th style="text-align: center;">Aktif</th>
                    <th style="text-align: center;">Sisa</th>
                    <th style="width: 160px;">Kapasitas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timKerja as $tim)
                @php
                    $sisa = $tim->kuota_maksimal - $tim->peserta_magang_count;
                    $persentase = $tim->kuota_maksimal > 0 ? ($tim->peserta_magang_count / $tim->kuota_maksimal) * 100 : 0;
                    $fillColor = '#10b981';
                    if($persentase >= 100) $fillColor = '#ef4444';
                    elseif($persentase >= 80) $fillColor = '#f59e0b';
                @endphp
                <tr>
                    <td>
                        <span style="font-size: 12px; color: var(--text-secondary);">{{ $tim->bidang }}</span>
                    </td>
                    <td style="font-weight: 500;">{{ $tim->nama_tim }}</td>
                    <td style="text-align: center;">{{ $tim->kuota_maksimal }}</td>
                    <td style="text-align: center; font-weight: 600;">{{ $tim->peserta_magang_count }}</td>
                    <td style="text-align: center;">
                        <span style="
                            font-weight: 600;
                            color: {{ $sisa <= 0 ? '#ef4444' : ($sisa <= 2 ? '#f59e0b' : '#10b981') }};
                        ">{{ $sisa }}</span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div class="progress-clean" style="flex: 1;">
                                <div class="progress-fill" style="width: {{ min($persentase, 100) }}%; background: {{ $fillColor }};"></div>
                            </div>
                            <span style="font-size: 11px; font-weight: 600; color: var(--text-secondary); min-width: 32px;">{{ round($persentase) }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
