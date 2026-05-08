<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Magang - PUSDATIN PUPR</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">

    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="color: #1e3a8a;">Sertifikat Magang</h2>
        <p style="margin-top: -10px; color: #666;">Pusat Data dan Teknologi Informasi — Kementerian Pekerjaan Umum</p>
    </div>

    <div style="background-color: #f9fafb; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb;">
        <p>Kepada Yth.<br><strong>{{ $peserta->nama }}</strong>,</p>

        <p>Dengan hormat,</p>

        <p>Terlampir adalah <strong>Sertifikat Magang</strong> Anda dari Pusat Data dan Teknologi Informasi, Sekretariat Jenderal, Kementerian Pekerjaan Umum.</p>

        @if(!empty($pesan_tambahan))
        <div style="background-color: #eff6ff; padding: 15px; border-left: 4px solid #1e3a8a; margin: 20px 0; border-radius: 4px;">
            {!! nl2br(e($pesan_tambahan)) !!}
        </div>
        @endif

        <p>Terima kasih atas dedikasi dan kontribusi Anda selama menjalani program magang. Semoga pengalaman ini bermanfaat bagi pengembangan karir Anda ke depan.</p>

        <p style="margin-top: 30px;">
            Salam hormat,<br>
            <strong>Subbagian Tata Usaha</strong><br>
            Pusat Data dan Teknologi Informasi<br>
            Kementerian Pekerjaan Umum
        </p>
    </div>

    <div style="margin-top: 20px; font-size: 12px; color: #9ca3af; text-align: center;">
        Ini adalah email otomatis, mohon tidak membalas ke alamat email ini.
    </div>

</body>
</html>
