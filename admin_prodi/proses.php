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

	/* ================== EDIT TAHUN AKADEMIK ================== */ 
	else if ($action === 'edit') {

		$id_prodi     = $_POST['id_prodi'] ?? '';
		$status_aktif = $_POST['status_aktif'] ?? '';
		$jenjang      = $_POST['jenjang'] ?? '';

		// whitelist enum
		$status_valid  = ['Aktif', 'Nonaktif'];
		$jenjang_valid = ['D3', 'S1', 'S2', 'S3'];

		if (
			$id_prodi === '' ||
			!in_array($status_aktif, $status_valid) ||
			!in_array($jenjang, $jenjang_valid)
		) {
			throw new Exception("Data edit program studi tidak valid");
		}

		$stmt = mysqli_prepare($conn, "UPDATE tbl_prodi SET status_aktif = ?, jenjang = ? WHERE id_prodi = ?");

		if (!$stmt) {
			throw new Exception("Prepare gagal: " . mysqli_error($conn));
		}

		mysqli_stmt_bind_param($stmt, "ssi", $status_aktif, $jenjang, $id_prodi);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		logAktivitas($conn, "Mengubah Program Studi ID $id_prodi");
	}

	/* ================== TOGGLE AKTIF / NONAKTIF PROGRAM STUDI ================== */ 
	elseif ($action === 'toggle') {

		if (!isset($_GET['id_prodi'])) {
			throw new Exception("ID Program Studi tidak ditemukan");
		}

		$id_prodi = decriptData($_GET['id_prodi']);

		if (!$id_prodi || !is_numeric($id_prodi)) {
			throw new Exception("ID Program Studi tidak valid");
		}

		/* === Ambil status saat ini === */
		$stmt = mysqli_prepare($conn,"SELECT status_aktif FROM tbl_prodi WHERE id_prodi = ?");

		if (!$stmt) {
			throw new Exception("Prepare gagal: " . mysqli_error($conn));
		}

		mysqli_stmt_bind_param($stmt, "i", $id_prodi);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);

		if (mysqli_num_rows($result) === 0) {
			throw new Exception("Data Program Studi tidak ditemukan");
		}

		$row = mysqli_fetch_assoc($result);
		mysqli_stmt_close($stmt);

		$status_sekarang = $row['status_aktif'];

		/* === Tentukan status baru === */
		if ($status_sekarang === 'Aktif') {
			$status_baru = 'Nonaktif';
			$log = "Menonaktifkan Program Studi ID $id_prodi";
		} elseif ($status_sekarang === 'Nonaktif') {
			$status_baru = 'Aktif';
			$log = "Mengaktifkan kembali Program Studi ID $id_prodi";
		} else {
			throw new Exception("Status Program Studi tidak valid");
		}

		/* === Update status === */
		$stmt = mysqli_prepare($conn,"UPDATE tbl_prodi SET status_aktif = ? WHERE id_prodi = ?");

		if (!$stmt) {
			throw new Exception("Prepare gagal: " . mysqli_error($conn));
		}

		mysqli_stmt_bind_param($stmt, "si", $status_baru, $id_prodi);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		logAktivitas($conn, $log);
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
