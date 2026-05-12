@extends('layouts.admin')

@section('title', 'Profil Saya')

@section('content')
<div class="card-clean" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header-clean">
        <h3><i class="fas fa-user-circle" style="color: var(--primary); margin-right: 8px;"></i> Informasi Akun</h3>
    </div>

    <div class="card-body-clean">
        <form action="{{ route('admin.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 32px; font-weight: 700; flex-shrink: 0;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <h4 style="margin: 0 0 4px; font-size: 18px; color: var(--text-primary);">{{ Auth::user()->name }}</h4>
                    <span style="font-size: 13px; color: var(--text-secondary); background: #f1f5f9; padding: 4px 10px; border-radius: 20px;">Administrator</span>
                </div>
            </div>

            @if($errors->any())
                <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 12px 16px; margin-bottom: 24px; border-radius: var(--radius-sm);">
                    <ul style="margin: 0; padding-left: 20px; color: #991b1b; font-size: 13px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="margin-bottom: 20px;">
                <label style="font-size: 13px; font-weight: 500; color: var(--text-primary); display: block; margin-bottom: 6px;">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="form-input" required style="width: 100%; padding: 12px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
            </div>
            
            <div style="margin-bottom: 24px;">
                <label style="font-size: 13px; font-weight: 500; color: var(--text-primary); display: block; margin-bottom: 6px;">Alamat Email</label>
                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="form-input" required style="width: 100%; padding: 12px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
            </div>

            <hr style="border: none; border-top: 1px solid var(--border); margin: 30px 0;">
            
            <h5 style="font-size: 14px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">Ubah Password <span style="font-size: 12px; font-weight: 400; color: var(--text-secondary);">(Opsional)</span></h5>
            
            <div style="margin-bottom: 20px;">
                <label style="font-size: 13px; font-weight: 500; color: var(--text-primary); display: block; margin-bottom: 6px;">Password Baru</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="profilePassword" class="form-input" placeholder="Kosongkan jika tidak ingin diubah" style="width: 100%; padding: 12px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border); padding-right: 40px;">
                    <button type="button" onclick="togglePassword('profilePassword', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 0; outline: none;">
                        <i class="far fa-eye-slash"></i>
                    </button>
                </div>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="font-size: 13px; font-weight: 500; color: var(--text-primary); display: block; margin-bottom: 6px;">Konfirmasi Password Baru</label>
                <div style="position: relative;">
                    <input type="password" name="password_confirmation" id="profilePasswordConfirm" class="form-input" placeholder="Ulangi password baru" style="width: 100%; padding: 12px 16px; border-radius: var(--radius-sm); border: 1px solid var(--border); padding-right: 40px;">
                    <button type="button" onclick="togglePassword('profilePasswordConfirm', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 0; outline: none;">
                        <i class="far fa-eye-slash"></i>
                    </button>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-primary-custom" style="padding: 10px 24px;">
                    <i class="fas fa-save" style="margin-right: 6px;"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
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
</script>
@endpush
