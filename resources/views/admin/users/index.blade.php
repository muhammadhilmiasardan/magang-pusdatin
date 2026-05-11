@extends('layouts.admin')

@section('title', 'Manajemen Admin')

@section('content')
<div class="card-clean">
    <div class="card-header-clean" style="flex-direction: column; align-items: flex-start; gap: 16px;">
        <div style="display: flex; justify-content: space-between; width: 100%; align-items: center;">
            <div>
                <h3 style="margin-bottom: 4px;">Daftar Admin</h3>
                <p style="font-size: 13px; color: var(--text-secondary); margin: 0;">Kelola akses akun admin untuk dashboard Manajemen Magang.</p>
            </div>
            <button onclick="openAddModal()" class="btn-primary-custom">
                <i class="fas fa-plus"></i> Tambah Admin
            </button>
        </div>

        {{-- Search Bar --}}
        <div style="width: 100%; max-width: 360px; position: relative; margin-top: 8px;">
            <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 14px;"></i>
            <input type="text" id="searchInput" placeholder="Cari nama atau email admin..." 
                   style="width: 100%; padding: 12px 16px 12px 42px; border-radius: 10px; background: #ffffff; border: 1px solid #cbd5e1; font-size: 14px; color: var(--text-primary); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none;"
                   onfocus="this.style.borderColor='var(--primary)';"
                   onblur="this.style.borderColor='#cbd5e1';">
        </div>
    </div>

    <div class="card-body-clean" style="padding: 0;">
        @if($users->isEmpty())
            <div class="empty-state" style="padding: 60px 20px;">
                <i class="fas fa-users-slash" style="font-size: 48px; color: var(--text-muted); margin-bottom: 16px; display: block;"></i>
                <h4 style="font-size: 16px; color: var(--text-primary); margin-bottom: 8px;">Belum Ada Admin</h4>
                <p style="color: var(--text-secondary); font-size: 14px;">Tambahkan admin baru untuk memberikan akses pengelola.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table class="table-clean" id="adminTable">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>Tanggal Ditambahkan</th>
                            <th style="text-align: center; width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $i => $u)
                        <tr class="admin-row">
                            <td style="text-align: center; color: var(--text-muted); font-weight: 500;">{{ $i + 1 }}</td>
                            <td>
                                <div class="admin-nama" style="font-weight: 600; color: var(--text-primary);">
                                    {{ $u->name }}
                                    @if($u->id === Auth::id())
                                        <span class="badge-status badge-aktif" style="margin-left: 8px; font-size: 10px;">Anda</span>
                                    @endif
                                </div>
                            </td>
                            <td class="admin-email" style="color: var(--text-secondary);">{{ $u->email }}</td>
                            <td style="font-size: 13px;">
                                {{ $u->created_at->format('d M Y, H:i') }}
                            </td>
                            <td style="text-align: center;">
                                <div style="display: flex; gap: 6px; justify-content: center;">
                                    <button onclick="openEditModal({{ $u->id }}, '{{ htmlspecialchars($u->name, ENT_QUOTES) }}', '{{ htmlspecialchars($u->email, ENT_QUOTES) }}')" class="btn-outline-custom btn-sm-custom" title="Edit Admin">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    
                                    @if($u->id !== Auth::id())
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akses admin {{ $u->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger-custom btn-sm-custom" title="Hapus Admin">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn-danger-custom btn-sm-custom" style="opacity: 0.5; cursor: not-allowed;" title="Anda tidak bisa menghapus akun sendiri" disabled>
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

{{-- MODAL TAMBAH ADMIN --}}
<div id="addModal" class="modal-overlay hidden" style="z-index: 1050;">
    <div class="modal-content" style="max-width: 500px; width: 95%;">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid var(--border);">
            <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--text-primary);"><i class="fas fa-user-plus" style="margin-right: 8px; color: var(--primary);"></i> Tambah Admin Baru</h3>
            <button onclick="closeModal('addModal')" style="background: none; border: none; cursor: pointer; font-size: 20px; color: var(--text-secondary);">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="modal-body" style="padding: 24px;">
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 13px; font-weight: 500; color: var(--text-primary); display: block; margin-bottom: 6px;">Nama Lengkap <span style="color: red;">*</span></label>
                    <input type="text" name="name" class="form-input" required placeholder="Masukkan nama lengkap" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                </div>
                
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 13px; font-weight: 500; color: var(--text-primary); display: block; margin-bottom: 6px;">Email <span style="color: red;">*</span></label>
                    <input type="email" name="email" class="form-input" required placeholder="email@contoh.com" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                </div>

                <div style="margin-bottom: 16px;">
                    <label style="font-size: 13px; font-weight: 500; color: var(--text-primary); display: block; margin-bottom: 6px;">Password <span style="color: red;">*</span></label>
                    <input type="password" name="password" class="form-input" required placeholder="Minimal 8 karakter" minlength="8" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                </div>

                <div style="margin-bottom: 8px;">
                    <label style="font-size: 13px; font-weight: 500; color: var(--text-primary); display: block; margin-bottom: 6px;">Konfirmasi Password <span style="color: red;">*</span></label>
                    <input type="password" name="password_confirmation" class="form-input" required placeholder="Ulangi password" minlength="8" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                </div>
            </div>
            <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 12px; background: #f8fafc;">
                <button type="button" onclick="closeModal('addModal')" class="btn-outline-custom">Batal</button>
                <button type="submit" class="btn-primary-custom">Simpan Admin</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT ADMIN --}}
