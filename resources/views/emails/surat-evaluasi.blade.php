<!DOCTYPE html>
<html>
<head>
    <title>Lembar Evaluasi Magang - PUSDATIN PUPR</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <p>Halo {{ $peserta->nama }},</p>
    
    <p>Terlampir adalah <strong>Lembar Evaluasi Kegiatan Magang/PKL</strong> Anda selama di Pusat Data dan Teknologi Informasi, Kementerian Pekerjaan Umum.</p>
    
    @if(!empty($pesan_tambahan))
    <div style="background-color: #f9f9f9; padding: 15px; border-left: 4px solid #0056b3; margin: 20px 0;">
        {!! nl2br(e($pesan_tambahan)) !!}
    </div>
    @endif
    
    <p>Terima kasih atas kontribusi Anda selama masa magang. Semoga pengalaman dan ilmu yang didapatkan dapat bermanfaat untuk karir Anda di masa depan.</p>
    
    <br>
    <p>Salam hangat,</p>
    <p><strong>Admin Magang PUSDATIN PUPR</strong></p>
</body>
</html>
