<script>
const CAPTION_TEMPLATE = (nama, institusi, jamNow, tglMulai) => {
    const jam = parseInt(jamNow);
    const sapa = jam < 11 ? 'pagi' : jam < 15 ? 'siang' : 'sore';
    return `Selamat ${sapa} Sdr. ${nama}, berikut terlampir surat tanggapan dari kami terkait permohonan magang dari ${institusi}:

Ketentuan Magang di Pusdatin Kementerian PU:

1. Ketentuan Waktu:
a. Masuk Pukul 07.30 dan Pulang Pukul 16.00 (senin-kamis) dan Pukul 16.30 (jumat)
PS: Apabila Ketua TIM meminta untuk perpanjangan waktu pulang tolong diinfokan di group (sebutkan Nama dan Tim).

b. Apabila H-2 akan selesai magang harap diinformasikan ke saya dan pada hari H bertemu dengan saya Kembali.

c. Pusdatin tidak akan mengeluarkan surat keterangan magang apabila periode magang sudah lewat H+1 dst.

2. Ketentuan Pakaian:
a. Mahasiswa/i
Senin - Kamis : Atasan Kemeja Putih Lengan Panjang/Pendek dan Bawahan Celana/Rok Hitam Sopan dilengkapi dengan Almamater Universitas dan Sepatu Hitam
Jumat : Pakaian Batik Semi Formal dilengkapi dengan Almamater dan Sepatu Hitam

b. Siswa/i
Senin - Jumat : Memakai seragam SMK/SMA sesuai dengan ketentuan sekolah dan Sepatu Hitam (dilengkapi Almamater apabila ada)

3. Membawa Laptop
4. Tidak Membawa Barang Terlarang
5. Dilarang keras merokok/pods diruang kerja dan/atau ruangan tertutup serta menimbulkan terganggunya aktivitas pegawai lain
6. Mentaati dan Melakukan segala Ketentuan yang berlaku di Lingkungan Pusdatin dan Kementerian PU
7. Dapat bertemu dengan narahubung pada tanggal ${tglMulai} pukul 08.00 WIB di Gedung Pusdatin Kementerian PU`;
};

// ── State ──────────────────────────────────────────
let _id = null, _data = null, _uploadedPath = null, _uploadedUrl = null;
let _emailJustOpened = false;

// ── Modal helpers ───────────────────────────────────
function closeReviewModal()  { document.getElementById('reviewOverlay').style.display='none'; }
function closeSuratModal()   { document.getElementById('suratOverlay').style.display='none'; }
function closeUploadModal()  { document.getElementById('uploadOverlay').style.display='none'; }
function closeEmailModal()   { document.getElementById('emailOverlay').style.display='none'; }

// ── Open Modal 1: Review ────────────────────────────
$(document).ready(function(){
    $('.open-review').click(function(){
        _id = $(this).data('id');
        $('#reviewLoadingSpinner').show();
        $('#reviewContent').hide();
        document.getElementById('reviewOverlay').style.display='block';

        $.get(`/admin/lamaran/${_id}`, function(d){
            _data = d;
            $('#rv-nama').text(d.nama);
            $('#rv-nim').text(d.nim_nis ? 'NIM/NIS: '+d.nim_nis : '');
            if(d.pas_foto){ $('#rv-foto').attr('src','/storage/'+d.pas_foto).show(); $('#rv-foto-ph').hide(); }
            else { $('#rv-foto').hide(); $('#rv-foto-ph').show(); }
            $('#rv-email').text(d.email);
            $('#rv-telp').text(d.nomor_telp);
            $('#rv-institusi').text(d.nama_institusi+' ('+d.tingkat_pendidikan+')');
            $('#rv-jurusan').text(d.jurusan);
            $('#rv-email-inst').text(d.email_institusi);
            const opt={day:'numeric',month:'long',year:'numeric'};
            $('#rv-mulai').text(new Date(d.tanggal_mulai).toLocaleDateString('id-ID',opt));
            $('#rv-selesai').text(new Date(d.tanggal_selesai).toLocaleDateString('id-ID',opt));
            $('#rv-tim1').text(d.tim_kerja1?d.tim_kerja1.nama_tim:'-');
            $('#rv-tim2').text(d.tim_kerja2?d.tim_kerja2.nama_tim:'-');
            if(d.surat_rekomendasi) $('#rv-btn-surat').attr('href','/storage/'+d.surat_rekomendasi).show(); else $('#rv-btn-surat').hide();
            if(d.cv) $('#rv-btn-cv').attr('href','/storage/'+d.cv).show(); else $('#rv-btn-cv').hide();
            if(d.pas_foto) $('#rv-btn-foto').attr('href','/storage/'+d.pas_foto).show(); else $('#rv-btn-foto').hide();
            $('#rv-form-tolak').attr('action',`/admin/lamaran/${d.id}/tolak`);
            $('#reviewLoadingSpinner').hide(); $('#reviewContent').show();
        });
    });
});

