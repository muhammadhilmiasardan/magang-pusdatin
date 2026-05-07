<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Magang - PUSDATIN PUPR</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="color: #004d40;">Surat Keterangan Magang</h2>
        <p style="margin-top: -10px; color: #666;">Pusat Data dan Teknologi Informasi - Kementerian PUPR</p>
    </div>

    <div style="background-color: #f9fafb; padding: 20px; border-radius: 8px; border: 1px solid #e5e7eb;">
        <p>Halo <strong>{{ $peserta->nama }}</strong>,</p>
        
        <p>Selamat! Anda telah menyelesaikan program Praktik Kerja Lapangan (PKL)/Magang di lingkungan Pusat Data dan Teknologi Informasi, Kementerian Pekerjaan Umum.</p>
        
        <p>
            {{ $pesan_tambahan ?? 'Terlampir adalah Surat Keterangan Magang resmi yang menandakan Anda telah menyelesaikan periode magang dengan baik. Terima kasih atas dedikasi dan kontribusi Anda selama berada di PUSDATIN PUPR.' }}
        </p>

        <p>Semoga pengalaman yang didapatkan dapat bermanfaat untuk karir dan studi Anda ke depannya.</p>

        <p style="margin-top: 30px;">
            Salam hangat,<br>
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
