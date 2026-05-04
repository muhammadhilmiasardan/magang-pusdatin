<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil — PUSDATIN PUPR</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .success-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 48px 40px;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        .check-circle {
            width: 80px; height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
            animation: bounceIn 0.5s ease;
        }
        .check-circle i { font-size: 36px; color: #fff; }
        h1 { font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 10px; }
        .desc { font-size: 14px; color: #64748b; line-height: 1.7; margin-bottom: 28px; }
        .info-box {
            background: #dbeafe; border-radius: 10px; padding: 14px 18px;
            font-size: 13px; color: #1e3a8a; text-align: left;
            display: flex; align-items: flex-start; gap: 10px; margin-bottom: 28px;
        }
        .info-box i { margin-top: 2px; flex-shrink: 0; }
        .btn-back {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 28px; background: #1e3a8a; color: #fff;
            border: none; border-radius: 10px; font-family: 'Inter', sans-serif;
            font-size: 14px; font-weight: 600; text-decoration: none;
            transition: all 150ms ease; cursor: pointer;
        }
        .btn-back:hover { background: #2548a8; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(30,58,138,0.3); }
        @keyframes bounceIn {
            0% { transform: scale(0); opacity: 0; }
            60% { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="check-circle">
            <i class="fas fa-check"></i>
        </div>
        <h1>Pendaftaran Berhasil Dikirim!</h1>
        <p class="desc">
            Terima kasih telah mendaftar magang di <strong>PUSDATIN PUPR</strong>.<br>
            Lamaran Anda sedang dalam proses review oleh tim kami.
        </p>
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div>Anda akan menerima informasi status penerimaan melalui <strong>email</strong> yang telah didaftarkan. Harap periksa inbox dan folder spam secara berkala.</div>
        </div>
        <a href="{{ route('pendaftaran.create') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Kembali ke Halaman Pendaftaran
        </a>
    </div>
</body>
</html>
