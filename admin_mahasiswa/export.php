<?php
require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// ================= VALIDASI PARAM =================
$type = $_GET['type'] ?? '';

if (!in_array($type, ['excel', 'pdf'])) {
  die('Tipe export tidak valid');
}

// ================= QUERY DATA =================
$sql = "SELECT m.nim, m.nama, sk.judul, a.keterangan AS angkatan, COALESCE(st.status,'Draft') AS status_skripsi, d.nama_dosen, m.aktif FROM tbl_mahasiswa m
        LEFT JOIN tbl_skripsi sk ON sk.username = m.nim
        LEFT JOIN tbl_status st ON st.id = sk.status_skripsi
        LEFT JOIN tbl_angkatan a ON m.angkatan = a.kode_angkatan
        LEFT JOIN tbl_dosen d ON m.dosen_pembimbing = d.nip
        ORDER BY m.nama ASC
";

$qMhs = mysqli_query($conn, $sql) or die(mysqli_error($conn));

// =================================================
// ================= EXPORT EXCEL ==================
// =================================================
if ($type === 'excel') {

  $spreadsheet = new Spreadsheet();
  $sheet = $spreadsheet->getActiveSheet();

  // Header
  $header = [
    'No',
    'NIM',
    'Nama',
    'Judul Skripsi',
    'Angkatan',
    'Status Skripsi',
    'Dosen Pembimbing',
    'Status Mahasiswa'
  ];

  $col = 'A';
  foreach ($header as $h) {
    $sheet->setCellValue($col . '1', $h);
    $col++;
  }

  // Data
  $row = 2;
  $no  = 1;
  while ($m = mysqli_fetch_assoc($qMhs)) {
    $sheet->setCellValue("A$row", $no++);
    $sheet->setCellValue("B$row", $m['nim']);
    $sheet->setCellValue("C$row", $m['nama']);
    $sheet->setCellValue("D$row", $m['judul']);
    $sheet->setCellValue("E$row", $m['angkatan']);
    $sheet->setCellValue("F$row", $m['status_skripsi']);
    $sheet->setCellValue("G$row", $m['nama_dosen']);
    $sheet->setCellValue("H$row", $m['aktif'] ? 'Aktif' : 'Nonaktif');
    $row++;
  }

  // Output
  $filename = 'Data_Mahasiswa.xlsx';
  header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
  header("Content-Disposition: attachment; filename=\"$filename\"");
  header('Cache-Control: max-age=0');

  $writer = new Xlsx($spreadsheet);
  $writer->save('php://output');
  exit;
}


// =================================================
// ================== EXPORT PDF ===================
// =================================================
if ($type === 'pdf') {

  $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
  $pdf->SetTitle('Data Mahasiswa');
  $pdf->SetMargins(10, 10, 10);
  $pdf->SetAutoPageBreak(true, 10);
  $pdf->AddPage();

  $html = '
    <h3 align="center">Data Mahasiswa</h3>
    <table border="1" cellpadding="4">
    <tr style="background-color:#f2f2f2;">
        <th width="30">No</th>
        <th width="70">NIM</th>
        <th width="120">Nama</th>
        <th width="200">Judul Skripsi</th>
        <th width="60">Angkatan</th>
        <th width="90">Status Skripsi</th>
        <th width="120">Dosen</th>
        <th width="70">Status</th>
    </tr>';

  $no = 1;
  mysqli_data_seek($qMhs, 0); // reset pointer result
  while ($m = mysqli_fetch_assoc($qMhs)) {
    $html .= '
        <tr>
            <td>' . $no++ . '</td>
            <td>' . htmlspecialchars($m['nim']) . '</td>
            <td>' . htmlspecialchars($m['nama']) . '</td>
            <td>' . htmlspecialchars($m['judul']) . '</td>
            <td>' . htmlspecialchars($m['angkatan']) . '</td>
            <td>' . htmlspecialchars($m['status_skripsi']) . '</td>
            <td>' . htmlspecialchars($m['nama_dosen']) . '</td>
            <td>' . ($m['aktif'] ? 'Aktif' : 'Nonaktif') . '</td>
        </tr>';
  }

  $html .= '</table>';

  $pdf->writeHTML($html, true, false, true, false, '');
  $pdf->Output('Data_Mahasiswa.pdf', 'I');
  exit;
}
