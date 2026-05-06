<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body { font-family: Arial, sans-serif; font-size: 13px; color: #1a1a1a; margin: 0; padding: 0; background: #f5f5f5; }
    .wrapper { max-width: 640px; margin: 0 auto; background: #fff; }
    .header { background: linear-gradient(135deg, #1a3a6e, #2d5bab); padding: 24px 32px; }
    .header-logo { color: #fff; font-size: 10px; opacity: 0.8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px; }
    .header-title { color: #fff; font-size: 18px; font-weight: bold; }
    .header-sub { color: rgba(255,255,255,0.75); font-size: 12px; margin-top: 4px; }
    .body { padding: 32px; }
    .caption-box { white-space: pre-wrap; font-size: 13px; line-height: 1.7; color: #333; }
    .caption-box b { color: #1a3a6e; }
    .footer { background: #f8f9fa; padding: 20px 32px; border-top: 1px solid #e5e7eb; }
    .footer-text { font-size: 11px; color: #888; line-height: 1.6; }
    .footer-brand { font-weight: bold; color: #1a3a6e; font-size: 12px; margin-bottom: 4px; }
    .divider { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
    .attachment-note { background: #eff6ff; border-left: 3px solid #2d5bab; padding: 10px 14px; border-radius: 0 6px 6px 0; font-size: 12px; color: #1e40af; margin-top: 20px; }
</style>
</head>
<body>
<div class="wrapper">
    {{-- Header --}}
    <div class="header">
        <div class="header-logo">Kementerian Pekerjaan Umum</div>
        <div class="header-title">Pusat Data dan Teknologi Informasi</div>
        <div class="header-sub">Sekretariat Jenderal · Kementerian PU</div>
    </div>

    {{-- Body --}}
    <div class="body">
        <div class="caption-box">{!! nl2br(e($captionBody)) !!}</div>

        <hr class="divider">

        <div class="attachment-note">
            <strong>📎 Lampiran:</strong> Surat Penerimaan Magang telah dilampirkan pada email ini.
            Mohon dibaca dan disimpan sebagai bukti penerimaan resmi.
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-brand">Pusat Data dan Teknologi Informasi – Kementerian PU</div>
        <div class="footer-text">
            Jl. Pattimura No. 20, Kebayoran Baru, Jakarta 12110<br>
            Telp: (021) 7392262 &nbsp;|&nbsp; Email: pusdatin@pu.go.id<br><br>
            <em>Email ini dikirimkan secara otomatis oleh sistem. Jika ada pertanyaan, silakan hubungi narahubung yang tertera dalam surat terlampir.</em>
        </div>
    </div>
</div>
</body>
</html>
