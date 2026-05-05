<?php
$lines = file('data magang final.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$header = array_shift($lines);
$names = [];
$dups = [];
$valid = 0;
$total = count($lines);
foreach ($lines as $i => $line) {
    $row = str_getcsv($line, ';');
    if (count($row) < 14) {
        // pad row to 14
        $row = array_pad($row, 14, '');
    }
    $nama = trim($row[1]);
    $nama = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $nama);
    $nama = trim($nama, " \t\n\r\0\x0B");
    if (empty($nama)) {
        echo "Skipped line $i: " . $line . "\n";
        continue;
    }
    
    $valid++;
    $key = strtolower($nama);
    if (isset($names[$key])) {
        $dups[] = $nama;
    }
    $names[$key] = true;
}

echo "Total baris (tanpa header): $total\n";
echo "Total valid rows (ada nama): $valid\n";
echo "Total nama unik: " . count($names) . "\n";
echo "Duplicates: " . json_encode($dups) . "\n";
