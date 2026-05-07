<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    @@page {
        size: A4 landscape;
        margin: 0mm;
    }

    @if($font_corsiva_base64)
    @font-face {
        font-family: 'Monotype Corsiva';
        src: url('{{ $font_corsiva_base64 }}') format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    @endif

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        margin: 0 !important;
        padding: 0 !important;
        background: #fff;
        font-family: 'Times New Roman', Times, serif;
        color: #000;
    }

    .page-sertifikat {
        width: 29.7cm;
        height: 21cm;
        position: relative;
        overflow: hidden;
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
    .page-sertifikat {
        /* Scale diterapkan via JS agar responsif */
        transform-origin: center center;
        box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        flex-shrink: 0;
    }
@endif


    /* ─── BINGKAI GOLD — full bleed ─── */
    .bingkai {
        position: absolute;
        top: 0; left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        /* object-fit bukan milik dompdf, pakai width/height 100% cukup */
    }

    /* ─── LOGO PUSDATIN (kiri atas) ─── */
    .logo-pusdatin {
        position: absolute;
        top: 1.0cm;
        left: 1.0cm;
        width: 7.0cm; /* Diperbesar sesuai permintaan */
        height: auto;
        z-index: 10;
    }

    /* ─── LOGO PU (tengah atas) ─── */
    .logo-pu {
        position: absolute;
        top: 1.8cm; /* Diturunkan lagi agar lebih dekat ke teks header */
        left: 0;
        width: 100%;
        text-align: center;
        z-index: 10;
    }
    .logo-pu img {
        width: 2.3cm;
        height: auto;
    }

    /* ─── LOGO BWK (kanan atas) ─── */
    .logo-bwk {
        position: absolute;
        top: 0.8cm;
        right: 1.0cm;
        width: 3.5cm; /* Diperbesar dari 2.8cm */
        height: auto;
        z-index: 10;
    }

    /* ─── HEADER TEKS ─── */
    /* Font: Canva Sans 14 Bold → dompdf pakai Arial Bold */
    .header-instansi {
        position: absolute;
        top: 4.5cm; /* Dinaikkan sedikit dari 4.8cm */
        left: 0;
        width: 100%;
        text-align: center;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 13.5pt;
        font-weight: bold;
        line-height: 1.4;
        letter-spacing: 0.01em;
        z-index: 10;
    }

    /* ─── SERTIFIKAT ─── */
    /* Canva: top=7.2cm */
    .cert-title {
        position: absolute;
        top: 7.0cm;
        left: 0;
        width: 100%;
        text-align: center;
        font-family: 'Times New Roman', Times, serif;
        font-size: 22pt;
        font-weight: bold;
        letter-spacing: 0.1em;
        z-index: 10;
    }

    /* ─── NOMOR SERTIFIKAT ─── */
    /* Canva: top=8.4cm */
    .cert-nomor {
        position: absolute;
        top: 8.2cm;
        left: 0;
        width: 100%;
        text-align: center;
        font-family: 'Times New Roman', Times, serif;
        font-size: 13pt;
        font-weight: normal;
        z-index: 10;
    }

    /* ─── DIBERIKAN KEPADA ─── */
    /* Canva: top=9.5cm */
    .cert-diberikan {
        position: absolute;
        top: 9.35cm;
        left: 0;
        width: 100%;
        text-align: center;
        font-family: 'Times New Roman', Times, serif;
        font-size: 13pt;
        font-weight: normal;
        font-style: italic;
        z-index: 10;
    }

    /* ─── NAMA LENGKAP (Monotype Corsiva) ─── */
    /* Canva: top=10.3cm */
    .cert-nama {
        position: absolute;
        top: 10.1cm;
        left: 0;
        width: 100%;
        text-align: center;
        font-family: 'Monotype Corsiva', 'URW Chancery L', cursive;
        font-size: 28pt; /* Sedikit diperbesar */
        font-weight: normal;
        /* Hapus font-style: italic; agar domPDF tidak mencari file font italic khusus yang tidak ada */
        z-index: 10;
    }

    /* ─── ATAS PARTISIPASINYA ─── */
    /* Canva: top=11.4cm */
    .cert-partisipasi {
        position: absolute;
        top: 11.8cm; /* Digeser ke bawah agar tidak menabrak nama */
        left: 0;
        width: 100%;
        text-align: center;
        font-family: 'Times New Roman', Times, serif;
        font-size: 13pt;
        font-weight: bold;
        letter-spacing: 0.07em;
        z-index: 10;
    }

    /* ─── MAGANG/PRAKTEK KERJA LAPANGAN ─── */
    /* Canva: top=12.4cm */
    .cert-jenis {
        position: absolute;
        top: 12.5cm; /* Digeser ke bawah mengikuti partisipasi */
        left: 0;
        width: 100%;
        text-align: center;
        font-family: 'Times New Roman', Times, serif;
        font-size: 18pt;
        font-weight: bold;
        z-index: 10;
    }

    /* ─── CAPTION (kalimat panjang 3 baris) ─── */
    /* Canva: top=13.6cm */
    .cert-caption {
        position: absolute;
        top: 13.7cm; /* Digeser ke bawah menyesuaikan spacing */
        left: 3.2cm;
        right: 3.2cm;
        text-align: center;
        font-family: 'Times New Roman', Times, serif;
        font-size: 13pt;
        font-weight: normal;
        line-height: 1.55;
        z-index: 10;
    }

    /* ─── BLOK TTD (lokasi, tanggal, jabatan) ─── */
    .cert-ttd {
        position: absolute;
        top: 15.3cm;
        left: 17.0cm; /* Digeser kiri agar ruang cukup untuk 1 baris */
        right: 1.5cm;
        text-align: center;
        font-family: 'Times New Roman', Times, serif;
        font-size: 12.5pt;
        font-weight: normal;
        line-height: 1.7;
        z-index: 10;
    }

    /* ─── NAMA PENANDATANGAN ─── */
    .cert-ttd-nama {
        position: absolute;
        top: 19.5cm;
        left: 17.0cm;
        right: 1.5cm;
        text-align: center;
        font-family: 'Times New Roman', Times, serif;
        font-size: 12.5pt;
        font-weight: normal;
        text-decoration: underline;
        z-index: 10;
    }
</style>
</head>
<body>
<div class="page-sertifikat">

    {{-- ═══ BINGKAI GOLD (full background) ═══ --}}
    <img class="bingkai" src="{{ $bg_bingkai_base64 }}" alt="">

    {{-- ═══ LOGO PUSDATIN (kiri atas) ═══ --}}
    <img class="logo-pusdatin" src="{{ $logo_pusdatin_base64 }}" alt="Logo PUSDATIN">

    {{-- ═══ LOGO PU (tengah — dibungkus div full-width agar bisa text-align center) ═══ --}}
    <div class="logo-pu">
        <img src="{{ $logo_pu_base64 }}" alt="Logo PU">
    </div>

    {{-- ═══ LOGO BWK / Zona Integritas (kanan atas) ═══ --}}
    <img class="logo-bwk" src="{{ $logo_bwk_base64 }}" alt="Logo WBK">

    {{-- ═══ HEADER INSTANSI ═══ --}}
    <div class="header-instansi">
        PUSAT DATA DAN TEKNOLOGI INFORMASI<br>
        SEKRETARIAT JENDERAL<br>
        KEMENTERIAN PEKERJAAN UMUM
    </div>

    {{-- ═══ JUDUL ═══ --}}
    <div class="cert-title">SERTIFIKAT</div>

    {{-- ═══ NOMOR ═══ --}}
    <div class="cert-nomor">NOMOR {{ $nomor_sertifikat }}</div>

    {{-- ═══ DIBERIKAN KEPADA ═══ --}}
    <div class="cert-diberikan">Diberikan Kepada:</div>

    {{-- ═══ NAMA ═══ --}}
    <div class="cert-nama">{{ $peserta->nama }}</div>

    {{-- ═══ ATAS PARTISIPASINYA ═══ --}}
    <div class="cert-partisipasi">Atas partisipasinya sebagai Peserta</div>

    {{-- ═══ JENIS MAGANG ═══ --}}
    <div class="cert-jenis">Magang/Praktek Kerja Lapangan</div>

    {{-- ═══ CAPTION ═══ --}}
    <div class="cert-caption">
        {{ $sebutan_peserta }} yang bersangkutan telah berhasil menyelesaikan Program Magang/Praktek Kerja Lapangan
        di Pusat Data dan Teknologi Informasi, Sekretariat Jenderal, Kementerian Pekerjaan Umum
        selama periode {{ $tanggal_mulai }} s.d. {{ $tanggal_selesai }} dengan Predikat <strong>{{ $predikat }}</strong>
    </div>

    {{-- ═══ TTD JABATAN ═══ --}}
    <div class="cert-ttd">
        Jakarta, {{ $tanggal_terbit }}<br>
        Kepala Pusat Data dan Teknologi Informasi,
    </div>

    {{-- ═══ NAMA PENANDATANGAN ═══ --}}
    <div class="cert-ttd-nama">Komang Sri Hartini</div>

</div>
@if(!$is_pdf)
<script>
    (function () {
        function fitSertifikat() {
            var page = document.querySelector('.page-sertifikat');
            if (!page) return;

            // Ukuran asli sertifikat dalam px (29.7cm x 21cm @96dpi)
            // 1cm = 96/2.54 px = 37.795px
            var CM = 37.795;
            var pageW = 29.7 * CM;  // ~1122px
            var pageH = 21   * CM;  // ~794px

            var vw = window.innerWidth;
            var vh = window.innerHeight;

            var scaleX = vw / pageW;
            var scaleY = vh / pageH;
            var scale  = Math.min(scaleX, scaleY) * 0.97; // 3% margin

            page.style.transform = 'scale(' + scale + ')';
        }

        // Jalankan setelah semua gambar base64 selesai di-render
        window.addEventListener('load', fitSertifikat);
        window.addEventListener('resize', fitSertifikat);

        // Fallback: jalankan lebih awal (DOM ready)
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fitSertifikat);
        } else {
            fitSertifikat();
        }
    })();
</script>
@endif
</body>
</html>