// ── Open Modal 2: Surat ─────────────────────────────
function openSuratModal(){
    if(!_data) return;
    closeReviewModal();
    buildTimOptions(_data);
    document.getElementById('suratOverlay').style.display='block';
    document.getElementById('lm-preview-surat-container').style.display='none';
}

function buildTimOptions(d){
    const c=document.getElementById('lm-penempatan-options'); c.innerHTML='';
    const opts=[];
    if(d.tim_kerja1) opts.push({id:d.id_tim_kerja_1,nama:d.tim_kerja1.nama_tim,label:'Pilihan 1 (Utama)'});
    if(d.tim_kerja2) opts.push({id:d.id_tim_kerja_2,nama:d.tim_kerja2.nama_tim,label:'Pilihan 2'});
    opts.forEach((opt,i)=>{
        const rid=`tim-radio-${i}`;
        const div=document.createElement('div');
        div.style.cssText='display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;cursor:pointer;border:2px solid var(--border);transition:all 150ms;background:#fff;';
        div.innerHTML=`<input type="radio" name="lm_pen" id="${rid}" value="${opt.id}" ${i===0?'checked':''} style="accent-color:var(--primary);width:16px;height:16px;cursor:pointer;">
            <label for="${rid}" style="cursor:pointer;flex:1;margin:0;">
                <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;">${opt.label}</div>
                <div style="font-size:13px;font-weight:600;color:var(--primary);margin-top:2px;">${opt.nama}</div>
            </label>`;
        const radio=div.querySelector('input');
        const upd=()=>{ div.style.borderColor=radio.checked?'var(--primary)':'var(--border)'; div.style.background=radio.checked?'var(--primary-lighter)':'#fff'; };
        radio.addEventListener('change',()=>{ c.querySelectorAll('div').forEach(d=>{const r=d.querySelector('input');if(r){d.style.borderColor='var(--border)';d.style.background='#fff';}}); upd(); document.getElementById('lm-hidden-tim').value=radio.value; });
        div.addEventListener('click',()=>{radio.checked=true;radio.dispatchEvent(new Event('change'));});
        upd(); c.appendChild(div);
    });
    const first=c.querySelector('input[type=radio]');
    if(first) document.getElementById('lm-hidden-tim').value=first.value;
}

function getSuratData(){
    return {
        id_tim_kerja_ditempatkan: document.getElementById('lm-hidden-tim').value,
        nomor_surat: document.getElementById('lm-nomor-surat').value.trim(),
        yth: document.getElementById('lm-yth').value.trim(),
        nomor_surat_univ: document.getElementById('lm-nomor-surat-univ').value.trim(),
        tanggal_surat_lamaran: document.getElementById('lm-tanggal-surat-lamaran').value.trim(),
    };
}

function previewSurat(){
    const f=getSuratData();
    if(!f.nomor_surat||!f.yth||!f.nomor_surat_univ||!f.tanggal_surat_lamaran){ alert('Harap lengkapi semua field data surat.'); return; }
    if(!f.id_tim_kerja_ditempatkan){ alert('Pilih penempatan terlebih dahulu.'); return; }
    const hf=document.createElement('form'); hf.method='POST'; hf.action=`/admin/lamaran/${_id}/surat/preview`; hf.target='lm-surat-iframe'; hf.style.display='none';
    const tk=document.createElement('input'); tk.type='hidden'; tk.name='_token'; tk.value=document.querySelector('meta[name="csrf-token"]').content; hf.appendChild(tk);
    Object.entries(f).forEach(([k,v])=>{ const i=document.createElement('input'); i.type='hidden'; i.name=k; i.value=v; hf.appendChild(i); });
    document.body.appendChild(hf); hf.submit(); document.body.removeChild(hf);
    document.getElementById('lm-preview-surat-container').style.display='block';
}

