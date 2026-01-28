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
	/* ================== TAMBAH PERIODE ================== */
	if ($action === 'tambah') {

		/* ================= AMBIL INPUT ================= */
		$nama_periode = trim($_POST['nama_periode'] ?? '');
		$semester     = $_POST['semester'] ?? '';

		/* ================= VALIDASI INPUT ================= */
		if ($nama_periode === '' || $semester === '') {
			throw new Exception("Data periode akademik tidak lengkap");
		}

		/* ================= AMBIL TAHUN AKADEMIK AKTIF ================= */
		$qTahun = mysqli_query($conn,"SELECT id_tahun, tahun_akademik FROM tbl_tahun_akademik WHERE status_aktif = 'Aktif' LIMIT 1");

		if (mysqli_num_rows($qTahun) === 0) {
			throw new Exception("Tidak ada tahun akademik yang aktif");
		}

		$tahun = mysqli_fetch_assoc($qTahun);
		$id_tahun = $tahun['id_tahun'];

		/* ================= NONAKTIFKAN PERIODE LAMA (SEMESTER SAMA) ================= */
		$stmt = mysqli_prepare($conn,"UPDATE tbl_periode SET status_aktif='Nonaktif' WHERE semester=?");
		mysqli_stmt_bind_param($stmt, "s", $semester);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		/* ================= INSERT PERIODE BARU ================= */
		$stmt = mysqli_prepare($conn,"INSERT INTO tbl_periode (id_tahun, nama_periode, semester, status_aktif, created_at) VALUES (?, ?, ?, 'Aktif', NOW())");

		mysqli_stmt_bind_param($stmt, "iss", $id_tahun, $nama_periode, $semester);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		logAktivitas($conn, "Menambahkan periode akademik $nama_periode");
	}

	/* ================== TOGGLE AKTIF / NONAKTIF PERIODE ================== */
	elseif ($action === 'toggle') {

		$id_periode = decriptData($_GET['id_periode']);
		if (!$id_periode) {
			throw new Exception("ID Periode tidak valid");
		}

		$q = mysqli_query($conn, "SELECT status_aktif FROM tbl_periode WHERE id_periode='$id_periode'");

		$row = mysqli_fetch_assoc($q);
		$status = $row['status_aktif'];

		if ($status === 'Aktif') {

			mysqli_query($conn, "UPDATE tbl_periode SET status_aktif='Nonaktif' WHERE id_periode='$id_periode'");

			$log = "Menonaktifkan Periode ID $id_periode";
		} else {
			mysqli_query($conn, "UPDATE tbl_periode SET status_aktif='Nonaktif'");
			mysqli_query($conn, "UPDATE tbl_periode SET status_aktif='Aktif' WHERE id_periode='$id_periode'");

			$log = "Mengaktifkan Periode ID $id_periode";
		}

		logAktivitas($conn, $log);
	}

	/* ================== HAPUS MAHASISWA ================== */ 
	elseif ($action === 'hapus') {

		$id_periode = decriptData($_GET['kd_prd']);

		$stmt = mysqli_prepare($conn, "DELETE FROM tbl_periode WHERE id_periode=?");
		mysqli_stmt_bind_param($stmt, "i", $id_periode);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);

		logAktivitas($conn, "Menghapus periode ID $id_periode");
	} else {
		throw new Exception("Aksi tidak valid");
	}
	mysqli_commit($conn);
	header("Location: ../admin_periode?status=success");
	exit;
} catch (Exception $e) {
	mysqli_rollback($conn);
	header("Location: ../admin_periode?status=error&msg=" . urlencode($e->getMessage()));
	exit;
}
