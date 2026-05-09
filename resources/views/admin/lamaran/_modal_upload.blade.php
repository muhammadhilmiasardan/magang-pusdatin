{{-- ═══ MODAL 3: UPLOAD SURAT FINAL ═══ --}}
<div id="uploadOverlay" style="display:none;position:fixed;inset:0;z-index:1020;background:rgba(15,29,61,0.6);backdrop-filter:blur(4px);">
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border-radius:16px;width:90%;max-width:520px;overflow:hidden;box-shadow:0 25px 50px -12px rgba(0,0,0,0.3);display:flex;flex-direction:column;max-height:90vh;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">Langkah 3 dari 4</div>
                <h3 style="font-size:16px;font-weight:700;color:var(--text-primary);margin:0;"><i class="fas fa-upload" style="color:var(--accent);margin-right:8px;"></i>Upload Surat Final</h3>
            </div>
            <button onclick="closeUploadModal()" style="background:none;border:none;cursor:pointer;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--text-secondary);font-size:16px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'"><i class="fas fa-times"></i></button>
        </div>
        <div style="padding:24px;flex:1;overflow-y:auto;">
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;margin-bottom:20px;font-size:12.5px;color:#92400e;line-height:1.6;">
                <strong><i class="fas fa-info-circle"></i> Petunjuk:</strong><br>
                1. Download draft PDF dari Langkah 2<br>
                2. Lakukan TTE &amp; tambahkan lampiran secara manual<br>
                3. Upload file final di sini (PDF atau scan JPG/PNG, maks 5MB)
            </div>
            <div id="upload-dropzone" style="border:2px dashed var(--border);border-radius:10px;padding:32px;text-align:center;cursor:pointer;transition:all 200ms ease;background:#fafafa;" onclick="document.getElementById('upload-file-input').click()" ondragover="event.preventDefault();this.style.borderColor='var(--primary)';this.style.background='var(--primary-lighter)'" ondragleave="this.style.borderColor='var(--border)';this.style.background='#fafafa'" ondrop="handleDrop(event)">
                <i class="fas fa-cloud-upload-alt" style="font-size:32px;color:var(--text-muted);margin-bottom:10px;display:block;"></i>
                <div style="font-size:13px;font-weight:600;color:var(--text-secondary);margin-bottom:4px;">Klik atau drag &amp; drop file di sini</div>
                <div style="font-size:11px;color:var(--text-muted);">PDF, JPG, PNG — Maksimal 5MB</div>
                <input type="file" id="upload-file-input" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" onchange="handleFileSelect(this)">
            </div>
            <div id="upload-file-preview" style="display:none;margin-top:12px;padding:12px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-file-check" style="color:#16a34a;font-size:18px;flex-shrink:0;"></i>
                <div style="flex:1;overflow:hidden;">
                    <div id="upload-file-name" style="font-size:13px;font-weight:600;color:#15803d;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">-</div>
                    <div id="upload-file-size" style="font-size:11px;color:#16a34a;">-</div>
                </div>
                <button onclick="clearFile()" style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:14px;flex-shrink:0;"><i class="fas fa-times"></i></button>
            </div>
            <div id="upload-progress-wrapper" style="display:none;margin-top:12px;">
                <div style="font-size:12px;color:var(--text-secondary);margin-bottom:6px;">Mengupload...</div>
                <div style="background:#e5e7eb;border-radius:999px;height:6px;overflow:hidden;">
                    <div id="upload-progress-bar" style="height:100%;background:var(--primary);width:0%;transition:width 200ms ease;border-radius:999px;"></div>
                </div>
            </div>
        </div>
        <div style="padding:16px 24px;border-top:1px solid var(--border);display:flex;gap:8px;justify-content:space-between;">
            <button onclick="closeUploadModal();document.getElementById('suratOverlay').style.display='block';" class="btn-outline-custom"><i class="fas fa-arrow-left"></i> Kembali</button>
            <button id="btn-upload-submit" onclick="submitUpload()" class="btn-primary-custom" disabled style="opacity:.5;cursor:not-allowed;"><i class="fas fa-upload"></i> Upload &amp; Lanjut</button>
        </div>
    </div>
</div>
