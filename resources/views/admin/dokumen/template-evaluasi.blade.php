<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @page {
        margin: 8.5mm 20mm 10mm 25mm !important;
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
    /* Preview mode: body tidak scroll, page di-scale ke viewport */
    html, body {
        width: 100%;
        height: 100%;
        overflow: hidden;
        margin: 0;
        padding: 0;
        background: #64748b;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .page {
        width: 21cm;
        height: 29.7cm; /* Tinggi fixed A4 agar bisa di-scale */
        padding: 0.85cm 2cm 1cm 2.5cm;
        background: white;
        box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        flex-shrink: 0;
        transform-origin: center center;
    }
    @endif

    /* ── KOP SURAT ── */
    .kop-surat {
        width: 100%;
        margin-bottom: 15px;
        border-bottom: 3px solid #000;
        padding-bottom: 10px;
    }

    /* ── TITLE ── */
    .surat-title {
        text-align: center;
        margin-top: 5px;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    /* ── CONTENT ── */
    .content-section {
        margin-bottom: 10px;
    }

    .info-table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 10px;
    }

    .info-table td {
        vertical-align: top;
        padding-bottom: 2px;
    }

    .info-table td:first-child {
        width: 210px;
    }

    .info-table td:nth-child(2) {
        width: 15px;
    }

    .paragraph {
        text-align: justify;
        margin-bottom: 10px;
        line-height: 1.4;
        text-indent: 40px;
    }

    /* ── TABLE NILAI ── */
    .nilai-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }
    .nilai-table th, .nilai-table td {
        border: 1px solid #000;
        padding: 3px 6px;
        vertical-align: middle;
    }
    .nilai-table th {
        font-weight: normal;
        text-align: center;
    }
    .col-no { width: 40px; text-align: center; }
    .col-nilai { width: 120px; text-align: center; }

    /* ── TTD ── */
    .ttd-wrapper {
        width: 100%;
        margin-top: 30px;
    }
    .ttd-table {
        width: 100%;
        text-align: center;
        border-collapse: collapse;
    }
    .ttd-table td {
        vertical-align: top;
        width: 50%;
    }
    .ttd-space {
        height: 70px;
    }
    .ttd-nama {
        color: #000;
        text-decoration: underline;
    }
