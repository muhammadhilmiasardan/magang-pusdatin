<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Admin Magang PUSDATIN</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #2548a8;
            --accent: #fbbf24;
            --bg-body: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border: #e2e8f0;
            --radius: 12px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            padding: 40px 32px;
            border: 1px solid var(--border);
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            margin-bottom: 16px;
        }

        .login-header h1 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 8px;
            color: var(--primary);
        }

        .login-header p {
            font-size: 14px;
            color: var(--text-secondary);
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-primary);
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            box-sizing: border-box;
            transition: all 0.2s;
            background: #f8fafc;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }

        .btn-login:hover {
            background: var(--primary-light);
            transform: translateY(-1px);
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #ecfdf5;
            color: #065f46;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid #a7f3d0;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('images/logo-pu.png') }}" alt="Logo PUPR">
                <h1>Admin PUSDATIN</h1>
                <p>Silakan masuk ke akun Anda</p>
            </div>

            @if($errors->any())
                <div class="alert-error auto-hide-toast">
                    <i class="fas fa-exclamation-circle" style="margin-right: 6px;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert-success auto-hide-toast">
                    <i class="fas fa-check-circle" style="margin-right: 6px;"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" required value="{{ old('email') }}" placeholder="admin@pusdatin.go.id" autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" class="form-input" required style="padding-right: 40px;">
                        <button type="button" onclick="togglePassword('password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 0; outline: none;">
                            <i class="far fa-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt" style="margin-right: 6px;"></i> Masuk
                </button>
            </form>
        </div>
        <p style="text-align: center; font-size: 12px; color: var(--text-secondary); margin-top: 24px;">
            &copy; {{ date('Y') }} PUSDATIN Kementerian PUPR.
        </p>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        }

        // Auto Hide Flash Messages
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.auto-hide-toast');
            if (toasts.length > 0) {
                setTimeout(function() {
                    toasts.forEach(toast => {
                        toast.style.transition = 'opacity 0.5s ease';
                        toast.style.opacity = '0';
                        setTimeout(() => toast.remove(), 500);
                    });
                }, 5000);
            }
        });
    </script>

</body>
</html>
