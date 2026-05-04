@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3 shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-hourglass-half"></i> Belum Aktif</h6>
                <h2 class="mb-0">{{ $kpi['belum_aktif'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3 shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-user-check"></i> Aktif</h6>
                <h2 class="mb-0">{{ $kpi['aktif'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3 shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-graduation-cap"></i> Selesai</h6>
                <h2 class="mb-0">{{ $kpi['selesai'] }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3 shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-ban"></i> Anulir</h6>
                <h2 class="mb-0">{{ $kpi['anulir'] }}</h2>
            </div>
        </div>
    </div>
</div>

@if($lamaranBaru > 0)
<div class="alert alert-info shadow-sm border-0 d-flex align-items-center mb-4">
    <i class="fas fa-bell fs-4 me-3"></i>
    <div>
        <strong>Pemberitahuan:</strong> Ada <strong>{{ $lamaranBaru }}</strong> lamaran baru yang menunggu untuk direview. 
        <a href="{{ route('admin.lamaran.index') }}" class="alert-link">Lihat lamaran</a>.
    </div>
</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-1"></i> Pemantauan Kuota Tim Kerja</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Bidang</th>
                        <th>Tim Kerja</th>
                        <th class="text-center">Kuota Maksimal</th>
                        <th class="text-center">Peserta Aktif</th>
                        <th class="text-center">Sisa Kuota</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timKerja as $tim)
                    @php
                        $sisa = $tim->kuota_maksimal - $tim->peserta_magang_count;
                        $persentase = $tim->kuota_maksimal > 0 ? ($tim->peserta_magang_count / $tim->kuota_maksimal) * 100 : 0;
                        $bgClass = 'bg-success';
                        if($persentase >= 100) $bgClass = 'bg-danger';
                        elseif($persentase >= 80) $bgClass = 'bg-warning';
                    @endphp
                    <tr>
                        <td>{{ $tim->bidang }}</td>
                        <td>{{ $tim->nama_tim }}</td>
                        <td class="text-center">{{ $tim->kuota_maksimal }}</td>
                        <td class="text-center">{{ $tim->peserta_magang_count }}</td>
                        <td class="text-center fw-bold {{ $sisa <= 0 ? 'text-danger' : 'text-success' }}">
                            {{ $sisa }}
                        </td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar {{ $bgClass }}" role="progressbar" style="width: {{ $persentase }}%;" aria-valuenow="{{ $persentase }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ round($persentase) }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