function downloadSurat(){
    const f=getSuratData();
    if(!f.nomor_surat||!f.yth||!f.nomor_surat_univ||!f.tanggal_surat_lamaran){ alert('Lengkapi data surat terlebih dahulu.'); return; }
    const hf=document.createElement('form'); hf.method='POST'; hf.action=`/admin/lamaran/${_id}/surat/download`; hf.style.display='none';
    const tk=document.createElement('input'); tk.type='hidden'; tk.name='_token'; tk.value=document.querySelector('meta[name="csrf-token"]').content; hf.appendChild(tk);
    Object.entries(f).forEach(([k,v])=>{ const i=document.createElement('input'); i.type='hidden'; i.name=k; i.value=v; hf.appendChild(i); });
    document.body.appendChild(hf); hf.submit(); document.body.removeChild(hf);
}

function openUploadModal(){
    const f=getSuratData();
    if(!f.id_tim_kerja_ditempatkan){ alert('Pilih penempatan terlebih dahulu.'); return; }
    if(!f.nomor_surat||!f.yth||!f.nomor_surat_univ||!f.tanggal_surat_lamaran){ 
        alert('Harap lengkapi semua Data Surat Penerimaan sebelum melanjutkan ke Langkah 3.'); 
        return; 
    }
    closeSuratModal();
    clearFile();
    document.getElementById('uploadOverlay').style.display='block';
}

// ── Modal 3: Upload ─────────────────────────────────
function handleFileSelect(input){
    if(input.files&&input.files[0]) showFile(input.files[0]);
}
function handleDrop(e){
    e.preventDefault();
    document.getElementById('upload-dropzone').style.borderColor='var(--border)';
    document.getElementById('upload-dropzone').style.background='#fafafa';
    if(e.dataTransfer.files&&e.dataTransfer.files[0]) showFile(e.dataTransfer.files[0]);
}
function showFile(f){
    const allowed=['application/pdf','image/jpeg','image/jpg','image/png'];
    if(!allowed.includes(f.type)){ alert('Format tidak didukung. Gunakan PDF, JPG, atau PNG.'); return; }
    if(f.size>5*1024*1024){ alert('Ukuran file melebihi 5MB.'); return; }
    document.getElementById('upload-file-name').textContent=f.name;
    document.getElementById('upload-file-size').textContent=(f.size/1024).toFixed(1)+' KB';
    document.getElementById('upload-file-preview').style.display='flex';
    document.getElementById('upload-dropzone').style.display='none';
    const btn=document.getElementById('btn-upload-submit'); btn.disabled=false; btn.style.opacity='1'; btn.style.cursor='pointer';
}
function clearFile(){
    document.getElementById('upload-file-input').value='';
    document.getElementById('upload-file-preview').style.display='none';
    document.getElementById('upload-dropzone').style.display='block';
    const btn=document.getElementById('btn-upload-submit'); btn.disabled=true; btn.style.opacity='.5'; btn.style.cursor='not-allowed';
}

function submitUpload(){
    const fileInput=document.getElementById('upload-file-input');
    const timId=document.getElementById('lm-hidden-tim').value;
    if(!fileInput.files||!fileInput.files[0]){ alert('Pilih file terlebih dahulu.'); return; }
    const fd=new FormData();
    fd.append('surat_final', fileInput.files[0]);
    fd.append('id_tim_kerja_ditempatkan', timId);
    fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    document.getElementById('upload-progress-wrapper').style.display='block';
    document.getElementById('btn-upload-submit').disabled=true;
    const xhr=new XMLHttpRequest();
    xhr.upload.onprogress=e=>{ if(e.lengthComputable){ document.getElementById('upload-progress-bar').style.width=(e.loaded/e.total*100)+'%'; } };
    xhr.onload=()=>{
        const res=JSON.parse(xhr.responseText);
        if(res.success){
            _uploadedPath=res.file_path; _uploadedUrl=res.file_url;
            closeUploadModal();
            setTimeout(()=>openEmailModal(), 250);
        } else { alert(res.message||'Upload gagal.'); document.getElementById('btn-upload-submit').disabled=false; }
        document.getElementById('upload-progress-wrapper').style.display='none';
        document.getElementById('upload-progress-bar').style.width='0%';
    };
    xhr.onerror=()=>{ alert('Terjadi kesalahan jaringan.'); document.getElementById('btn-upload-submit').disabled=false; };
    xhr.open('POST',`/admin/lamaran/${_id}/upload-surat`);
    xhr.send(fd);
}

