<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #1a1a1a; background: #f0f2f5; }
    .wrapper { max-width: 620px; margin: 24px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }

    /* Header */
    .header { background: linear-gradient(135deg, #1a3a6e 0%, #2d5bab 100%); padding: 20px 28px; }
    .header-inner { display: table; width: 100%; }
    .header-logo-cell { display: table-cell; vertical-align: middle; width: 80px; padding-right: 18px; }
    .header-logo-cell img { width: 72px; height: auto; display: block; }
    .header-text { display: table-cell; vertical-align: middle; }
    .kementerian-label { font-size: 9.5px; color: rgba(255,255,255,0.65); text-transform: uppercase; letter-spacing: 1.2px; margin-bottom: 4px; }
    .instansi-nama { font-size: 17px; font-weight: bold; color: #ffffff; line-height: 1.2; margin-bottom: 3px; }
    .instansi-sub { font-size: 11px; color: rgba(255,255,255,0.72); }

    /* Body */
    .body { padding: 28px 28px 20px; }

    /* Greeting */
    .greeting { font-size: 13.5px; color: #1a1a1a; line-height: 1.6; margin-bottom: 20px; }

    /* Divider */
    .divider { border: none; border-top: 1px solid #e8eaf0; margin: 20px 0; }

    /* Ketentuan section */
    .section-title { font-size: 13px; font-weight: bold; color: #1a3a6e; margin-bottom: 12px; }
    .ketentuan-list { list-style: none; padding: 0; margin: 0; }
    .ketentuan-item { margin-bottom: 14px; }
    .ketentuan-num { font-size: 13px; font-weight: bold; color: #1a1a1a; margin-bottom: 5px; }
    .ketentuan-sub { list-style: none; padding: 0; margin: 4px 0 0 12px; }
    .ketentuan-sub li { font-size: 12.5px; color: #333; line-height: 1.6; margin-bottom: 4px; padding-left: 16px; position: relative; }
    .ketentuan-sub li::before { content: attr(data-marker); position: absolute; left: 0; font-weight: bold; color: #1a3a6e; }
    .ketentuan-plain { font-size: 12.5px; color: #333; line-height: 1.6; padding-left: 4px; }
    .note-box { background: #f0f4ff; border-left: 3px solid #2d5bab; border-radius: 0 6px 6px 0; padding: 8px 12px; margin: 6px 0 0 12px; font-size: 12px; color: #444; line-height: 1.5; font-style: italic; }

    /* Attachment note */
    .attachment-note { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; margin-top: 20px; display: table; width: 100%; }
    .attachment-icon { display: table-cell; vertical-align: middle; width: 32px; font-size: 20px; }
    .attachment-text { display: table-cell; vertical-align: middle; }
    .attachment-text strong { display: block; font-size: 12.5px; color: #15803d; margin-bottom: 2px; }
    .attachment-text span { font-size: 12px; color: #166534; }

    /* Footer */
    .footer { background: #f7f8fa; border-top: 1px solid #e8eaf0; padding: 18px 28px; }
    .footer-brand { font-size: 12.5px; font-weight: bold; color: #1a3a6e; margin-bottom: 4px; }
    .footer-address { font-size: 11.5px; color: #6b7280; line-height: 1.7; }
    .footer-auto { margin-top: 12px; font-size: 11px; color: #9ca3af; font-style: italic; border-top: 1px solid #e8eaf0; padding-top: 10px; }
</style>
</head>
<body>
<div class="wrapper">

@php
    // Logo asli `logo_pu.png` telah di-hosting ke CDN publik secara permanen.
    // Gmail memblokir kode Base64 sehingga gambar menjadi rusak (broken link).
    // Dengan direct public link ini, logo dijamin muncul seketika dan 100% bebas dari attachment.
    $logoImgSrc = 'https://i.imgur.com/ywF5vkl.jpeg';
@endphp

    {{-- ══ HEADER ══ --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-logo-cell">
                @if(!empty($logoImgSrc))
                    <img src="{{ $logoImgSrc }}" alt="Logo PU" style="width:72px;height:auto;display:block;">
                @endif
            </div>
            <div class="header-text">
                <div class="kementerian-label">Kementerian Pekerjaan Umum</div>
                <div class="instansi-nama">Pusat Data dan Teknologi Informasi</div>
                <div class="instansi-sub">Sekretariat Jenderal &nbsp;·&nbsp; Kementerian PU</div>
            </div>
        </div>
    </div>

    {{-- ══ BODY ══ --}}
    <div class="body">

        @php
            $lines     = explode("\n", $captionBody);
            $greeting  = array_shift($lines);          // baris pertama = salam pembuka
        @endphp

        {{-- Salam Pembuka --}}
        <p class="greeting">{{ $greeting }}</p>

        <hr class="divider">

        {{-- Ketentuan Magang --}}
        <div class="section-title">Ketentuan Magang di Pusdatin Kementerian PU:</div>

        <ul class="ketentuan-list">

            {{-- 1. Ketentuan Waktu --}}
            <li class="ketentuan-item">
                <div class="ketentuan-num">1. Ketentuan Waktu</div>
                <ul class="ketentuan-sub" style="list-style-type: none; padding-left: 20px;">
                    <li style="margin-bottom: 6px;">Masuk Pukul <strong>07.30</strong> dan Pulang Pukul <strong>16.00</strong> (Senin–Kamis) dan Pukul <strong>16.30</strong> (Jumat)</li>
                    <li style="margin-bottom: 6px;">Apabila H-2 akan selesai magang, harap diinformasikan ke kami dan pada hari H bertemu kembali.</li>
                    <li style="margin-bottom: 6px;">Pusdatin tidak akan mengeluarkan surat keterangan magang apabila periode magang sudah lewat H+1 dst.</li>
                </ul>
                <div class="note-box">📌 <i>Apabila Ketua TIM meminta perpanjangan waktu pulang, tolong diinfokan di group (sebutkan Nama dan Tim).</i></div>
            </li>

            {{-- 2. Ketentuan Pakaian --}}
            <li class="ketentuan-item">
                <div class="ketentuan-num">2. Ketentuan Pakaian</div>
                <ul class="ketentuan-sub" style="list-style-type: none; padding-left: 20px;">
                    <li style="margin-bottom: 6px;"><strong>Mahasiswa/i:</strong><br>
                        &nbsp;&nbsp;Senin–Kamis: Kemeja Putih + Celana/Rok Hitam + Almamater + Sepatu Hitam<br>
                        &nbsp;&nbsp;Jumat: Pakaian Batik Semi Formal + Almamater + Sepatu Hitam
                    </li>
                    <li style="margin-bottom: 6px;"><strong>Siswa/i:</strong><br>
                        &nbsp;&nbsp;Senin–Jumat: Seragam SMK/SMA sesuai ketentuan sekolah + Sepatu Hitam (+ Almamater jika ada)
                    </li>
                </ul>
            </li>

            {{-- 3–7 --}}
            <li class="ketentuan-item"><div class="ketentuan-num">3. Membawa Laptop</div></li>
            <li class="ketentuan-item"><div class="ketentuan-num">4. Tidak Membawa Barang Terlarang</div></li>
            <li class="ketentuan-item">
                <div class="ketentuan-num">5. Larangan Merokok</div>
                <p class="ketentuan-plain">Dilarang keras merokok/pods di ruang kerja dan/atau ruangan tertutup, serta mengganggu aktivitas pegawai lain.</p>
            </li>
            <li class="ketentuan-item">
                <div class="ketentuan-num">6. Mentaati Peraturan</div>
                <p class="ketentuan-plain">Mentaati dan melakukan segala ketentuan yang berlaku di lingkungan Pusdatin dan Kementerian PU.</p>
            </li>
            <li class="ketentuan-item">
                @php
                    // Ambil tanggal dari text box admin jika ada
                    preg_match('/tanggal (.+?) pukul/i', $captionBody, $m);
                    $tglTemu = isset($m[1]) ? $m[1] : '[ISI TANGGAL]';
                @endphp
                <div class="ketentuan-num">7. Pertemuan Awal</div>
                <p class="ketentuan-plain">Dapat bertemu dengan narahubung pada tanggal <strong>{{ $tglTemu }}</strong> pukul 08.00 WIB di Gedung Pusdatin Kementerian PU.</p>
            </li>
        </ul>

        {{-- Attachment note --}}
        <div class="attachment-note">
            <div class="attachment-icon">📎</div>
            <div class="attachment-text">
                <strong>Surat Penerimaan Terlampir</strong>
                <span>Mohon dibaca dan disimpan sebagai bukti penerimaan resmi magang Anda.</span>
            </div>
        </div>

        {{-- Anti-clipping khusus untuk block body --}}
        <div style="color:#ffffff; font-size:1px; line-height:1px; mso-line-height-rule:exactly; min-height:1px; padding:0; margin:0;">
            &zwj; {{ uniqid() }} &zwj;
        </div>

    </div>

    {{-- ══ FOOTER ══ --}}
    <div class="footer">
        <div class="footer-brand">Pusat Data dan Teknologi Informasi – Kementerian Pekerjaan Umum</div>
        <div class="footer-address">
            Jl. Pattimura No. 20, Kebayoran Baru, Jakarta 12110<br>
            Telp: (021) 7392262 &nbsp;|&nbsp; Email: <a href="mailto:pusdatin@pu.go.id" style="color:#2d5bab;text-decoration:none;">pusdatin@pu.go.id</a>
        </div>
        <div class="footer-auto">Email ini dikirim secara otomatis. Untuk pertanyaan, hubungi narahubung yang tertera dalam surat terlampir.</div>
    </div>
    
    {{-- Trik untuk menghindari Gmail menyingkat (clipping / ellipsis ...) email yang kontennya mirip --}}
    <div style="color:#f0f2f5; font-size:1px; line-height:1px; mso-line-height-rule:exactly; min-height:1px;">
        &zwj; Ref: {{ uniqid() }} - {{ now()->timestamp }} &zwj;
    </div>

</div>
</body>
</html>