</style>
</head>
<body>
<div class="page">

    {{-- ═══ KOP SURAT ═══ --}}
    <table class="kop-surat">
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
        <div>LEMBAR EVALUASI</div>
        <div>PELAKSANAAN MAGANG/PKL</div>
        <div>NOMOR : {{ $nomor_surat }}</div>
    </div>

    {{-- ═══ YTH ═══ --}}
    <div class="content-section">
        <div>Yth,</div>
        <div>Bapak/Ibu {{ $kepada_yth }}</div>
        <div>{{ $peserta->nama_institusi }}</div>
        <div>di Tempat</div>
    </div>

    <div class="content-section">
        <p class="paragraph">
            Dengan mempertimbangkan segala aspek, baik dari segi bobot pekerjaan maupun pelaksanaan Kegiatan Magang/PKL, maka kami memutuskan bahwa yang bersangkutan telah menyelesaikan kewajibannya dengan hasil penilaian sebagai berikut:
        </p>
    </div>

    <div class="content-section">
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
                <td>Prodi/Jurusan</td>
                <td>:</td>
                <td>{{ $peserta->jurusan }}</td>
            </tr>
            <tr>
                <td>Tim Kerja</td>
                <td>:</td>
                <td>{{ $tim_kerja }}</td>
            </tr>
            <tr>
                <td>Periode Magang</td>
                <td>:</td>
                <td>{{ $tanggal_mulai }} s.d. {{ $tanggal_selesai }}</td>
            </tr>
            <tr>
                <td>Nama Pembimbing Lapangan</td>
                <td>:</td>
                <td>{{ $ketua_tim }}</td>
            </tr>
        </table>
    </div>

    {{-- ═══ TABLE NILAI ═══ --}}
    <table class="nilai-table">
        <tr>
            <th colspan="2" style="text-align: left; padding-left: 10px;">A. INTEGRITAS & SIKAP</th>
            <th class="col-nilai">Nilai (8.5 s.d 10)</th>
        </tr>
        <tr>
            <td class="col-no">1</td>
            <td>Kedisiplinan & Ketepatan Waktu</td>
            <td class="col-nilai">{{ $nilai[0] }}</td>
        </tr>
        <tr>
            <td class="col-no">2</td>
            <td>Tanggung Jawab</td>
            <td class="col-nilai">{{ $nilai[1] }}</td>
        </tr>
        <tr>
            <td class="col-no">3</td>
            <td>Kejujuran</td>
            <td class="col-nilai">{{ $nilai[2] }}</td>
        </tr>
        <tr>
            <td class="col-no">4</td>
            <td>Etika & Sopan Santun</td>
            <td class="col-nilai">{{ $nilai[3] }}</td>
        </tr>
        <tr>
            <td colspan="3" style="background: #f0f0f0;"></td>
        </tr>
        <tr>
            <td class="col-no">5</td>
            <td>Kualitas Hasil Kerja</td>
            <td class="col-nilai">{{ $nilai[4] }}</td>
        </tr>
        <tr>
            <td class="col-no">6</td>
            <td>Kemampuan Teknis/Fungsional</td>
            <td class="col-nilai">{{ $nilai[5] }}</td>
        </tr>
        <tr>
            <td class="col-no">7</td>
            <td>Inisiatif dan Kreatifitas</td>
            <td class="col-nilai">{{ $nilai[6] }}</td>
        </tr>
        <tr>
            <td colspan="3" style="background: #f0f0f0;"></td>
        </tr>
        <tr>
            <td class="col-no">8</td>
            <td>Kerjasama Tim</td>
            <td class="col-nilai">{{ $nilai[7] }}</td>
        </tr>
        <tr>
            <td class="col-no">9</td>
            <td>Kemampuan Komunikasi</td>
            <td class="col-nilai">{{ $nilai[8] }}</td>
        </tr>
        <tr>
            <td class="col-no">10</td>
            <td>Kemauan Belajar</td>
            <td class="col-nilai">{{ $nilai[9] }}</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold;">Total Nilai</td>
            <td class="col-nilai" style="font-weight: bold;">{{ number_format($total_nilai, 1) }}</td>
        </tr>
    </table>

    {{-- ═══ TTD ═══ --}}
    <div class="ttd-wrapper">
        <table class="ttd-table">
            <tr>
                <td>
                    <div style="margin-bottom: 5px;">&nbsp;</div>
                    <div>Menyetujui</div>
                    <div>Pembimbing Lapangan,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-nama">{{ $ketua_tim }}</div>
                    <div>NIP. {{ $nip_ketua_tim }}</div>
                </td>
                <td>
                    <div style="margin-bottom: 5px;">Jakarta, {{ $tanggal_terbit }}</div>
                    <div>Mengetahui</div>
                    <div>Kepala Subbagian Tata Usaha,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-nama">Priscka Maharani Hardi</div>
                    <div>NIP. 198410082010122003</div>
                </td>
            </tr>
        </table>
    </div>

</div>
@if(!$is_pdf)
<script>
    (function () {
        function fitEvaluasi() {
            var page = document.querySelector('.page');
            if (!page) return;

            // Ukuran asli A4 Portrait dalam px (21cm x 29.7cm @96dpi)
            // 1cm = 96/2.54 px = 37.795px
            var CM = 37.795;
            var pageW = 21   * CM;  // ~794px
            var pageH = 29.7 * CM;  // ~1122px

            var vw = window.innerWidth;
            var vh = window.innerHeight;

            var scaleX = vw / pageW;
            var scaleY = vh / pageH;
            var scale  = Math.min(scaleX, scaleY) * 0.97; // 3% margin agar tidak terlalu mepet

            page.style.transform = 'scale(' + scale + ')';
        }

        window.addEventListener('load', fitEvaluasi);
        window.addEventListener('resize', fitEvaluasi);

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fitEvaluasi);
        } else {
            fitEvaluasi();
        }
    })();
</script>
@endif
</body>
</html>
