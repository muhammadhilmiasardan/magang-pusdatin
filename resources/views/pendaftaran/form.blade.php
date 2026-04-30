@extends('layouts.app')

@section('content')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">Formulir Pendaftaran Magang PUSDATIN PUPR</h4>
                </div>
                <div class="card-body p-4">
                    
                    <form action="{{ route('pendaftaran.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <h5 class="text-primary border-bottom pb-2 mb-3">A. Data Diri & Institusi</h5>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tingkat Pendidikan <span class="text-danger">*</span></label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tingkat_pendidikan" value="Universitas" id="univ" {{ old('tingkat_pendidikan') == 'Universitas' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="univ">Universitas</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tingkat_pendidikan" value="SMK" id="smk" {{ old('tingkat_pendidikan') == 'SMK' ? 'checked' : '' }}>
                                <label class="form-check-label" for="smk">SMK</label>
                            </div>
                            @error('tingkat_pendidikan') <small class="text-danger d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Institusi (Kampus/Sekolah) <span class="text-danger">*</span></label>
                                <input type="text" name="nama_institusi" class="form-control @error('nama_institusi') is-invalid @enderror" value="{{ old('nama_institusi') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jurusan / Program Studi <span class="text-danger">*</span></label>
                                <input type="text" name="jurusan" class="form-control @error('jurusan') is-invalid @enderror" value="{{ old('jurusan') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai Magang <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" value="{{ old('tanggal_mulai') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai Magang <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" value="{{ old('tanggal_selesai') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor Telepon (WhatsApp) <span class="text-danger">*</span></label>
                            <input type="tel" name="nomor_telp" class="form-control @error('nomor_telp') is-invalid @enderror" value="{{ old('nomor_telp') }}" placeholder="08..." required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="email.pribadi@gmail.com" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Institusi <span class="text-danger">*</span></label>
                                <input type="email" name="email_institusi" class="form-control @error('email_institusi') is-invalid @enderror" value="{{ old('email_institusi') }}" placeholder="nama@kampus.ac.id" required>
                            </div>
                        </div>

                        <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">B. Penempatan & Dokumen</h5>

                        <div class="mb-3">
                            <label class="form-label">Pilih Bidang <span class="text-danger">*</span></label>
                            <select id="select-bidang" name="bidang" class="form-select @error('bidang') is-invalid @enderror" onchange="updateTimKerja()" required>
                                <option value="" disabled selected>-- Pilih Bidang --</option>
                                @foreach($groupedTimKerja as $bidang => $tims)
                                    <option value="{{ $bidang }}" {{ old('bidang') == $bidang ? 'selected' : '' }}>
                                        {{ $bidang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pilihan Tim Kerja 1 <span class="text-danger">*</span></label>
                                <select id="select-tim-kerja-1" name="id_tim_kerja_1" class="form-select @error('id_tim_kerja_1') is-invalid @enderror" onchange="syncTimKerjaDropdowns()" required disabled>
                                    <option value="" disabled selected>-- Pilih Bidang Dahulu --</option>
                                </select>
                                @error('id_tim_kerja_1') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Pilihan Tim Kerja 2 <span class="text-danger">*</span></label>
                                <select id="select-tim-kerja-2" name="id_tim_kerja_2" class="form-select @error('id_tim_kerja_2') is-invalid @enderror" onchange="syncTimKerjaDropdowns()" required disabled>
                                    <option value="" disabled selected>-- Pilih Bidang Dahulu --</option>
                                </select>
                                @error('id_tim_kerja_2') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="alert alert-info py-2 mb-3" role="alert">
                            <small><i class="fas fa-info-circle"></i> Pilih 2 tim kerja yang berbeda sebagai prioritas penempatan Anda.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Curriculum Vitae (CV) <span class="text-muted">- Opsional</span></label>
                            <input type="file" name="cv" class="form-control @error('cv') is-invalid @enderror" accept="application/pdf">
                            <small class="text-muted">Format .pdf maksimal 2MB.</small>
                            @error('cv') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Surat Rekomendasi Institusi <span class="text-danger">*</span></label>
                            <input type="file" name="surat_rekomendasi" class="form-control @error('surat_rekomendasi') is-invalid @enderror" accept="application/pdf" required>
                            <small class="text-muted">Format .pdf maksimal 2MB. Wajib dilampirkan.</small>
                            @error('surat_rekomendasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Kirim Lamaran Magang</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Data tim kerja dari database, di-render oleh Blade
    const timKerjaData = {
        @foreach($groupedTimKerja as $bidang => $tims)
        "{{ $bidang }}": [
            @foreach($tims as $tim)
            @php 
                $sisa = $tim->kuota_maksimal - $tim->peserta_magang_count; 
            @endphp
            {
                id: "{{ $tim->id }}",
                nama: "{{ $tim->nama_tim }}",
                sisa: {{ $sisa }},
                is_full: {{ $sisa <= 0 ? 'true' : 'false' }}
            },
            @endforeach
        ],
        @endforeach
    };

    /**
     * Dipanggil saat Bidang berubah.
     * Mengisi kedua dropdown Tim Kerja dengan data sesuai bidang.
     */
    function updateTimKerja() {
        const selectedBidang = document.getElementById('select-bidang').value;
        const timSelect1 = document.getElementById('select-tim-kerja-1');
        const timSelect2 = document.getElementById('select-tim-kerja-2');
        const oldTim1 = "{{ old('id_tim_kerja_1') }}";
        const oldTim2 = "{{ old('id_tim_kerja_2') }}";

        // Reset kedua dropdown
        timSelect1.innerHTML = '<option value="" disabled selected>-- Pilih Tim Kerja 1 --</option>';
        timSelect2.innerHTML = '<option value="" disabled selected>-- Pilih Tim Kerja 2 --</option>';

        if (selectedBidang && timKerjaData[selectedBidang]) {
            timSelect1.disabled = false;
            timSelect2.disabled = false;

            timKerjaData[selectedBidang].forEach(tim => {
                // Buat option untuk dropdown 1
                let opt1 = document.createElement('option');
                opt1.value = tim.id;
                opt1.text = tim.is_full ? tim.nama + ' (Penuh)' : tim.nama + ' (Sisa: ' + tim.sisa + ')';
                if (tim.is_full) opt1.disabled = true;
                if (oldTim1 == tim.id) opt1.selected = true;
                timSelect1.appendChild(opt1);

                // Buat option untuk dropdown 2
                let opt2 = document.createElement('option');
                opt2.value = tim.id;
                opt2.text = tim.is_full ? tim.nama + ' (Penuh)' : tim.nama + ' (Sisa: ' + tim.sisa + ')';
                if (tim.is_full) opt2.disabled = true;
                if (oldTim2 == tim.id) opt2.selected = true;
                timSelect2.appendChild(opt2);
            });

            // Sinkronkan agar tidak bisa pilih tim yang sama
            syncTimKerjaDropdowns();
        } else {
            timSelect1.disabled = true;
            timSelect2.disabled = true;
        }
    }

    /**
     * Sinkronisasi kedua dropdown agar tidak bisa memilih tim kerja yang sama.
     * Tim yang sudah dipilih di dropdown 1 akan di-disable di dropdown 2, dan sebaliknya.
     */
    function syncTimKerjaDropdowns() {
        const timSelect1 = document.getElementById('select-tim-kerja-1');
        const timSelect2 = document.getElementById('select-tim-kerja-2');
        const val1 = timSelect1.value;
        const val2 = timSelect2.value;
        const selectedBidang = document.getElementById('select-bidang').value;

        if (!selectedBidang || !timKerjaData[selectedBidang]) return;

        // Update disabled state di dropdown 2 berdasarkan pilihan dropdown 1
        Array.from(timSelect2.options).forEach(opt => {
            if (opt.value === "") return; // Skip placeholder
            const tim = timKerjaData[selectedBidang].find(t => t.id === opt.value);
            // Disable jika sudah dipilih di dropdown 1 ATAU kuota penuh
            opt.disabled = (opt.value === val1) || (tim && tim.is_full);
        });

        // Update disabled state di dropdown 1 berdasarkan pilihan dropdown 2
        Array.from(timSelect1.options).forEach(opt => {
            if (opt.value === "") return; // Skip placeholder
            const tim = timKerjaData[selectedBidang].find(t => t.id === opt.value);
            // Disable jika sudah dipilih di dropdown 2 ATAU kuota penuh
            opt.disabled = (opt.value === val2) || (tim && tim.is_full);
        });
    }

    // Jalankan saat halaman di-load untuk menangani old() input setelah validasi error
    document.addEventListener("DOMContentLoaded", function() {
        if (document.getElementById('select-bidang').value !== "") {
            updateTimKerja();
        }
    });
</script>
@endsection