<div id="editModal" class="modal-overlay hidden" style="z-index: 1050;">
    <div class="modal-content" style="max-width: 500px; width: 95%;">
        <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid var(--border);">
            <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--text-primary);"><i class="fas fa-user-edit" style="margin-right: 8px; color: var(--primary);"></i> Edit Admin</h3>
            <button onclick="closeModal('editModal')" style="background: none; border: none; cursor: pointer; font-size: 20px; color: var(--text-secondary);">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editForm" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body" style="padding: 24px;">
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 13px; font-weight: 500; color: var(--text-primary); display: block; margin-bottom: 6px;">Nama Lengkap <span style="color: red;">*</span></label>
                    <input type="text" name="name" id="editName" class="form-input" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                </div>
                
                <div style="margin-bottom: 16px;">
                    <label style="font-size: 13px; font-weight: 500; color: var(--text-primary); display: block; margin-bottom: 6px;">Email <span style="color: red;">*</span></label>
                    <input type="email" name="email" id="editEmail" class="form-input" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                </div>

                <div style="background: #f1f5f9; padding: 12px; border-radius: 8px; margin-bottom: 8px;">
                    <p style="font-size: 12px; color: var(--text-secondary); margin-bottom: 12px; margin-top: 0;">
                        <i class="fas fa-info-circle"></i> Kosongkan kolom password di bawah ini jika Anda <b>tidak ingin</b> mengubah password.
                    </p>
                    <div style="margin-bottom: 12px;">
                        <label style="font-size: 13px; font-weight: 500; color: var(--text-primary); display: block; margin-bottom: 6px;">Password Baru</label>
                        <input type="password" name="password" class="form-input" placeholder="Opsional" minlength="8" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                    </div>
    
                    <div>
                        <label style="font-size: 13px; font-weight: 500; color: var(--text-primary); display: block; margin-bottom: 6px;">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="Opsional" minlength="8" style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding: 16px 24px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 12px; background: #f8fafc;">
                <button type="button" onclick="closeModal('editModal')" class="btn-outline-custom">Batal</button>
                <button type="submit" class="btn-primary-custom">Update Admin</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
    .hidden { display: none !important; }
    
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 29, 61, 0.6);
        display: flex; justify-content: center; align-items: center;
        backdrop-filter: blur(4px);
    }
    
    .modal-content {
        background: #fff; border-radius: 12px;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
        overflow: hidden;
        animation: scaleIn 0.2s ease;
    }

    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    
    /* Error display */
    .validation-errors {
        background: #fef2f2;
        border-left: 4px solid #ef4444;
        padding: 12px 16px;
        margin-bottom: 20px;
        border-radius: var(--radius-sm);
    }
    
    .validation-errors ul {
        margin: 0;
        padding-left: 20px;
        color: #991b1b;
        font-size: 13px;
    }
</style>
@endpush

@push('scripts')
<script>
    // Fitur Search Client-Side
    document.getElementById('searchInput')?.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('.admin-row');
        
        rows.forEach(row => {
            const nama = row.querySelector('.admin-nama').textContent.toLowerCase();
            const email = row.querySelector('.admin-email').textContent.toLowerCase();
            
            if (nama.includes(searchTerm) || email.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Modal Logic
    function openAddModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function openEditModal(id, name, email) {
        const form = document.getElementById('editForm');
        form.action = `/admin/users/${id}`;
        
        document.getElementById('editName').value = name;
        document.getElementById('editEmail').value = email;
        
        document.getElementById('editModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.body.style.overflow = '';
    }

    // Close on overlay click
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });

    // Close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay:not(.hidden)').forEach(modal => {
                closeModal(modal.id);
            });
        }
    });
</script>

{{-- Show Validation Errors as Sweet Alerts or standard alert if any --}}
@if($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        let errorMsg = "Terjadi kesalahan:\n";
        @foreach ($errors->all() as $error)
            errorMsg += "- {{ $error }}\n";
        @endforeach
        alert(errorMsg);
    });
</script>
@endif
@endpush
