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

    /* ── TITLE ── */
    .surat-title {
        text-align: center;
        margin-top: 10px;
        margin-bottom: 25px;
        line-height: 1.2;
    }

    .surat-title .judul {
        font-size: 12pt;
    }
    
    .surat-title .nomor {
        font-size: 12pt;
    }

    /* ── CONTENT ── */
    .content-section {
        margin-bottom: 20px;
    }

    .info-table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 15px;
    }

    .info-table td {
        vertical-align: top;
        padding-bottom: 5px;
    }

    .info-table td:first-child {
        width: 160px; /* Adjust width as needed */
    }

    .info-table td:nth-child(2) {
        width: 15px;
    }

    .paragraph {
        text-align: justify;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    /* ── TTD ── */
    .ttd-block {
        margin-top: 30px;
        float: right;
        width: 300px;
        text-align: center;
    }

    .ttd-tanggal {
        margin-bottom: 2px;
    }

    .ttd-jabatan {
        margin-bottom: 80px;
    }

    .ttd-nama {
        color: #000;
        text-decoration: underline;
    }

    .ttd-nip {
        color: #000;
    }

    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }

    .highlight {
        background-color: yellow;
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

    {{-- ═══ JUDUL SURAT ═══ --}}
    <div class="surat-title">
        <div class="judul">SURAT KETERANGAN</div>
        <div class="nomor">NOMOR: {{ $nomor_surat }}</div>
    </div>

    {{-- ═══ ISI SURAT ═══ --}}
    <div class="content-section">
        <p style="margin-bottom: 10px;">Yang Bertandatangan di bawah ini:</p>
        <table class="info-table">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>Priscka Maharani Hardi</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td>198410082010122003</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>Kepala Subbagian Tata Usaha, Pusat Data Teknologi Informasi,<br>Sekretariat Jenderal, Kementerian Pekerjaan Umum</td>
            </tr>
        </table>
    </div>

    <div class="content-section">
        <p style="margin-bottom: 10px;">Menerangkan dengan sebenarnya bahwa:</p>
        <table class="info-table">
            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $peserta->nama }}</td>
            </tr>
            <tr>
                <td>NIM/NIS</td>
                <td>:</td>
                <td>{{ $peserta->nim_nis ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jurusan</td>
                <td>:</td>
                <td>{{ $peserta->jurusan }}</td>
            </tr>
            <tr>
                <td>Universitas/Sekolah</td>
                <td>:</td>
                <td>{{ $peserta->nama_institusi }}</td>
            </tr>
        </table>
    </div>

    <div class="content-section">
        <p class="paragraph">
            Adalah {{ $sebutan_peserta }} {{ $peserta->nama_institusi }} yang melaksanakan Magang/Praktek Kerja Lapangan (PKL) di {{ $tim_kerja }}, Pusat Data dan Teknologi Informasi, Sekretariat Jenderal, Kementerian Pekerjaan Umum. Yang bersangkutan melaksanakan kegiatan Magang/Praktek Kerja Lapangan (PKL) mulai dari tanggal {{ $tanggal_mulai }} s.d. {{ $tanggal_selesai }}.
        </p>
        <p class="paragraph">
            Demikian surat keterangan ini dibuat untuk digunakan sebagaimana mestinya.
        </p>
    </div>

    {{-- ═══ TTD ═══ --}}
    <div class="ttd-block clearfix">
        <div class="ttd-tanggal">
            Jakarta, {{ $tanggal_terbit }}
        </div>
        <div class="ttd-jabatan">
            Kepala Subbagian Tata Usaha,
        </div>
        <div class="ttd-nama">
            Priscka Maharani Hardi
        </div>
        <div class="ttd-nip">
            NIP. 198410082010122003
        </div>
    </div>

</div>
</body>
</html>
