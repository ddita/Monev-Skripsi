<?php
session_start();
require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// ================= CEK AKSES =================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
	exit('Akses ditolak');
}

// ================= DEKRIP =================
function decriptData($data)
{
	$key = 'monev_skripsi_2024';
	return openssl_decrypt(
		base64_decode(urldecode($data)),
		'AES-128-ECB',
		$key
	);
}

// ================= VALIDASI PARAM =================
$type = $_GET['type'] ?? 'excel';
$nipEnc = $_GET['nip'] ?? '';

if (!in_array($type, ['excel', 'pdf']) || !$nipEnc) {
	die('Parameter tidak valid');
}

$nip = decriptData($nipEnc);
if (!$nip) die('Data rusak');

// ================= DATA DOSEN =================
$qDosen = mysqli_query($conn, "
  SELECT nip, nama_dosen 
  FROM tbl_dosen 
  WHERE nip='$nip'
");
$dosen = mysqli_fetch_assoc($qDosen);
if (!$dosen) die('Dosen tidak ditemukan');

// ================= QUERY MAHASISWA =================
$sql = "
  SELECT 
    m.nim,
    m.nama,
    m.prodi,
    sk.judul
  FROM tbl_mahasiswa m
  LEFT JOIN tbl_skripsi sk ON sk.username = m.nim
  WHERE m.dosen_pembimbing='$nip'
  ORDER BY m.nama ASC
";
$qMhs = mysqli_query($conn, $sql) or die(mysqli_error($conn));


// =================================================
// ================= EXPORT EXCEL ==================
// =================================================
if ($type === 'excel') {

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();

	// Judul
	$sheet->setCellValue('A1', 'Data Mahasiswa Bimbingan');
	$sheet->mergeCells('A1:E1');
	$sheet->setCellValue('A2', 'Dosen : ' . $dosen['nama_dosen']);
	$sheet->mergeCells('A2:E2');
	$sheet->setCellValue('A3', 'NIP : ' . $dosen['nip']);
	$sheet->mergeCells('A3:E3');

	// Header
	$header = ['No', 'NIM', 'Nama', 'Prodi', 'Judul Skripsi'];
	$col = 'A';
	foreach ($header as $h) {
		$sheet->setCellValue($col . '5', $h);
		$col++;
	}

	// Data
	$row = 6;
	$no  = 1;
	while ($m = mysqli_fetch_assoc($qMhs)) {
		$sheet->setCellValue("A$row", $no++);
		$sheet->setCellValue("B$row", $m['nim']);
		$sheet->setCellValue("C$row", $m['nama']);
		$sheet->setCellValue("D$row", $m['prodi']);
		$sheet->setCellValue("E$row", $m['judul'] ?: '-');
		$row++;
	}

	// Output
	$filename = 'Bimbingan_' . $dosen['nip'] . '.xlsx';
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
	$pdf->SetTitle('Data Mahasiswa Bimbingan');
	$pdf->SetMargins(10, 10, 10);
	$pdf->SetAutoPageBreak(true, 10);
	$pdf->AddPage();

	$html = '
    <h3 align="center">Data Mahasiswa Bimbingan</h3>
    <p><b>Dosen:</b> ' . htmlspecialchars($dosen['nama_dosen']) . '<br>
       <b>NIP:</b> ' . htmlspecialchars($dosen['nip']) . '</p>

    <table border="1" cellpadding="4">
    <tr style="background-color:#f2f2f2;">
      <th width="30">No</th>
      <th width="80">NIM</th>
      <th width="150">Nama</th>
      <th width="80">Prodi</th>
      <th width="250">Judul Skripsi</th>
    </tr>';

	$no = 1;
	while ($m = mysqli_fetch_assoc($qMhs)) {
		$html .= '
      <tr>
        <td>' . $no++ . '</td>
        <td>' . htmlspecialchars($m['nim']) . '</td>
        <td>' . htmlspecialchars($m['nama']) . '</td>
        <td>' . htmlspecialchars($m['prodi']) . '</td>
        <td>' . htmlspecialchars($m['judul'] ?: '-') . '</td>
      </tr>';
	}

	$html .= '</table>';

	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Bimbingan_' . $dosen['nip'] . '.pdf', 'I');
	exit;
}
