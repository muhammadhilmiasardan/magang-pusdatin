{{-- ═══ MODAL 4: KIRIM EMAIL ═══ --}}
<div id="emailOverlay" style="display:none;position:fixed;inset:0;z-index:1030;background:rgba(15,29,61,0.6);backdrop-filter:blur(4px);">
    {{-- Hapus flex-direction di container utama, gunakan flex di children kalau perlu --}}
    <div id="emailBox" style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border-radius:16px;width:90%;max-width:680px;box-shadow:0 25px 50px -12px rgba(0,0,0,0.3);overflow:hidden;">
        
        {{-- STATE 1: FORM KIRIM EMAIL --}}
        <div id="em-form" style="display:block;">
            <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Langkah 4 dari 4 — Final</div>
                    <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);margin:0;"><i class="fas fa-paper-plane" style="color:var(--accent);margin-right:8px;"></i>Kirim Email Penerimaan</h3>
                </div>
                <button onclick="closeEmailModal()" style="background:none;border:none;cursor:pointer;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-size:16px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'"><i class="fas fa-times"></i></button>
            </div>
            
            <div style="padding:24px; max-height:calc(90vh - 140px); overflow-y:auto;">
                {{-- Info Penerima --}}
                <div style="background:#f8fafc;border-radius:10px;padding:14px 16px;margin-bottom:16px;border:1px solid var(--border);">
                    <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px;">Informasi Pengiriman</div>
                    <div style="display:flex;gap:24px;flex-wrap:wrap;">
                        <div><span style="font-size:12px;color:var(--text-secondary);">Kepada:</span><br><span id="em-nama-penerima" style="font-size:13px;font-weight:600;color:var(--text-primary);">-</span></div>
                        <div><span style="font-size:12px;color:var(--text-secondary);">Email:</span><br><span id="em-email-penerima" style="font-size:13px;font-weight:600;color:var(--primary);">-</span></div>
                        <div><span style="font-size:12px;color:var(--text-secondary);">Lampiran:</span><br><span id="em-lampiran-info" style="font-size:13px;font-weight:600;color:#16a34a;"><i class="fas fa-paperclip"></i> Surat Penerimaan</span></div>
                    </div>
                </div>

                {{-- Caption / Body Email --}}
                <div style="margin-bottom:16px;">
                    <label style="font-size:12px;font-weight:600;color:var(--text-secondary);text-transform:uppercase;letter-spacing:.04em;display:block;margin-bottom:8px;">
                        Isi Pesan Email <span style="color:#ef4444;">*</span>
                        <span style="font-size:10px;color:var(--text-muted);text-transform:none;font-weight:400;margin-left:6px;">(dapat diedit sesuai kebutuhan)</span>
                    </label>
                    <textarea id="em-caption" rows="18" style="width:100%;padding:12px;border:1px solid var(--border);border-radius:8px;font-size:12.5px;font-family:'Courier New',monospace;line-height:1.6;resize:vertical;outline:none;color:var(--text-primary);box-sizing:border-box;"></textarea>
                </div>

                {{-- Uploaded file preview --}}
                <div id="em-file-preview" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;font-size:12px;color:#15803d;display:flex;align-items:center;gap:8px;">
                    <i class="fas fa-file-check" style="font-size:16px;flex-shrink:0;"></i>
                    <span id="em-file-name">-</span>
                    <a id="em-file-link" href="#" target="_blank" style="margin-left:auto;color:var(--primary);font-size:11px;text-decoration:none;"><i class="fas fa-eye"></i> Lihat</a>
                </div>
            </div>
        </div>

        {{-- STATE 2: SUCCESS STATE --}}
        <div id="em-success" style="display:none; flex-direction:column; align-items:center; justify-content:center; padding:50px 30px; text-align:center;">
            <div style="width:80px;height:80px;background:#dcfce7;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:20px;animation:successPop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;">
                <i class="fas fa-check" style="font-size:40px;color:#16a34a;"></i>
            </div>
            <h3 style="font-size:22px;font-weight:700;color:#166534;margin:0 0 10px;">Email Berhasil Dikirim!</h3>
            <p style="font-size:14px;color:#4b5563;margin:0 0 4px;" id="em-success-to">-</p>
            <p style="font-size:13px;font-weight:600;color:#15803d;margin:0 0 24px;" id="em-success-email">-</p>
            
            <div style="background:#f3f4f6;border-radius:8px;padding:12px 20px;margin-bottom:30px;font-size:12px;color:#4b5563;">
                <i class="fas fa-info-circle" style="color:var(--primary);margin-right:6px;"></i>
                Status pelamar sekarang adalah <strong>Belum Aktif</strong>.
            </div>

            <button onclick="location.reload()" class="btn-success-custom" style="padding:12px 32px;font-size:14px;border-radius:10px;">
                <i class="fas fa-check-circle"></i> Selesai
            </button>
        </div>

        {{-- FOOTER KHUSUS FORM (Disembunyikan saat sukses) --}}
        <div id="em-footer" style="padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:8px;justify-content:space-between;align-items:center;">
            <button onclick="closeEmailModal();document.getElementById('uploadOverlay').style.display='block';" class="btn-outline-custom"><i class="fas fa-arrow-left"></i> Kembali</button>
            <div style="display:flex;gap:8px;">
                <div id="em-sending-indicator" style="display:none;font-size:13px;color:var(--text-secondary);align-items:center;gap:8px;">
                    <div style="width:16px;height:16px;border:2px solid var(--border);border-top-color:var(--primary);border-radius:50%;animation:spin .8s linear infinite;"></div> Mengirim...
                </div>
                <button id="btn-kirim-email" onclick="submitKirimEmail()" class="btn-success-custom" style="gap:8px;">
                    <i class="fas fa-paper-plane"></i> Kirim Email &amp; Terima Lamaran
                </button>
            </div>
        </div>

    </div>
</div>
