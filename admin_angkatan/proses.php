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
	/* ================== TAMBAH Tahun Angkatan ================== */
	if ($action === 'tambah') {

		/* ================= AMBIL INPUT ================= */
		$kode_angkatan	 	 = trim($_POST['kode_angkatan'] ?? '');
		$keterangan 	 = trim($_POST['keterangan'] ?? '');

		/* ================= VALIDASI INPUT ================= */
		if ($kode_angkatan === '' || $keterangan === '') {
			throw new Exception("Data angkatan tidak lengkap");
		}

		/* ================= INSERT angkatan BARU ================= */
		$stmt = mysqli_prepare($conn, "INSERT INTO tbl_angkatan (kode_angkatan, keterangan) VALUES (?, ?)");

		mysqli_stmt_bind_param($stmt, "is", $kode_angkatan, $keterangan);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		logAktivitas($conn, "Menambahkan Tahun Angkatan $kode_angkatan");
	}

	/* ================== HAPUS Tahun Angkatan ================== */ elseif ($action === 'hapus') {

		$kode_angkatan = decriptData($_GET['kode_angkatan']);

		$stmt = mysqli_prepare($conn, "DELETE FROM tbl_angkatan WHERE kode_angkatan=?");
		mysqli_stmt_bind_param($stmt, "i", $kode_angkatan);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		logAktivitas($conn, "Menghapus Tahun Angkatan $kode_angkatan");
	} else {
		throw new Exception("Aksi tidak valid");
	}
	mysqli_commit($conn);
	header("Location: ../admin_angkatan?status=success");
	exit;
} catch (Exception $e) {
	mysqli_rollback($conn);
	header("Location: ../admin_angkatan?status=error&msg=" . urlencode($e->getMessage()));
	exit;
}
