<?php
require_once '../database/config.php';
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
	die("Hanya admin yang bisa akses");
}

if (!isset($_FILES['file_excel'])) {
	die("File Excel harus diupload!");
}

$file = $_FILES['file_excel']['tmp_name'];
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();
$duplikat = [];
$berhasil = 0;

mysqli_begin_transaction($conn);

try {

	for ($i = 1; $i < count($rows); $i++) {

		$row = $rows[$i];

		$nip        = trim($row[1]);
		$nama_dosen = trim($row[2]);
		$aktif      = strtolower(trim($row[3])) === 'aktif' ? 1 : 0;
		$statusUser = $aktif ? 'aktif' : 'nonaktif';
		$now        = date('Y-m-d H:i:s');

		if ($nip == '' || $nama_dosen == '') continue;

		// CEK DUPLIKAT
		$cekDuplikat = mysqli_query($conn, "SELECT nip FROM tbl_dosen WHERE nip='$nip' AND nama_dosen='$nama_dosen'");

		if (!$cekDuplikat) {
			throw new Exception(mysqli_error($conn));
		}

		if (mysqli_num_rows($cekDuplikat) > 0) {
			$duplikat[] = "$nip - $nama_dosen (duplikat)";
			continue;
		}

		/* =========================
       1️⃣ TBL_DOSEN
    ========================= */
		$cekDosen = mysqli_query($conn, "SELECT nip FROM tbl_dosen WHERE nip='$nip'");

		if (mysqli_num_rows($cekDosen) > 0) {
			$sqlDosen = "UPDATE tbl_dosen SET nama_dosen='$nama_dosen', aktif='$aktif' WHERE nip='$nip' ";
		} else {
			$sqlDosen = "INSERT INTO tbl_dosen (nip, nama_dosen, aktif) VALUES ('$nip','$nama_dosen','$aktif') ";
		}

		if (!mysqli_query($conn, $sqlDosen)) {
			throw new Exception(mysqli_error($conn));
		}

		/* =========================
       2️⃣ TBL_USERS
    ========================= */
		$cekUser = mysqli_query($conn, "SELECT id_user FROM tbl_users WHERE username='$nip'");

		$password = sha1($nip);

		if (mysqli_num_rows($cekUser) > 0) {
			$sqlUser = "UPDATE tbl_users SET nama_lengkap='$nama_dosen', status='$statusUser' WHERE username='$nip' ";
		} else {
			$sqlUser = "INSERT INTO tbl_users (username, password, nama_lengkap, role, status, created_at)
        VALUES ('$nip','$password','$nama_dosen','dosen','$statusUser','$now')";
		}

		if (!mysqli_query($conn, $sqlUser)) {
			throw new Exception(mysqli_error($conn));
		}
		$berhasil++; // ✅ DATA VALID
	}

	mysqli_commit($conn);

	if (!empty($duplikat)) {
		$_SESSION['alert_warning'] =
			"Import selesai, beberapa data dilewati:<br>" .
			implode("<br>", $duplikat);
	}

	$_SESSION['alert_success'] =
		"Import berhasil 🎉<br>Total data berhasil diproses: <b>$berhasil</b>";
} catch (Exception $e) {
	mysqli_rollback($conn);
	$_SESSION['alert_warning'] = "Import gagal ❌<br>" . $e->getMessage();
}

header("Location: ../admin_dosen");
exit;
