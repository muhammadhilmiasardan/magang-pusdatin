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
                    htmlspecialchars($row->email_institusi ?? '-'),
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
            <th style="padding:8px 10px;border:1px solid #1e40af;text-align:left;font-size:10pt">Email Institusi</th>
            <th style="padding:8px 10px;border:1px solid #1e40af;text-align:left;font-size:10pt;white-space:nowrap">No. Telp / WhatsApp</th>
        </tr>
    </thead>
    <tbody>
        ' . $rows . '
        <tr style="background:#eff6ff">
            <td colspan="5" style="text-align:right;font-weight:bold;font-size:10pt;padding:8px 12px;border:1.5px solid #93c5fd">TOTAL PESERTA :</td>
            <td colspan="6" style="font-weight:bold;font-size:10pt;padding:8px 10px;border:1.5px solid #93c5fd">' . $peserta->count() . ' orang &nbsp;&mdash;&nbsp; ' . $summaryStr . '</td>
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
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import');

        // Hide gridlines for a cleaner look (like the report)
        $sheet->setShowGridlines(false);

        // ─── Column Widths (manual, matching report proportions) ───
        $columnWidths = [
            'A' => 5,    // No
            'B' => 32,   // Nama Lengkap
            'C' => 30,   // Institusi
            'D' => 28,   // Jurusan
            'E' => 32,   // Tim Kerja Penempatan
            'F' => 14,   // Status
            'G' => 14,   // Tgl Mulai
            'H' => 14,   // Tgl Selesai
            'I' => 28,   // Email
            'J' => 28,   // Email Institusi
            'K' => 20,   // No. Telp / WhatsApp
        ];
        foreach ($columnWidths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        // ─── Row 1: Title (merged across all columns) ───
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT DATA PESERTA MAGANG');
        $sheet->getStyle('A1')->getFont()->setName('Calibri')->setSize(16)->setBold(true);
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('1e3a8a');
        $sheet->getStyle('A1')->getAlignment()
            ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
            ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // ─── Row 2: Subtitle (merged) ───
        $sheet->mergeCells('A2:K2');
        $sheet->setCellValue('A2', 'Pusat Data dan Teknologi Informasi (PUSDATIN) – Kementerian Pekerjaan Umum');
        $sheet->getStyle('A2')->getFont()->setName('Calibri')->setSize(10);
        $sheet->getStyle('A2')->getFont()->getColor()->setRGB('333333');
        $sheet->getRowDimension(2)->setRowHeight(18);

        // ─── Rows 3-4: Metadata info block (styled like report) ───
        $metaData = [
            ['A3' => 'Keterangan',      'C3' => ': Template Pengisian Massal (Import)',  'E3' => 'Pilihan Status', 'G3' => ': Belum Aktif, Aktif, Selesai, Anulir, Ditolak'],
            ['A4' => 'Format Tanggal',   'C4' => ': YYYY-MM-DD (contoh: 2026-06-01)',    'E4' => 'Tim Kerja',      'G4' => ': BDI, MTI, TU, atau Nama Tim Kerja Lengkap'],
        ];

        // Merge cells for metadata values so they have room
        $sheet->mergeCells('C3:D3');
        $sheet->mergeCells('G3:K3');
        $sheet->mergeCells('C4:D4');
        $sheet->mergeCells('G4:K4');

        foreach ($metaData as $rowData) {
            foreach ($rowData as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }
        }

        // Style metadata labels (bold, slate gray)
        foreach (['A3', 'A4', 'E3', 'E4'] as $cell) {
            $sheet->getStyle($cell)->getFont()->setName('Calibri')->setSize(10)->setBold(true);
            $sheet->getStyle($cell)->getFont()->getColor()->setRGB('2d2d2d');
            $sheet->getStyle($cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }
        // Style metadata values
        foreach (['C3', 'C4', 'G3', 'G4'] as $cell) {
            $sheet->getStyle($cell)->getFont()->setName('Calibri')->setSize(10);
            $sheet->getStyle($cell)->getFont()->getColor()->setRGB('000000');
            $sheet->getStyle($cell)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        }
        $sheet->getRowDimension(3)->setRowHeight(18);
        $sheet->getRowDimension(4)->setRowHeight(18);

        // ─── Row 6: Table Headers (dark blue, matching report exactly) ───
        $headerRow = 6;
        $headers = [
            'No', 'Nama Lengkap', 'Institusi / Asal Kampus', 'Jurusan',
            'Tim Kerja Penempatan', 'Status', 'Tgl Mulai', 'Tgl Selesai',
            'Email', 'Email Institusi', 'No. Telp / WhatsApp'
        ];

        $alignments = [
            'A' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // No
            'B' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,   // Nama Lengkap
            'C' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,   // Institusi
            'D' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,   // Jurusan
            'E' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,   // Tim Kerja Penempatan
            'F' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Status
            'G' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Tgl Mulai
            'H' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Tgl Selesai
            'I' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,   // Email
            'J' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,   // Email Institusi
            'K' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,   // No. Telp
        ];

        // Apply header styles as a range for consistency
        $headerRange = "A{$headerRow}:K{$headerRow}";
        $headerStyle = $sheet->getStyle($headerRange);
        $headerStyle->getFont()->setName('Calibri')->setSize(10)->setBold(true);
        $headerStyle->getFont()->getColor()->setRGB('ffffff');
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('1e3a8a');
        $headerStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setRGB('1e40af');
        $headerStyle->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);

        $col = 'A';
        foreach ($headers as $header) {
            $cell = $col . $headerRow;
            $sheet->setCellValue($cell, $header);
            $horizAlign = $alignments[$col] ?? \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
            $sheet->getStyle($cell)->getAlignment()->setHorizontal($horizAlign);
            $col++;
        }
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        // ─── Sample Data Rows ───
        $data = [
            [
                '1', 'Muhammad Ghafiqi Radiyansyah', 'Institut Teknologi Bandung', 'Teknik Geodesi dan Geomatika',
                'BDI', 'Selesai', '2024-01-08', '2024-02-02', 'ghafiqi@gmail.com', 'akademik@itb.ac.id', '081234567890'
            ],
            [
                '2', 'Mutyayasa Adji Nugroho', 'Universitas Bina Nusantara', 'Computer Science',
                'MTI', 'Selesai', '2024-01-01', '2025-02-28', 'adji@gmail.com', 'kampus@binus.ac.id', '081234567890'
            ]
        ];

        $rowNum = $headerRow + 1; // Start at row 7
        foreach ($data as $index => $row) {
            $col = 'A';
            $bgRow = ($index % 2 === 0) ? 'ffffff' : 'f8fafc'; // Zebra striping

            foreach ($row as $val) {
                $cell = $col . $rowNum;
                $sheet->setCellValueExplicit($cell, $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

                $style = $sheet->getStyle($cell);

                // Font styling
                $isBold = ($col === 'B' || $col === 'F');
                $fontColor = '000000'; // Black for all data columns

                if ($col === 'F') {
                    // Status column stays blue/colored
                    $fontColor = match ($val) {
                        'Aktif' => '15803d',
                        'Selesai' => '1d4ed8',
                        'Belum Aktif' => '92400e',
                        'Anulir', 'Ditolak' => 'dc2626',
                        default => '1d4ed8',
                    };
                }

                $style->getFont()->setName('Calibri')->setSize(10)->setBold($isBold);
                $style->getFont()->getColor()->setRGB($fontColor);

                // Row striping background
                $style->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB($bgRow);

                // Borders (light, matching report)
                $style->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)->getColor()->setRGB('e2e8f0');

                // Alignment
                $horizAlign = $alignments[$col] ?? \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                $style->getAlignment()->setHorizontal($horizAlign)->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $style->getNumberFormat()->setFormatCode('@');

                $col++;
            }
            $sheet->getRowDimension($rowNum)->setRowHeight(22);
            $rowNum++;
        }

        // ─── Footer note row (like report's footer) ───
        $footerRow = $rowNum + 1;
        $sheet->mergeCells("A{$footerRow}:K{$footerRow}");
        $sheet->setCellValue("A{$footerRow}", '* Hapus baris contoh di atas sebelum mengimpor data Anda. Isi data mulai dari baris ke-7. Kolom wajib: Nama Lengkap, Institusi, Jurusan, Tgl Mulai, Tgl Selesai. Email Institusi bersifat opsional.');
        $sheet->getStyle("A{$footerRow}")->getFont()->setName('Calibri')->setSize(9)->setItalic(true);
        $sheet->getStyle("A{$footerRow}")->getFont()->getColor()->setRGB('94a3b8');
        $sheet->getStyle("A{$footerRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getRowDimension($footerRow)->setRowHeight(20);

        // Second footer line with format hints
        $footerRow2 = $footerRow + 1;
        $sheet->mergeCells("A{$footerRow2}:K{$footerRow2}");
        $sheet->setCellValue("A{$footerRow2}", '  Format Tanggal: YYYY-MM-DD (contoh: 2026-06-01).  Pilihan Status: Belum Aktif, Aktif, Selesai, Anulir, Ditolak.  Tim Kerja: BDI, MTI, TU, atau Nama Tim Kerja Lengkap.');
        $sheet->getStyle("A{$footerRow2}")->getFont()->setName('Calibri')->setSize(9)->setItalic(true);
        $sheet->getStyle("A{$footerRow2}")->getFont()->getColor()->setRGB('94a3b8');
        $sheet->getStyle("A{$footerRow2}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)->setWrapText(true);
        $sheet->getRowDimension($footerRow2)->setRowHeight(20);

        // ─── Freeze panes (header stays visible when scrolling) ───
        $sheet->freezePane('A' . ($headerRow + 1));

        // ─── Print setup ───
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.5)->setRight(0.3)->setBottom(0.5)->setLeft(0.3);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);

        return response()->stream(
            function () use ($writer) {
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => 'attachment; filename="template_import_peserta.xls"',
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ]
        );
    }

    public function import(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|max:5120']);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $content = file_get_contents($path);

        // Deteksi jika file XLS disimpan sebagai Halaman Web Multi-Sheet (berupa frameset eksternal yang tidak memiliki data tabel di dalamnya)
        if (str_contains($content, 'Excel Workbook Frameset') || (str_contains($content, 'Excel.Sheet') && str_contains($content, '<frameset'))) {
            return back()->withErrors('File yang Anda unggah disimpan oleh Excel sebagai Halaman Web Multi-Sheet (sehingga data aslinya tersimpan di folder terpisah yang tidak ikut terunggah). Silakan buka kembali file ini di Excel, lalu pilih "Save As" dan simpan dengan tipe "Excel Workbook (*.xlsx)" atau "Excel 97-2003 Workbook (*.xls)" sebelum diunggah kembali.');
        }

        $isZip = str_starts_with($content, "PK\x03\x04");
        $isOle = str_starts_with($content, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1");
        $isBinary = $isZip || $isOle;

        $isHtml = !$isBinary && (str_contains($content, '<html') || str_contains($content, '<table') || str_contains($content, '<Workbook'));
        $tmpPath = null;
        $loadPath = $path;

        if ($isHtml) {
            // Bersihkan deklarasi DOCTYPE secara aman (termasuk internal subset) untuk menghindari security check XXE PHPSpreadsheet.
            // Jangan bersihkan deklarasi XML karena XML reader PHPSpreadsheet membutuhkannya untuk identifikasi file XML Spreadsheet 2003.
            $cleanContent = preg_replace('/<!DOCTYPE\s+[^\[>]*(\[[^\]]*\])?[^>]*>/is', '', $content);
            $tmpPath = tempnam(sys_get_temp_dir(), 'import_xls_html');
            file_put_contents($tmpPath, $cleanContent);
            $loadPath = $tmpPath;
        }

        $rows = [];
        try {
            // Buat reader berdasarkan tipe file secara otomatis
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($loadPath);
            // Redam warning parsing HTML (seperti tag kustom Microsoft Excel) agar tidak memicu exception di Laravel
            if ($reader instanceof \PhpOffice\PhpSpreadsheet\Reader\Html) {
                $reader->setSuppressLoadWarnings(true);
            }
            $spreadsheet = $reader->load($loadPath);
            $worksheet = $spreadsheet->getActiveSheet();
            
            foreach ($worksheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE); // Ambil semua sel termasuk yang kosong
                $rowData = [];
                foreach ($cellIterator as $cell) {
                    $rowData[] = $cell->getFormattedValue();
                }
                $rows[] = $rowData;
            }
        } catch (\Exception $e) {
            if ($tmpPath && file_exists($tmpPath)) unlink($tmpPath);
            return back()->withErrors('Gagal membaca file: ' . $e->getMessage() . ' Silakan gunakan template yang disediakan atau pastikan format file sesuai.');
        }

        if ($tmpPath && file_exists($tmpPath)) {
            unlink($tmpPath);
        }

        \Illuminate\Support\Facades\Log::info('Import debug rows parsed', [
            'rows_count' => count($rows),
            'first_3_rows' => array_slice($rows, 0, 3),
        ]);

        $headerRowIndex = -1;
        foreach ($rows as $index => $row) {
            $normalizedRow = array_map(fn($c) => $this->normalizeHeader($c), $row);
            if (in_array('nama mahasiswa', $normalizedRow) || in_array('nama lengkap', $normalizedRow) || in_array('nama', $normalizedRow)) {
                $headerRowIndex = $index; break;
            }
        }

        \Illuminate\Support\Facades\Log::info('Import debug header check', [
            'headerRowIndex' => $headerRowIndex,
            'headerRow' => $headerRowIndex !== -1 ? $rows[$headerRowIndex] : null,
        ]);

        if ($headerRowIndex === -1) return back()->withErrors('Format file tidak sesuai.');

        $normalizedHeader = array_map(fn($h) => $this->normalizeHeader($h), $rows[$headerRowIndex]);
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
            'status' => ['status', 'status magang', 'status peserta', 'keterangan status', 'keterangan']
        ];

        $headerMap = [];
        foreach ($expectedHeaders as $key => $aliases) {
            $headerMap[$key] = -1;
            foreach ($aliases as $alias) {
                $normAlias = $this->normalizeHeader($alias);
                $idx = array_search($normAlias, $normalizedHeader);
                if ($idx !== false) {
                    $headerMap[$key] = $idx;
                    break;
                }
            }
        }

        \Illuminate\Support\Facades\Log::info('Import debug headerMap', [
            'headerMap' => $headerMap,
        ]);

        // Validasi kolom wajib agar tidak memicu error database mentah
        $requiredKeys = [
            'nama' => 'Nama Lengkap / Nama',
            'nama_institusi' => 'Institusi / Asal Kampus',
            'jurusan' => 'Jurusan',
            'tanggal_mulai' => 'Tgl Mulai',
            'tanggal_selesai' => 'Tgl Selesai'
        ];
        
        $missingHeaders = [];
        foreach ($requiredKeys as $key => $label) {
            if ($headerMap[$key] === -1) {
                $missingHeaders[] = $label;
            }
        }
        
        if (!empty($missingHeaders)) {
            return back()->withErrors('Gagal import. Kolom wajib berikut tidak ditemukan di dalam file: ' . implode(', ', $missingHeaders));
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

                try {
                    $dMulai = \Carbon\Carbon::parse($val['tanggal_mulai']);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\DB::rollBack();
                    return back()->withErrors("Baris ke-" . ($i + 1) . ": Format tanggal mulai tidak valid ('" . $val['tanggal_mulai'] . "'). Gunakan format YYYY-MM-DD.");
                }

                try {
                    $dSelesai = \Carbon\Carbon::parse($val['tanggal_selesai']);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\DB::rollBack();
                    return back()->withErrors("Baris ke-" . ($i + 1) . ": Format tanggal selesai tidak valid ('" . $val['tanggal_selesai'] . "'). Gunakan format YYYY-MM-DD.");
                }

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
                    'belum aktif'  => 'Belum Aktif',
                    'belumaktif'   => 'Belum Aktif',
                    'pending'      => 'Belum Aktif',
                    'aktif'        => 'Aktif',
                    'active'       => 'Aktif',
                    'selesai'      => 'Selesai',
                    'done'         => 'Selesai',
                    'lulus'        => 'Selesai',
                    'anulir'       => 'Anulir',
                    'batal'        => 'Anulir',
                    'dibatalkan'   => 'Anulir',
                    'cancel'       => 'Anulir',
                    'ditolak'      => 'Ditolak',
                    'tolak'        => 'Ditolak',
                    'rejected'     => 'Ditolak',
                ];
                // Hapus karakter tersembunyi (BOM, non-breaking space, dll) lalu lowercase + trim
                $rawStatus = strtolower(trim(preg_replace('/[\x{FEFF}\x{FFFE}\x{00A0}]/u', '', $val['status'] ?? '')));
                // Hapus juga spasi ganda di dalam string
                $rawStatus = preg_replace('/\s+/', ' ', $rawStatus);
                $statusMagang = $statusMapping[$rawStatus] ?? null;
                // Jika status tidak dikenali, coba cari partial match (misal 'belum' -> 'Belum Aktif')
                if ($statusMagang === null) {
                    if (str_contains($rawStatus, 'belum')) $statusMagang = 'Belum Aktif';
                    elseif (str_contains($rawStatus, 'selesai') || str_contains($rawStatus, 'done')) $statusMagang = 'Selesai';
                    elseif (str_contains($rawStatus, 'anulir') || str_contains($rawStatus, 'batal')) $statusMagang = 'Anulir';
                    elseif (str_contains($rawStatus, 'tolak') || str_contains($rawStatus, 'reject')) $statusMagang = 'Ditolak';
                    elseif (str_contains($rawStatus, 'aktif') || str_contains($rawStatus, 'active')) $statusMagang = 'Aktif';
                    else $statusMagang = 'Belum Hacky default'; // default jika status kosong atau tidak dikenali
                }

                // fallback to Belum Aktif if still null
                if ($statusMagang === 'Belum Hacky default') {
                    $statusMagang = 'Belum Aktif';
                }

                \Illuminate\Support\Facades\Log::info('Import debug insert row', [
                    'nama' => $val['nama'],
                    'tanggal_mulai' => $val['tanggal_mulai'],
                    'raw_status_val' => $val['status'],
                    'raw_status_processed' => $rawStatus,
                    'status_magang_mapped' => $statusMagang,
                ]);

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
            return redirect()->route('admin.manajemen.index')->with('success', "Berhasil mengimpor $insertedCount data peserta magang. (Catatan: Peserta dengan status 'Belum Aktif' yang tanggal mulainya hari ini atau di masa lalu otomatis diaktifkan oleh sistem).");
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

    private function normalizeHeader(string $str): string {
        $str = strtolower($str);
        $str = preg_replace('/[\x{FEFF}\x{FFFE}\x{00A0}]/u', '', $str);
        $str = preg_replace('/[^a-z0-9]/', ' ', $str);
        $str = preg_replace('/\s+/', ' ', $str);
        return trim($str);
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