// ── Modal 4: Email ──────────────────────────────────
function openEmailModal(){
    if(!_data) return;
    
    // Reset state modal ke form
    document.getElementById('em-form').style.display='block';
    document.getElementById('em-success').style.display='none';
    document.getElementById('em-footer').style.display='flex';
    
    document.getElementById('em-nama-penerima').textContent=_data.nama;
    document.getElementById('em-email-penerima').textContent=_data.email;
    const fname=_uploadedPath ? _uploadedPath.split('/').pop() : 'Surat Penerimaan';
    document.getElementById('em-file-name').textContent=fname;
    if(_uploadedUrl) document.getElementById('em-file-link').href=_uploadedUrl;
    const jam=new Date().getHours();
    
    // Format tanggal mulai ke format Indonesia (contoh: 11 Mei 2026)
    const tglMulaiObj = new Date(_data.tanggal_mulai);
    const formatter = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    const tglMulaiText = formatter.format(tglMulaiObj);
    
    document.getElementById('em-caption').value=CAPTION_TEMPLATE(_data.nama, _data.nama_institusi, jam, tglMulaiText);
    
    // Set flag anti-ghost click
    _emailJustOpened=true;
    setTimeout(()=>{ _emailJustOpened=false; }, 500);
    
    document.getElementById('emailOverlay').style.display='block';
}

function submitKirimEmail(){
    const caption=document.getElementById('em-caption').value.trim();
    if(!caption){ alert('Isi pesan email tidak boleh kosong.'); return; }
    if(!confirm(`Kirim email penerimaan ke ${_data.email}?\n\nStatus ${_data.nama} akan berubah menjadi "Belum Aktif".`)) return;
    const btn = document.getElementById('btn-kirim-email');
    btn.disabled=true;
    document.getElementById('em-sending-indicator').style.display='flex';
    $.ajax({
        url: `/admin/lamaran/${_id}/kirim-email`,
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {caption: caption},
        success: function(res){
            // Tampilkan success state animasi
            document.getElementById('em-form').style.display='none';
            document.getElementById('em-footer').style.display='none';
            document.getElementById('em-success-to').textContent='Berhasil dikirim kepada '+_data.nama;
            document.getElementById('em-success-email').textContent=_data.email;
            document.getElementById('em-success').style.display='flex';
        },
        error: function(xhr){
            alert('❌ '+(xhr.responseJSON?.message||'Terjadi kesalahan.'));
            btn.disabled=false;
            document.getElementById('em-sending-indicator').style.display='none';
        }
    });
}

// ── ESC & overlay close ─────────────────────────────
['reviewOverlay','suratOverlay','uploadOverlay'].forEach(id=>{
    document.getElementById(id).addEventListener('click',function(e){ if(e.target===this) this.style.display='none'; });
});
// emailOverlay punya proteksi anti-ghost click
document.getElementById('emailOverlay').addEventListener('click',function(e){ 
    if(e.target===this && !_emailJustOpened) this.style.display='none'; 
});

document.addEventListener('keydown',function(e){
    if(e.key==='Escape'){
        ['emailOverlay','uploadOverlay','suratOverlay','reviewOverlay'].forEach(id=>{
            const el=document.getElementById(id);
            if(el&&el.style.display!=='none'){ el.style.display='none'; return; }
        });
    }
});

function closeEmailModal(){
    document.getElementById('emailOverlay').style.display='none';
}

// Iframe Scaling to fit without scrolling
function fitIframe(iframe) {
    if (!iframe || iframe.offsetParent === null) return; // if hidden
    const parentWidth = iframe.parentElement.offsetWidth;
    const iframeWidth = parseFloat(iframe.style.width);
    if(parentWidth > 0 && iframeWidth > 0) {
        const scale = parentWidth / iframeWidth;
        iframe.style.transform = `scale(${scale})`;
    }
}

window.addEventListener('resize', () => {
    document.querySelectorAll('.iframe-fit-container iframe').forEach(fitIframe);
});
</script>
