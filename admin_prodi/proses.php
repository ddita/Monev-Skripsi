<?php
session_start();
require_once '../database/config.php';

/* ================== CEK LOGIN ADMIN ================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
	header("Location: ../login/logout.php");
	exit;
}

/* ================== KONFIG ================== */
date_default_timezone_set('Asia/Jakarta');

/* ================== FUNGSI UTIL ================== */
function decriptData($data)
{
	$key = 'monev_skripsi_2024';
	return openssl_decrypt(base64_decode(urldecode($data)), 'AES-128-ECB', $key);
}

function logAktivitas($conn, $ket)
{
	$usr   = $_SESSION['username'] ?? '-';
	$waktu = date('Y-m-d H:i:s');

	$stmt = mysqli_prepare($conn, "INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES (?, ?, ?)");
	mysqli_stmt_bind_param($stmt, "sss", $usr, $waktu, $ket);
	mysqli_stmt_execute($stmt);
	mysqli_stmt_close($stmt);
}

/* ================== ACTION ================== */
$action = $_GET['action'] ?? $_POST['action'] ?? '';

mysqli_begin_transaction($conn);

try {
	/* ================== TAMBAH PROGRAM STUDI ================== */
	if ($action === 'tambah') {

		/* ================= AMBIL INPUT ================= */
		$kode_prodi	 	 = trim($_POST['kode_prodi'] ?? '');
		$nama_prodi 	 = trim($_POST['nama_prodi'] ?? '');
		$jenjang    	 = $_POST['jenjang'] ?? '';
		$status_aktif  = $_POST['status_aktif'] ?? '';

		/* ================= VALIDASI INPUT ================= */
		if ($kode_prodi === '' || $jenjang === '') {
			throw new Exception("Data prodi akademik tidak lengkap");
		}

		/* ================= INSERT PRODI BARU ================= */
		$stmt = mysqli_prepare($conn, "INSERT INTO tbl_prodi (kode_prodi, nama_prodi, jenjang, status_aktif) VALUES (?, ?, ?, 'Aktif')");

		mysqli_stmt_bind_param($stmt, "sss", $kode_prodi, $nama_prodi, $jenjang);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		logAktivitas($conn, "Menambahkan Program Studi $kode_prodi");
	}

	/* ================== HAPUS PROGRAM STUDI ================== */
	elseif ($action === 'hapus') {

		$id_prodi = decriptData($_GET['id_prodi']);

		$stmt = mysqli_prepare($conn, "DELETE FROM tbl_prodi WHERE id_prodi=?");
		mysqli_stmt_bind_param($stmt, "i", $id_prodi);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		logAktivitas($conn, "Menghapus prodi ID $id_prodi");
	} else {
		throw new Exception("Aksi tidak valid");
	}
	mysqli_commit($conn);
	header("Location: ../admin_prodi?status=success");
	exit;
} catch (Exception $e) {
	mysqli_rollback($conn);
	header("Location: ../admin_prodi?status=error&msg=" . urlencode($e->getMessage()));
	exit;
}
