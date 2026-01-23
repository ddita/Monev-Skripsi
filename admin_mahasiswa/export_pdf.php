<?php
require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Query data mahasiswa
$qMhs = mysqli_query($conn, "
SELECT 
    m.nim, 
    m.nama, 
    sk.judul, 
    a.keterangan AS angkatan, 
    COALESCE(st.status,'Draft') AS status_skripsi, 
    d.nama_dosen, 
    m.aktif
FROM tbl_mahasiswa m
LEFT JOIN tbl_skripsi sk ON sk.username = m.nim
LEFT JOIN tbl_status st ON st.id = sk.status_skripsi
LEFT JOIN tbl_angkatan a ON m.angkatan = a.kode_angkatan
LEFT JOIN tbl_dosen d ON m.dosen_pembimbing = d.nip
ORDER BY m.nama ASC
") or die(mysqli_error($conn));

// Inisialisasi TCPDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetTitle('Data Mahasiswa');
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(true, 10);
$pdf->AddPage();

// HTML
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
while ($m = mysqli_fetch_assoc($qMhs)) {
	$html .= '
    <tr>
        <td>' . $no++ . '</td>
        <td>' . $m['nim'] . '</td>
        <td>' . $m['nama'] . '</td>
        <td>' . $m['judul'] . '</td>
        <td>' . $m['angkatan'] . '</td>
        <td>' . $m['status_skripsi'] . '</td>
        <td>' . $m['nama_dosen'] . '</td>
        <td>' . ($m['aktif'] ? 'Aktif' : 'Nonaktif') . '</td>
    </tr>';
}

$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Data_Mahasiswa.pdf', 'I');
exit;
