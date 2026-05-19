<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesertaMagang;

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

        return view('admin.manajemen.index', compact('grouped'));
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
}
