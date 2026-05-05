@extends('layouts.admin')

@section('title', 'Foto Akses Pintu')

@section('content')

<div style="margin-bottom: 20px; padding: 16px 20px; background: linear-gradient(135deg, var(--primary), var(--primary-light)); border-radius: var(--radius); color: #fff; display: flex; align-items: center; gap: 14px;">
    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
        <i class="fas fa-id-badge"></i>
    </div>
    <div style="flex: 1;">
        <div style="font-weight: 700; font-size: 15px; margin-bottom: 2px;">Foto Akses Pintu Kantor</div>
        <div style="font-size: 12.5px; opacity: 0.85;">Foto pas foto peserta magang aktif & calon peserta untuk keperluan pembuatan kartu akses scan pintu kantor.</div>
    </div>
    <a href="{{ route('admin.foto-akses.index') }}" style="
        background: var(--accent); color: var(--primary-dark);
        padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
        text-decoration: none; display: flex; align-items: center; gap: 6px;
        transition: all 150ms;
    ">
        <i class="fas fa-refresh"></i> Refresh
    </a>
</div>

@php
    $tabs = [
        'aktif'       => ['label' => 'Aktif',       'data' => $grouped['aktif'],       'icon' => 'fa-user-check', 'color' => '#059669'],
        'belum_aktif' => ['label' => 'Belum Aktif', 'data' => $grouped['belum_aktif'], 'icon' => 'fa-hourglass-half', 'color' => '#d97706'],
        'review'      => ['label' => 'Menunggu Review', 'data' => $grouped['review'], 'icon' => 'fa-inbox', 'color' => '#7c3aed'],
    ];
@endphp

@foreach($tabs as $tabId => $tab)
<div class="card-clean" style="margin-bottom: 24px;">
    <div class="card-header-clean">
        <h3>
            <i class="fas {{ $tab['icon'] }}" style="color: {{ $tab['color'] }}; margin-right: 8px;"></i>
            Peserta {{ $tab['label'] }}
        </h3>
        <span class="badge-status {{ $tabId === 'aktif' ? 'badge-aktif' : ($tabId === 'belum_aktif' ? 'badge-belum' : 'badge-review') }}">
            {{ $tab['data']->count() }} orang
        </span>
    </div>

    @if($tab['data']->count() == 0)
        <div class="empty-state">
            <i class="fas {{ $tab['icon'] }}" style="display: block; color: var(--text-muted);"></i>
            <p>Tidak ada peserta dengan status {{ $tab['label'] }}.</p>
        </div>
    @else
        <div style="padding: 20px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px;">
                @foreach($tab['data'] as $item)
                <div style="
                    border: 1px solid var(--border);
                    border-radius: 12px;
                    overflow: hidden;
                    background: #fff;
                    transition: all 150ms ease;
                    cursor: default;
                " onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'; this.style.transform='translateY(-2px)';"
                   onmouseout="this.style.boxShadow='none'; this.style.transform='translateY(0)';">

                    {{-- Foto --}}
                    <div style="position: relative; background: #f8fafc; height: 160px; display: flex; align-items: center; justify-content: center;">
                        @if($item->pas_foto)
                            <img src="{{ asset('storage/' . $item->pas_foto) }}"
                                 alt="Foto {{ $item->nama }}"
                                 style="width: 100%; height: 160px; object-fit: cover; display: block;">
                            {{-- Download overlay --}}
                            <a href="{{ asset('storage/' . $item->pas_foto) }}"
                               download="{{ str_replace(' ', '_', $item->nama) }}_pas_foto"
                               target="_blank"
                               style="
                                position: absolute; inset: 0;
                                background: rgba(30, 58, 138, 0);
                                display: flex; align-items: center; justify-content: center;
                                transition: all 150ms ease;
                                text-decoration: none;
                               "
                               onmouseover="this.style.background='rgba(30,58,138,0.6)'; this.querySelector('.dl-icon').style.opacity='1';"
                               onmouseout="this.style.background='rgba(30,58,138,0)'; this.querySelector('.dl-icon').style.opacity='0';">
                                <span class="dl-icon" style="
                                    opacity: 0; transition: opacity 150ms;
                                    color: #fff; font-size: 13px; font-weight: 600;
                                    display: flex; flex-direction: column; align-items: center; gap: 4px;
                                ">
                                    <i class="fas fa-download" style="font-size: 20px;"></i>
                                    Unduh
                                </span>
                            </a>
                        @else
                            {{-- Placeholder --}}
                            <div style="
                                width: 80px; height: 80px; border-radius: 50%;
                                background: linear-gradient(135deg, var(--primary), var(--primary-light));
                                display: flex; align-items: center; justify-content: center;
                                color: #fff; font-size: 32px;
                            ">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div style="padding: 12px;">
                        <div style="font-weight: 600; font-size: 13px; color: var(--text-primary); margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->nama }}">
                            {{ $item->nama }}
                        </div>
                        <div style="font-size: 11.5px; color: var(--text-secondary); margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $item->nama_institusi }}">
                            {{ $item->nama_institusi }}
                        </div>
                        <div style="font-size: 11px; color: var(--text-muted);">
                            {{ $item->timKerja1->nama_tim ?? '-' }}
                        </div>

                        @if($item->pas_foto)
                        <a href="{{ asset('storage/' . $item->pas_foto) }}"
                           download="{{ str_replace(' ', '_', $item->nama) }}_pas_foto"
                           target="_blank"
                           class="btn-primary-custom btn-sm-custom"
                           style="width: 100%; justify-content: center; text-decoration: none; margin-top: 10px;">
                            <i class="fas fa-download"></i> Unduh
                        </a>
                        @else
                        <div style="
                            margin-top: 10px; padding: 6px; border-radius: 6px;
                            background: #fef2f2; color: #dc2626;
                            font-size: 11.5px; font-weight: 500; text-align: center;
                        ">
                            <i class="fas fa-exclamation-triangle"></i> Foto belum ada
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Tombol Unduh Semua --}}
            @php $hasFoto = $tab['data']->filter(fn($p) => $p->pas_foto)->count(); @endphp
            @if($hasFoto > 0)
            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
                <div style="font-size: 13px; color: var(--text-secondary);">
                    <i class="fas fa-info-circle" style="margin-right: 4px;"></i>
                    {{ $hasFoto }} foto tersedia · Hover foto untuk mengunduh satu per satu
                </div>
            </div>
            @endif
        </div>
    @endif
</div>
@endforeach

@endsection
