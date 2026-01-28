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
	/* ================== TAMBAH STATUS SKRIPSI ================== */
	if ($action === 'tambah') {

		/* ================= AMBIL INPUT ================= */
		$id_status = trim($_POST['id'] ?? '');
		$status 	 = trim($_POST['status'] ?? '');

		/* ================= VALIDASI INPUT ================= */
		if ($id === '' || $status === '') {
			throw new Exception("Data status skripsi tidak lengkap");
		}

		/* ================= INSERT STATUS BARU ================= */
		$stmt = mysqli_prepare($conn, "INSERT INTO tbl_status (id,status) VALUES (?, ?)");

		mysqli_stmt_bind_param($stmt, "is", $id, $status);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		logAktivitas($conn, "Menambahkan Status Skripsi $id");
	}

	/* ================== HAPUS STATUS ================== */ elseif ($action === 'hapus') {

		$id = decriptData($_GET['id']);

		$stmt = mysqli_prepare($conn, "DELETE FROM tbl_status WHERE id=?");
		mysqli_stmt_bind_param($stmt, "i", $id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		logAktivitas($conn, "Menghapus Status Skripsi $id");
	} else {
		throw new Exception("Aksi tidak valid");
	}
	mysqli_commit($conn);
	header("Location: ../admin_status?status=success");
	exit;
} catch (Exception $e) {
	mysqli_rollback($conn);
	header("Location: ../admin_status?status=error&msg=" . urlencode($e->getMessage()));
	exit;
}
