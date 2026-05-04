# Spesifikasi Dashboard Admin V2 - Sistem Manajemen Magang PUSDATIN

Dokumen ini merupakan pembaruan (*update*) dari spesifikasi sebelumnya, dengan penambahan arsitektur UX/UI terbaru berupa **Popup Modal Detail** dan pemisahan menu **Pusat Dokumen** untuk efisiensi kerja Admin Kepegawaian.

---

## 1. Struktur Menu Utama (Sidebar)

Sistem dirancang dengan 4 menu utama agar fungsi operasional (Master Data) dan fungsi administratif (Persuratan) tidak saling tumpang tindih.

### 🏠 A. Dashboard
Pusat pemantauan visual terpadu (*Helicopter View*).
* **KPI Cards:** Menampilkan jumlah total peserta berdasarkan status (Belum Aktif, Aktif, Selesai, Anulir).
* **Pemantauan Kuota:** Tabel/Grafik sisa kuota masing-masing Tim Kerja secara *real-time*.
* **Notifikasi:** Indikator jumlah lamaran baru yang masuk.

### 📥 B. Lamaran Masuk (Review Pipeline)
Tempat verifikasi khusus bagi pendaftar baru (`Menunggu Review`).
* **Fitur Utama:** Preview CV & Surat Rekomendasi.
* **Tindakan (Action):** * **Terima:** Memicu *generate* otomatis **Surat Penerimaan**. Setelah Admin klik 'Kirim Email', status pelamar berubah menjadi `Belum Aktif`.
  * **Tolak:** Mengubah status pelamar menjadi `Anulir`.

### 👥 C. Manajemen Magang (Master Data)
Buku induk data peserta. Menampilkan tabel ringkas (Nama, Institusi, Penempatan, Tanggal Selesai) yang dikelompokkan dengan **Tabs Status** (Belum Aktif, Aktif, Selesai, Anulir).
* **⭐ Fitur Unggulan: Popup Modal Detail Peserta**
  Saat nama peserta atau tombol *View* diklik, akan muncul Modal *Pop-up* berisi:
  * **Sisi Kiri (Biodata):** Pas foto, informasi kontak peserta, kontak Penanggung Jawab (PJ) Institusi, dan periode magang.
  * **Sisi Kanan (Berkas & Status):** Tautan untuk mengunduh CV/Rekomendasi, status pengiriman dokumen akhir (SK, Evaluasi, Sertifikat), dan informasi penempatan.

### 🗂️ D. Pusat Dokumen (Persuratan)
Menu khusus untuk *generate*, kelola, dan *blast* email dokumen tahap lanjut. Dikelompokkan dalam bentuk Tabs:
* **Tab 1: Surat Keterangan Aktif:** Berisi daftar peserta `Aktif` untuk dicetakkan SK Magang.
* **Tab 2: Pengiriman Evaluasi (H-7):** Tabel dinamis yang **hanya** menampilkan peserta dengan sisa waktu magang 7-14 hari. Memudahkan Admin untuk segera mem-blast Lembar Evaluasi ke Email Institusi terkait.
* **Tab 3: Sertifikat Kelulusan:** Menampilkan daftar alumni magang (`Selesai`) yang belum mendapatkan sertifikat (`is_sertifikat_sent = 0`).

---

## 2. Alur Pengiriman Dokumen (PDF Generation)

| Jenis Dokumen | Lokasi Eksekusi | Kapan / Trigger | Penerima Email |
| :--- | :--- | :--- | :--- |
| **Surat Penerimaan** | Menu Lamaran Masuk | Setelah di-ACC oleh Admin | Email Pribadi Peserta |
| **SK Magang** | Menu Pusat Dokumen | Selama status masih `Aktif` | Email Pribadi Peserta |
| **Lembar Evaluasi** | Menu Pusat Dokumen | Mendekati akhir magang (H-7 / H-14) | **Email Penanggung Jawab Institusi** |
| **Sertifikat** | Menu Pusat Dokumen | Setelah magang selesai | Email Pribadi Peserta |

---

## 3. Logika Perubahan Status (*State Machine*)

Perubahan status (*lifecycle*) peserta diatur oleh tindakan Admin dan eksekusi otomatis di latar belakang (*Cron Job/Task Scheduling*).

1. **`Menunggu Review` ➔ `Belum Aktif`** *Terjadi saat:* Admin menekan "Terima" dan mengonfirmasi pengiriman Surat Penerimaan.
2. **`Belum Aktif` ➔ `Aktif`** *Terjadi saat:* Sistem mengecek bahwa tanggal hari ini telah memasuki `Tanggal Mulai`.
3. **`Aktif` ➔ `Selesai`** *Terjadi secara otomatis jika 2 syarat terpenuhi:* (a) Tanggal hari ini sudah melewati `Tanggal Selesai`, **DAN** (b) Ketiga dokumen (SK, Evaluasi, Sertifikat) berstatus sudah dikirim (`is_sk_sent = 1`, `is_evaluasi_sent = 1`, `is_sertifikat_sent = 1`).
4. **`Anulir`** *Terjadi saat:* Admin menolak lamaran, atau peserta membatalkan diri sebelum/saat magang berjalan.

---

## 4. Kebutuhan Teknis Database

Agar sistem otomatisasi berjalan lancar, tabel `peserta_magang` wajib memiliki kolom tambahan berikut:
* `status_magang` (Enum: Menunggu Review, Belum Aktif, Aktif, Selesai, Anulir)
* `is_sk_sent` (Boolean, Default: 0)
* `is_evaluasi_sent` (Boolean, Default: 0)
* `is_sertifikat_sent` (Boolean, Default: 0)
