<?php
require_once '../database/config.php';
require __DIR__ . '/../vendor/autoload.php'; // pastikan PhpSpreadsheet sudah diinstall

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Query data mahasiswa
$qMhs = mysqli_query($conn, "SELECT m.nim, m.nama, sk.judul, a.keterangan AS angkatan, COALESCE(st.status,'Draft') AS status_skripsi, d.nama_dosen, m.aktif
FROM tbl_mahasiswa m
LEFT JOIN tbl_skripsi sk ON sk.username = m.nim
LEFT JOIN tbl_status st ON st.id = sk.status_skripsi
LEFT JOIN tbl_angkatan a ON m.angkatan = a.kode_angkatan
LEFT JOIN tbl_dosen d ON m.dosen_pembimbing = d.nip
ORDER BY m.nama ASC") or die(mysqli_error($conn));

// Buat spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'NIM');
$sheet->setCellValue('C1', 'Nama');
$sheet->setCellValue('D1', 'Judul Skripsi');
$sheet->setCellValue('E1', 'Angkatan');
$sheet->setCellValue('F1', 'Status Skripsi');
$sheet->setCellValue('G1', 'Dosen Pembimbing');
$sheet->setCellValue('H1', 'Status Mahasiswa');

// Isi data
$row = 2;
$no = 1;
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

// Buat file Excel
$writer = new Xlsx($spreadsheet);
$filename = 'Data_Mahasiswa.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
$writer->save('php://output');
exit;
