<?php
session_start();
require_once '../database/config.php';

// CEK LOGIN
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
	header("Location: ../login/logout.php");
	exit;
}

// CEK NIM
if (!isset($_GET['nim'])) {
	header("Location: ../admin_mahasiswa");
	exit;
}

// Fungsi dekripsi sama seperti di admin_mahasiswa
function decriptData($data)
{
	$key = 'monev_skripsi_2024';
	return openssl_decrypt(base64_decode(urldecode($data)), 'AES-128-ECB', $key);
}

$nim = decriptData($_GET['nim']);

// Mulai transaksi agar update konsisten
mysqli_begin_transaction($conn);

try {
	// 1️⃣ Update mahasiswa jadi nonaktif
	$qMhs = mysqli_query($conn, "UPDATE tbl_mahasiswa SET aktif = 0 WHERE nim = '$nim'");
	if (!$qMhs) throw new Exception(mysqli_error($conn));

	// 2️⃣ Update user terkait jadi 'nonaktif'
	$qUser = mysqli_query($conn, "UPDATE tbl_users SET status = 'nonaktif' WHERE username = '$nim'");
	if (!$qUser) throw new Exception(mysqli_error($conn));

	// 3️⃣ Log aktivitas
	$usr   = $_SESSION['username'] ?? '-';
	$nama  = $_SESSION['nama_user'] ?? '-';
	$waktu = date('Y-m-d H:i:s');
	$ket   = "Mahasiswa dengan NIM $nim dinonaktifkan beserta akun user terkait oleh $usr ($nama)";
	$qLog  = mysqli_query($conn, "INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES ('$usr','$waktu','$ket')");
	if (!$qLog) throw new Exception(mysqli_error($conn));

	// Commit transaksi
	mysqli_commit($conn);
} catch (Exception $e) {
	mysqli_rollback($conn);
	die("Gagal menonaktifkan mahasiswa: " . $e->getMessage());
}

// Redirect kembali ke halaman mahasiswa
header("Location: ../admin_mahasiswa?pesan=nonaktif");
exit;
