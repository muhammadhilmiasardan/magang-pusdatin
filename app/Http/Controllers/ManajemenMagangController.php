<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesertaMagang;
use App\Models\TimKerja;

class ManajemenMagangController extends Controller
{
    public function index()
    {
        $peserta = PesertaMagang::with(['timKerja1'])->orderBy('created_at', 'desc')->get();
        
        $grouped = [
            'belum_aktif' => $peserta->where('status_magang', 'Belum Aktif'),
            'aktif' => $peserta->where('status_magang', 'Aktif'),
            'selesai' => $peserta->where('status_magang', 'Selesai'),
            'anulir' => $peserta->where('status_magang', 'Anulir'),
            'ditolak' => $peserta->where('status_magang', 'Ditolak'),
        ];

        $timKerja = TimKerja::orderBy('nama_tim')->get();

        return view('admin.manajemen.index', compact('grouped', 'timKerja'));
    }

    /**
     * Menyimpan peserta baru yang ditambahkan secara manual oleh admin.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'                => 'required|string|max:255',
            'tingkat_pendidikan'  => 'required|in:SMA/SMK,D3,D4,S1,S2,S3',
            'nim_nis'             => 'nullable|string|max:50',
            'nama_institusi'      => 'required|string|max:255',
            'jurusan'             => 'required|string|max:255',
            'email'               => 'required|email|max:255',
            'email_institusi'     => 'nullable|email|max:255',
            'nomor_telp'          => 'required|string|max:20',
            'id_tim_kerja_1'      => 'required|exists:tim_kerja,id',
            'id_tim_kerja_2'      => 'nullable|exists:tim_kerja,id|different:id_tim_kerja_1',
            'tanggal_mulai'       => 'required|date',
            'tanggal_selesai'     => 'required|date|after_or_equal:tanggal_mulai',
            'status'              => 'required|in:Belum Aktif,Aktif,Selesai,Anulir,Ditolak',
        ], [
            'nama.required'               => 'Nama lengkap wajib diisi.',
            'tingkat_pendidikan.required' => 'Tingkat pendidikan wajib dipilih.',
            'tingkat_pendidikan.in'       => 'Tingkat pendidikan tidak valid.',
            'nama_institusi.required'     => 'Institusi wajib diisi.',
            'jurusan.required'            => 'Jurusan wajib diisi.',
            'email.required'              => 'Email wajib diisi.',
            'email.email'                 => 'Format email tidak valid.',
            'email_institusi.email'       => 'Format email institusi tidak valid.',
            'nomor_telp.required'         => 'Nomor WhatsApp wajib diisi.',
            'id_tim_kerja_1.required'     => 'Tim kerja penempatan (pilihan 1) wajib dipilih.',
            'id_tim_kerja_1.exists'       => 'Tim kerja pilihan 1 tidak valid.',
            'id_tim_kerja_2.exists'       => 'Tim kerja pilihan 2 tidak valid.',
            'id_tim_kerja_2.different'    => 'Tim kerja pilihan 2 harus berbeda dengan pilihan 1.',
            'tanggal_mulai.required'      => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required'    => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'status.required'             => 'Status wajib dipilih.',
            'status.in'                   => 'Status tidak valid.',
        ]);

        $peserta = PesertaMagang::create([
            'nama'               => $validated['nama'],
            'tingkat_pendidikan' => $validated['tingkat_pendidikan'],
            'nim_nis'            => $validated['nim_nis'] ?? null,
            'nama_institusi'     => $validated['nama_institusi'],
            'jurusan'            => $validated['jurusan'],
            'email'              => $validated['email'],
            'email_institusi'    => $validated['email_institusi'] ?? '',
            'nomor_telp'         => $validated['nomor_telp'],
            'id_tim_kerja_1'     => $validated['id_tim_kerja_1'],
            'id_tim_kerja_2'     => $validated['id_tim_kerja_2'] ?? $validated['id_tim_kerja_1'],
            'tanggal_mulai'      => $validated['tanggal_mulai'],
            'tanggal_selesai'    => $validated['tanggal_selesai'],
            'status_magang'      => $validated['status'],
            'surat_rekomendasi'  => '',  // tambah manual tidak punya berkas fisik
        ]);

        \App\Models\ActivityLog::log('tambah_manual', 'menambahkan peserta magang baru secara manual atas nama **' . $peserta->nama . '**.');

        return redirect()->route('admin.manajemen.index')
            ->with('success', "Peserta \"{$peserta->nama}\" berhasil ditambahkan.");
    }

    public function show($id)
    {
        $peserta = PesertaMagang::with(['timKerja1', 'timKerja2'])->findOrFail($id);
        return response()->json($peserta);
    }

    /**
     * Mengundurkan diri — ubah status peserta menjadi Anulir.
     * Hanya bisa dilakukan jika status saat ini Belum Aktif atau Aktif.
     */
    public function anulir(Request $request, $id)
    {
        $peserta = PesertaMagang::findOrFail($id);

        if (!in_array($peserta->status_magang, ['Belum Aktif', 'Aktif'])) {
            return response()->json(['error' => 'Hanya peserta Belum Aktif atau Aktif yang bisa mengundurkan diri.'], 422);
        }

        $peserta->status_magang = 'Anulir';
        $peserta->save();

        \App\Models\ActivityLog::log('anulir', 'menganulir peserta magang atas nama **' . $peserta->nama . '**.');

        return response()->json(['success' => true, 'message' => "{$peserta->nama} telah mengundurkan diri (Anulir)."]);
    }

