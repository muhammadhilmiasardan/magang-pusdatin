<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @page {
        margin: 8.5mm 20mm 10mm 25mm !important; /* Top 0.85cm, Right 2cm, Bottom 1cm, Left 2.5cm */
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        margin: 0 !important;
        padding: 0 !important;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11pt;
        line-height: 1.3;
        color: #000;
        background: #fff;
    }

    .page {
        width: 100%;
        background: #fff;
    }

    @if(!$is_pdf)
    /* Preview di Browser */
    body {
        background: #e2e8f0;
        display: flex;
        justify-content: center;
        padding: 2rem;
    }
    .page {
        width: 21cm;
        min-height: 29.7cm;
        padding: 0.85cm 2cm 1cm 2.5cm;
        background: white;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    @endif
    /* ── TUJUAN SURAT ── */
    .tujuan-surat {
        margin-bottom: 24px;
    }

    /* ── PARAGRAF ── */
    .paragraf-indent {
        text-align: justify;
        margin-bottom: 12px;
        text-indent: 40px;
    }

    .list-numbered {
        margin-left: 20px;
        margin-bottom: 12px;
        text-align: justify;
    }
    
    .list-numbered li {
        margin-bottom: 8px;
        padding-left: 5px;
    }

    .paragraf-normal {
        text-align: justify;
        margin-bottom: 12px;
        text-indent: 40px;
    }

    /* ── TABEL DATA PESERTA ── */
    .tabel-peserta {
        width: 95%;
        margin: 16px auto;
        border-collapse: collapse;
        text-align: center;
    }

    .tabel-peserta th, .tabel-peserta td {
        border: 1px solid #000;
        padding: 6px 4px;
        vertical-align: middle;
    }

    .tabel-peserta th {
        font-weight: bold;
    }

    /* ── TTD ── */
    .ttd-block {
        margin-top: 30px;
        float: right;
        width: 300px;
        text-align: center;
    }

    .ttd-jabatan {
        margin-bottom: 110px;
    }

    .ttd-nama {
        color: #000;
        font-weight: bold; /* Often names in signatures are bold, or we just leave it normal. Let's leave it normal if we don't know, but #000 is required */
    }

    .clearfix::after {
        content: '';
        display: table;
        clear: both;
    }
</style>
</head>
<body>
<div class="page">

    {{-- ═══ KOP SURAT ═══ --}}
    <table style="width: 100%; margin-bottom: 24px; border-bottom: 3px solid #000; padding-bottom: 10px;">
        <tr>
            <td style="width: 110px; vertical-align: middle;">
                <img src="{{ $logo_base64 }}" alt="Logo PUPR" style="width: 100px; height: auto;">
            </td>
            <td style="vertical-align: middle; text-align: center;">
                <div style="font-size: 14pt; text-transform: uppercase; color: #333;">KEMENTERIAN PEKERJAAN UMUM</div>
                <div style="font-size: 14pt; text-transform: uppercase; color: #333;">SEKRETARIAT JENDERAL</div>
                <div style="font-size: 16pt; font-weight: bold; text-transform: uppercase; margin: 2px 0;">PUSAT DATA DAN TEKNOLOGI INFORMASI</div>
                <div style="font-size: 8pt; color: #333; white-space: nowrap;">
                    Jl. Pattimura Nomor 20, Kebayoran Baru, Jakarta 12110, Telepon (021) 7392262, surel <a href="mailto:pusdatin@pu.go.id" style="color: #0000FF; text-decoration: none;">pusdatin@pu.go.id</a>
                </div>
            </td>
        </tr>
    </table>

    {{-- ═══ HEADER INFO ═══ --}}
    <table style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: top; width: 60%;">
                <table style="border-collapse: collapse;">
                    <tr><td style="width: 80px; vertical-align: top; padding-bottom: 2px;">Nomor</td><td style="width: 15px; vertical-align: top;">:</td><td style="vertical-align: top;">{{ $nomor_surat }}</td></tr>
                    <tr><td style="vertical-align: top; padding-bottom: 2px;">Sifat</td><td style="vertical-align: top;">:</td><td style="vertical-align: top;">Biasa</td></tr>
                    <tr><td style="vertical-align: top; padding-bottom: 2px;">Lampiran</td><td style="vertical-align: top;">:</td><td style="vertical-align: top;">Satu set</td></tr>
                    <tr><td style="vertical-align: top; padding-bottom: 2px;">Hal</td><td style="vertical-align: top;">:</td><td style="vertical-align: top;">Konfirmasi Kerja Praktik di Lingkungan<br>Pusat Data dan Teknologi Informasi</td></tr>
                </table>
            </td>
            <td style="vertical-align: top; width: 40%; text-align: right;">
                Jakarta, {{ $tanggal_terbit }}
            </td>
        </tr>
    </table>

    {{-- ═══ TUJUAN SURAT ═══ --}}
    <div class="tujuan-surat">
        Yth. {{ $yth }}<br>
        {{ $peserta->nama_institusi }}<br>
        di Tempat
    </div>

    {{-- ═══ BODY ═══ --}}
    <p class="paragraf-indent">
        Kami mengucapkan terima kasih atas kepercayaan yang diberikan kepada Pusat Data 
        dan Teknologi Informasi dalam upaya mendukung pembinaan kerja Praktik {{ $sebutan_peserta }} 
        {{ $peserta->nama_institusi }}. Menindaklanjuti surat {{ $yth }}, {{ $peserta->nama_institusi }} 
        nomor {{ $nomor_surat_univ }} tanggal {{ $tanggal_surat_lamaran }} hal Permohonan Izin Kerja Praktik, 
        bersama ini kami sampaikan beberapa hal sebagai berikut:
    </p>

    <ol class="list-numbered">
        <li>
            PUSDATIN berkenan memberikan izin kepada {{ strtolower($sebutan_peserta) }} yang tertera dibawah ini untuk 
            melakukan Praktik Kerja Lapangan/Magang pada {{ $tim_ditempatkan ? $tim_ditempatkan->nama_tim : '-' }}, 
            {{ $tim_ditempatkan ? $tim_ditempatkan->bidang : '-' }}, Pusdatin Kementerian Pekerjaan Umum 
            terhitung tanggal {{ $tanggal_mulai_fmt }} s.d. {{ $tanggal_selesai_fmt }} dengan data sebagai berikut :
        </li>
    </ol>

    <table class="tabel-peserta">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">NIM/NIS</th>
                <th style="width: 30%;">Nama</th>
                <th style="width: 20%;">Program Studi</th>
                <th style="width: 20%;">No. Telepon</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $peserta->nim_nis ?? '-' }}</td>
                <td>{{ $peserta->nama }}</td>
                <td>{{ $peserta->jurusan }}</td>
                <td>{{ $peserta->nomor_telp }}</td>
            </tr>
        </tbody>
    </table>

    <ol class="list-numbered" start="2">
        <li>
            Kami berharap dapat memberikan pengalaman yang bermanfaat bagi {{ strtolower($sebutan_peserta) }} yang 
            bersangkutan dan mendukung proses pembelajaran akademik;
        </li>
        <li>
            Kami menugaskan Sdr. Deny (HP. 0896 4900 7440) sebagai narahubung apabila terdapat 
            hal-hal yang perlu didiskusikan.
        </li>
    </ol>

    <p class="paragraf-normal">
        Selanjutnya, dalam menunjang pembangunan Zona Integritas menuju Wilayah Birokrasi 
        Bersih dan Melayani (WBBM), PUSDATIN Kementerian PU berkomitmen meningkatkan kualitas 
        pelayanan publik yang bebas dari korupsi dan memberikan pelayanan prima.
    </p>

    <p class="paragraf-normal" style="text-indent: 40px;">
        Demikian kami sampaikan. Atas perhatian dan kerja samanya diucapkan terima kasih.
    </p>

    {{-- ═══ TTD ═══ --}}
    <div class="ttd-block clearfix">
        <div class="ttd-jabatan">
            Kepala Pusat Data dan Teknologi Informasi
        </div>
        <div class="ttd-nama" style="font-weight: normal; color: #000;">
            Komang Sri Hartini
        </div>
    </div>

</div>
</body>
</html>
