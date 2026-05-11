<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 13px; color: #1a1a1a; background: #f0f2f5; }
    .wrapper { max-width: 640px; margin: 24px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }

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
    .body { padding: 28px 28px 24px; }

    /* Body email dirender dari textarea — preserve whitespace */
    .email-body-text {
        font-size: 13px;
        color: #1a1a1a;
        line-height: 1.8;
        white-space: pre-line;
        word-break: break-word;
    }

    .divider { border: none; border-top: 1px solid #e8eaf0; margin: 20px 0; }

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
    $logoImgSrc = 'https://i.imgur.com/ywF5vkl.jpeg';
@endphp

    {{-- ══ HEADER ══ --}}
    <div class="header">
        <div class="header-inner">
            <div class="header-logo-cell">
                <img src="{{ $logoImgSrc }}" alt="Logo PU" style="width:72px;height:auto;display:block;">
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

        {{-- 
            Render isi email langsung dari captionBody yang ditulis/diedit admin.
            white-space: pre-line akan menjaga semua baris dan spasi yang ada di textarea.
        --}}
        <div class="email-body-text">{{ $captionBody }}</div>

        {{-- Attachment note --}}
        <div class="attachment-note" style="margin-top: 24px;">
            <div class="attachment-icon">📎</div>
            <div class="attachment-text">
                <strong>Surat Konfirmasi Penerimaan Terlampir</strong>
                <span>Mohon dibaca dan disimpan sebagai bukti penerimaan resmi magang/PKL Anda.</span>
            </div>
        </div>

        {{-- Anti-clipping Gmail --}}
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

    {{-- Anti-clipping --}}
    <div style="color:#f0f2f5; font-size:1px; line-height:1px; mso-line-height-rule:exactly; min-height:1px;">
        &zwj; Ref: {{ uniqid() }} - {{ now()->timestamp }} &zwj;
    </div>

</div>
</body>
</html>