    public function export(Request $request)
    {
        $query = PesertaMagang::with(['timKerja1']);

        if ($request->has('statuses') && is_array($request->statuses)) {
            $query->whereIn('status_magang', $request->statuses);
        }

        $rentang = $request->rentang_waktu;
        $tahun   = $request->tahun;

        // Logika OVERLAP: peserta dianggap "aktif di periode ini" jika:
        //   tanggal_mulai  <= akhir_periode  (sudah mulai sebelum/saat periode berakhir)
        //   tanggal_selesai >= awal_periode  (belum selesai saat periode dimulai)
        // Ini menangkap semua peserta yang masa magangnya beririsan dengan TW, bukan hanya yang mulai di TW tersebut.
        if ($rentang === 'triwulan' && $tahun) {
            $tw = (int) $request->triwulan;
            $ranges = [
                1 => ["$tahun-01-01", "$tahun-03-31"],
                2 => ["$tahun-04-01", "$tahun-06-30"],
                3 => ["$tahun-07-01", "$tahun-09-30"],
                4 => ["$tahun-10-01", "$tahun-12-31"],
            ];
            if (isset($ranges[$tw])) {
                [$awal, $akhir] = $ranges[$tw];
                $query->where('tanggal_mulai', '<=', $akhir)
                      ->where('tanggal_selesai', '>=', $awal);
            }
        } elseif ($rentang === 'tahunan' && $tahun) {
            // Tahunan: peserta yang masa magangnya beririsan dengan tahun tersebut
            $query->where('tanggal_mulai', '<=', "$tahun-12-31")
                  ->where('tanggal_selesai', '>=', "$tahun-01-01");
        }

        $peserta = $query->orderBy('tanggal_mulai', 'asc')->get();

        // Readable labels
        $twLabels = [
            1 => 'Triwulan I (Januari – Maret)',
            2 => 'Triwulan II (April – Juni)',
            3 => 'Triwulan III (Juli – September)',
            4 => 'Triwulan IV (Oktober – Desember)',
        ];

        if ($rentang === 'triwulan' && $tahun) {
            $filterLabel = ($twLabels[(int)$request->triwulan] ?? '') . ' Tahun ' . $tahun;
        } elseif ($rentang === 'tahunan' && $tahun) {
            $filterLabel = 'Tahun ' . $tahun;
        } else {
            $filterLabel = 'Semua Waktu';
        }

        $statusesLabel = ($request->has('statuses') && is_array($request->statuses))
            ? implode(', ', $request->statuses)
            : 'Semua Status';

        $tanggalCetak = \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm');
        $fileName     = 'laporan_peserta_magang_' . date('Ymd_His') . '.xls';

        $headers = [
            'Content-type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($peserta, $filterLabel, $statusesLabel, $tanggalCetak) {
            $file = fopen('php://output', 'w');

            // Count per status for summary
            $counts = [];
            foreach ($peserta as $p) {
                $counts[$p->status_magang] = ($counts[$p->status_magang] ?? 0) + 1;
            }

            $rows = '';
            foreach ($peserta as $index => $row) {
                $statusStyle = match ($row->status_magang) {
                    'Aktif'       => 'color:#15803d;font-weight:bold',
                    'Selesai'     => 'color:#1d4ed8;font-weight:bold',
                    'Belum Aktif' => 'color:#92400e;font-weight:bold',
                    'Anulir'      => 'color:#dc2626;font-weight:bold',
                    'Ditolak'     => 'color:#dc2626;font-weight:bold',
                    default       => '',
                };
                $bgRow   = ($index % 2 === 0) ? '#ffffff' : '#f8fafc';
                $mulai   = $row->tanggal_mulai   ? \Carbon\Carbon::parse($row->tanggal_mulai)->locale('id')->isoFormat('D MMM YYYY')   : '-';
                $selesai = $row->tanggal_selesai ? \Carbon\Carbon::parse($row->tanggal_selesai)->locale('id')->isoFormat('D MMM YYYY') : '-';

                $rows .= sprintf(
                    '<tr style="background:%s">
                        <td style="text-align:center;color:#94a3b8;border:1px solid #e2e8f0">%d</td>
                        <td style="border:1px solid #e2e8f0;font-weight:600">%s</td>
                        <td style="border:1px solid #e2e8f0">%s</td>
                        <td style="border:1px solid #e2e8f0">%s</td>
                        <td style="border:1px solid #e2e8f0">%s</td>
                        <td style="border:1px solid #e2e8f0;%s">%s</td>
                        <td style="border:1px solid #e2e8f0;white-space:nowrap">%s</td>
                        <td style="border:1px solid #e2e8f0;white-space:nowrap">%s</td>
                        <td style="border:1px solid #e2e8f0">%s</td>
                        <td style="border:1px solid #e2e8f0;white-space:nowrap">%s</td>
                    </tr>',
                    $bgRow,
                    $index + 1,
                    htmlspecialchars($row->nama ?? '-'),
                    htmlspecialchars($row->nama_institusi ?? '-'),
                    htmlspecialchars($row->jurusan ?? '-'),
                    htmlspecialchars($row->timKerja1 ? $row->timKerja1->nama_tim : '-'),
                    $statusStyle,
                    htmlspecialchars($row->status_magang ?? '-'),
                    $mulai,
                    $selesai,
                    htmlspecialchars($row->email ?? '-'),
                    htmlspecialchars($row->nomor_telp ?? '-')
                );
            }

            $summaryParts = [];
            foreach ($counts as $status => $count) {
                $summaryParts[] = htmlspecialchars($status) . ': <b>' . $count . '</b>';
            }
            $summaryStr = implode('&nbsp;&nbsp;|&nbsp;&nbsp;', $summaryParts);

            $html = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
</head><body style="font-family:Calibri,Arial,sans-serif;font-size:11pt;color:#1e293b;margin:20px">

<p style="font-size:16pt;font-weight:bold;color:#1e3a8a;margin:0">REKAPITULASI DATA PESERTA MAGANG</p>
<p style="font-size:10pt;color:#475569;margin:4px 0 16px">Pusat Data dan Teknologi Informasi (PUSDATIN) &ndash; Kementerian Pekerjaan Umum</p>

<table border="0" cellspacing="0" cellpadding="3" style="font-size:10pt;margin-bottom:16px">
    <tr>
        <td style="color:#64748b;font-weight:bold;padding-right:4px">Periode Laporan</td>
        <td style="padding-right:24px">: ' . htmlspecialchars($filterLabel) . '</td>
        <td style="color:#64748b;font-weight:bold;padding-right:4px">Status Disertakan</td>
        <td>: ' . htmlspecialchars($statusesLabel) . '</td>
    </tr>
    <tr>
        <td style="color:#64748b;font-weight:bold;padding-right:4px">Tanggal Cetak</td>
        <td style="padding-right:24px">: ' . htmlspecialchars($tanggalCetak) . ' WIB</td>
        <td style="color:#64748b;font-weight:bold;padding-right:4px">Total Peserta</td>
        <td>: <b>' . $peserta->count() . ' orang</b></td>
    </tr>
</table>

<table cellspacing="0" cellpadding="0" style="border-collapse:collapse;width:100%">
    <thead>
        <tr style="background:#1e3a8a;color:#ffffff">
            <th style="padding:8px 6px;border:1px solid #1e40af;text-align:center;font-size:10pt;white-space:nowrap">No</th>
            <th style="padding:8px 10px;border:1px solid #1e40af;text-align:left;font-size:10pt">Nama Lengkap</th>
            <th style="padding:8px 10px;border:1px solid #1e40af;text-align:left;font-size:10pt">Institusi / Asal Kampus</th>
            <th style="padding:8px 10px;border:1px solid #1e40af;text-align:left;font-size:10pt">Jurusan</th>
            <th style="padding:8px 10px;border:1px solid #1e40af;text-align:left;font-size:10pt">Tim Kerja Penempatan</th>
            <th style="padding:8px 10px;border:1px solid #1e40af;text-align:center;font-size:10pt">Status</th>
            <th style="padding:8px 10px;border:1px solid #1e40af;text-align:center;font-size:10pt;white-space:nowrap">Tgl Mulai</th>
            <th style="padding:8px 10px;border:1px solid #1e40af;text-align:center;font-size:10pt;white-space:nowrap">Tgl Selesai</th>
            <th style="padding:8px 10px;border:1px solid #1e40af;text-align:left;font-size:10pt">Email</th>
            <th style="padding:8px 10px;border:1px solid #1e40af;text-align:left;font-size:10pt;white-space:nowrap">No. Telp / WhatsApp</th>
        </tr>
    </thead>
    <tbody>
        ' . $rows . '
        <tr style="background:#eff6ff">
            <td colspan="5" style="text-align:right;font-weight:bold;font-size:10pt;padding:8px 12px;border:1.5px solid #93c5fd">TOTAL PESERTA :</td>
            <td colspan="5" style="font-weight:bold;font-size:10pt;padding:8px 10px;border:1.5px solid #93c5fd">' . $peserta->count() . ' orang &nbsp;&mdash;&nbsp; ' . $summaryStr . '</td>
        </tr>
    </tbody>
</table>

<p style="font-size:8pt;color:#94a3b8;margin-top:14px">
    * Dokumen ini dicetak secara otomatis oleh Sistem Manajemen Magang PUSDATIN pada ' . htmlspecialchars($tanggalCetak) . ' WIB.<br/>
    &nbsp;&nbsp;Data diurutkan berdasarkan tanggal mulai magang (terlama ke terbaru).
</p>
</body></html>';

            fwrite($file, $html);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $fileName = 'template_import_peserta.xls';

        $html = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
<style>
    th { background: #1e3a8a; color: #ffffff; font-family: Calibri, Arial, sans-serif; font-size: 11pt; font-weight: bold; padding: 8px 12px; border: 1px solid #1e40af; text-align: left; }
    td { font-family: Calibri, Arial, sans-serif; font-size: 11pt; padding: 8px 12px; border: 1px solid #cbd5e1; }
    .text-column { mso-number-format: "\@"; }
    .info-label { color: #64748b; font-weight: bold; font-size: 10pt; font-family: Calibri, Arial, sans-serif; border: none; padding: 3px 6px; }
    .info-value { font-size: 10pt; font-family: Calibri, Arial, sans-serif; border: none; padding: 3px 6px; }
</style>
</head><body style="margin:20px">

<p style="font-size:16pt;font-weight:bold;color:#1e3a8a;margin:0">TEMPLATE IMPORT DATA PESERTA MAGANG</p>
<p style="font-size:10pt;color:#475569;margin:4px 0 16px">Pusat Data dan Teknologi Informasi (PUSDATIN) &ndash; Kementerian Pekerjaan Umum</p>

<table border="0" cellspacing="0" cellpadding="0" style="margin-bottom:20px;border:none;">
    <tr>
        <td class="info-label">Keterangan</td>
        <td class="info-value">: Template Pengisian Massal (Import)</td>
        <td class="info-label" style="padding-left:40px;">Format Tanggal</td>
        <td class="info-value">: YYYY-MM-DD (contoh: 2026-06-01)</td>
    </tr>
    <tr>
        <td class="info-label">Pilihan Status</td>
        <td class="info-value">: Belum Aktif, Aktif, Selesai, Anulir, Ditolak</td>
        <td class="info-label" style="padding-left:40px;">Tim Kerja</td>
        <td class="info-value">: BDI, MTI, TU, atau Nama Tim Kerja Lengkap (contoh: Tim Kerja Sistem Informasi)</td>
    </tr>
</table>

<table id="participant-table" cellspacing="0" cellpadding="0" style="border-collapse:collapse;width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Lengkap</th>
            <th>Institusi / Asal Kampus</th>
            <th>Jurusan</th>
            <th>Tim Kerja Penempatan</th>
            <th>Status</th>
            <th>Tgl Mulai</th>
            <th>Tgl Selesai</th>
            <th>Email</th>
            <th>No. Telp / WhatsApp</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>Muhammad Ghafiqi Radiyansyah</td>
            <td>Institut Teknologi Bandung</td>
            <td>Teknik Geodesi dan Geomatika</td>
            <td>BDI</td>
            <td>Selesai</td>
            <td class="text-column">2024-01-08</td>
            <td class="text-column">2024-02-02</td>
            <td>ghafiqi@gmail.com</td>
            <td class="text-column">-</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Mutyayasa Adji Nugroho</td>
            <td>Universitas Bina Nusantara</td>
            <td>Computer Science</td>
            <td>MTI</td>
            <td>Selesai</td>
            <td class="text-column">2024-01-01</td>
            <td class="text-column">2025-02-28</td>
            <td>adji@gmail.com</td>
            <td class="text-column">081234567890</td>
        </tr>
    </tbody>
</table>
</body></html>';

        return response($html, 200, [
            'Content-type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|max:5120']);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        $isHtml = str_contains($content, '<html') || str_contains($content, '<table');

        $rows = [];
        if ($isHtml) {
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($content);
            $table = $dom->getElementById('participant-table') ?: $dom->getElementsByTagName('table')->item(0);
            foreach ($table->getElementsByTagName('tr') as $tr) {
                $row = [];
                foreach ($tr->getElementsByTagName('td') as $cell) $row[] = trim($cell->nodeValue);
                foreach ($tr->getElementsByTagName('th') as $cell) $row[] = trim($cell->nodeValue);
                if (!empty(array_filter($row))) $rows[] = $row;
            }
        } else {
            if (($handle = fopen($path, 'r')) !== false) {
                $delimiter = str_contains(fgets($handle), ';') ? ';' : ',';
                rewind($handle);
                while (($data = fgetcsv($handle, 1000, $delimiter)) !== false) $rows[] = $data;
                fclose($handle);
            }
        }

        $headerRowIndex = -1;
        foreach ($rows as $index => $row) {
            $normalizedRow = array_map(fn($c) => strtolower(trim($c)), $row);
            if (in_array('nama mahasiswa', $normalizedRow) || in_array('nama lengkap', $normalizedRow) || in_array('nama', $normalizedRow)) {
                $headerRowIndex = $index; break;
            }
        }

        if ($headerRowIndex === -1) return back()->withErrors('Format file tidak sesuai.');

        $header = array_map(fn($h) => strtolower(trim(preg_replace('/[\x{FEFF}\x{FFFE}]/u', '', $h))), $rows[$headerRowIndex]);
        $expectedHeaders = [
            'nama' => ['nama lengkap', 'nama mahasiswa', 'nama'],
            'tingkat_pendidikan' => ['tingkat pendidikan', 'pendidikan'],
            'nim_nis' => ['nim', 'nim/nis', 'nis', 'nomor induk'],
            'nama_institusi' => ['institusi / asal kampus', 'nama institusi', 'institusi', 'asal sekolah', 'asal kampus'],
            'jurusan' => ['jurusan', 'program studi', 'prodi'],
            'email' => ['email', 'email pribadi'],
            'email_institusi' => ['email sekolah', 'email institusi', 'email kampus'],
            'nomor_telp' => ['no. telp / whatsapp', 'no. telp/whatsapp', 'nomor telp', 'no whatsapp', 'whatsapp', 'no telp', 'no. telp'],
            'tim_kerja' => ['tim kerja penempatan', 'tim kerja', 'penempatan'],
            'bidang' => ['bidang'],
            'tanggal_mulai' => ['tgl mulai', 'tanggal_mulai', 'tanggal mulai'],
            'tanggal_selesai' => ['tgl selesai', 'tanggal selesai', 'tanggal_selesai'],
            'status' => ['status', 'status magang']
        ];

        $headerMap = [];
        foreach ($expectedHeaders as $key => $aliases) {
            $headerMap[$key] = -1;
            foreach ($aliases as $alias) if (($idx = array_search($alias, $header)) !== false) { $headerMap[$key] = $idx; break; }
        }

        $map = $this->getPenempatanMap();
        $timKerjaCache = ['bidang' => [], 'tim' => []];
        $errors = []; $insertedCount = 0;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
                $data = $rows[$i];
                $val = [];
                foreach ($headerMap as $key => $idx) $val[$key] = ($idx !== -1 && isset($data[$idx])) ? preg_replace('/^="([^"]*)"$/', '$1', trim($data[$idx])) : '';

                if (empty($val['nama'])) continue;

                $emailName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '.', $val['nama']));
                $val['email'] = $val['email'] ?: $emailName . '@gmail.com';
                $val['email_institusi'] = $val['email_institusi'] ?: 'kampus@' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $val['nama_institusi'])) . '.ac.id';
                
                $phone = $this->normalizePhone($val['nomor_telp']);
                $idTimKerja1 = $this->resolveTimKerjaId(!empty($val['tim_kerja']) ? $val['tim_kerja'] : $val['bidang'], $map, $timKerjaCache) ?: (TimKerja::first()->id ?? 1);
                $idTimKerja2 = TimKerja::where('id', '!=', $idTimKerja1)->inRandomOrder()->first()->id ?? $idTimKerja1;

                $dMulai = \Carbon\Carbon::parse($val['tanggal_mulai']);
                $dSelesai = \Carbon\Carbon::parse($val['tanggal_selesai']);

                $tingkat = $val['tingkat_pendidikan'];
                if (empty($tingkat)) {
                    $inst = strtolower($val['nama_institusi']);
                    if (str_contains($inst, 'smk') || str_contains($inst, 'sma')) {
                        $tingkat = 'SMA/SMK';
                    } else {
                        $tingkat = 'Universitas';
                    }
                }

                $statusMapping = [
                    'belum aktif' => 'Belum Aktif',
                    'aktif'       => 'Aktif',
                    'selesai'     => 'Selesai',
                    'anulir'      => 'Anulir',
                    'ditolak'     => 'Ditolak',
                ];
                $rawStatus = strtolower(trim($val['status'] ?? ''));
                $statusMagang = $statusMapping[$rawStatus] ?? 'Belum Aktif';

                PesertaMagang::create([
                    'nama' => $val['nama'], 'tingkat_pendidikan' => $tingkat, 'nim_nis' => $val['nim_nis'] ?: null,
                    'nama_institusi' => $val['nama_institusi'], 'jurusan' => $val['jurusan'], 'email' => $val['email'],
                    'email_institusi' => $val['email_institusi'], 'nomor_telp' => $phone, 'id_tim_kerja_1' => $idTimKerja1,
                    'id_tim_kerja_2' => $idTimKerja2, 'tanggal_mulai' => $dMulai->format('Y-m-d'), 'tanggal_selesai' => $dSelesai->format('Y-m-d'),
                    'status_magang' => $statusMagang, 'surat_rekomendasi' => ''
                ]);
                $insertedCount++;
            }
            \Illuminate\Support\Facades\DB::commit();
            return redirect()->route('admin.manajemen.index')->with('success', "Berhasil import $insertedCount data.");
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return back()->withErrors('Error: ' . $e->getMessage());
        }
    }

    private function getPenempatanMap(): array {
        return [
            'BDI' => 'bidang:Bidang Data Analitik Pekerjaan Umum',
            'MTI' => 'bidang:Bidang Manajemen Teknologi Informasi',
            'TU'  => 'bidang:Subbagian Tata Usaha',
            'Tim Kerja Sistem Informasi' => 'Tim Kerja Sistem Informasi',
            'Tim Kerja Kepegawaian dan Jabatan Fungsional' => 'Tim Kerja Kepegawaian dan Jabatan Fungsional'
        ];
    }

    private function normalizePenempatan(string $raw): string {
        return rtrim(trim(preg_replace('/\s+/', ' ', preg_replace('/[^\x20-\x7E\x{00C0}-\x{024F}]/u', '', $raw))), ',');
    }

    private function resolveTimKerjaId(string $penempatan, array $map, array &$timKerjaCache): ?int {
        $normalized = $this->normalizePenempatan($penempatan);
        if (isset($map[$normalized])) {
            $target = $map[$normalized];
            if (str_starts_with($target, 'bidang:')) {
                $bidang = substr($target, 7);
                if (!isset($timKerjaCache['bidang'][$bidang])) $timKerjaCache['bidang'][$bidang] = TimKerja::where('bidang', $bidang)->pluck('id')->toArray();
                $ids = $timKerjaCache['bidang'][$bidang];
                return !empty($ids) ? $ids[array_rand($ids)] : null;
            }
            if (!isset($timKerjaCache['tim'][$target])) $timKerjaCache['tim'][$target] = TimKerja::where('nama_tim', $target)->first()?->id;
            return $timKerjaCache['tim'][$target];
        }

        if (!isset($timKerjaCache['tim'][$normalized])) {
            $timKerjaCache['tim'][$normalized] = TimKerja::where('nama_tim', $normalized)->first()?->id;
        }
        if ($timKerjaCache['tim'][$normalized]) {
            return $timKerjaCache['tim'][$normalized];
        }

        if (!isset($timKerjaCache['bidang'][$normalized])) {
            $timKerjaCache['bidang'][$normalized] = TimKerja::where('bidang', $normalized)->pluck('id')->toArray();
        }
        $ids = $timKerjaCache['bidang'][$normalized];
        if (!empty($ids)) {
            return $ids[array_rand($ids)];
        }

        return null;
    }

    private function normalizePhone(string $raw): string {
        if (empty($raw) || $raw === '-') return '08000000000';
        $digits = preg_replace('/[^0-9]/', '', $raw);
        if (!str_starts_with($digits, '0') && strlen($digits) >= 10) $digits = '0' . $digits;
        return $digits ?: '08000000000';
    }

    public function destroy($id)
    {
        $peserta = PesertaMagang::findOrFail($id);
        $deletedName = $peserta->nama;

        // Hapus berkas fisik jika ada
        $files = [
            $peserta->cv,
            $peserta->surat_rekomendasi,
            $peserta->surat_penerimaan_final,
            $peserta->pas_foto,
            $peserta->surat_keterangan,
            $peserta->surat_evaluasi,
            $peserta->surat_sertifikat
        ];

        foreach ($files as $file) {
            if ($file && \Illuminate\Support\Facades\Storage::disk('public')->exists($file)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
            }
        }

        $peserta->delete();

        \App\Models\ActivityLog::log('manajemen_magang', 'menghapus data peserta magang atas nama **' . $deletedName . '** secara permanen.');

        return response()->json(['success' => true, 'message' => 'Peserta "' . $deletedName . '" berhasil dihapus secara permanen.']);
    }
}
