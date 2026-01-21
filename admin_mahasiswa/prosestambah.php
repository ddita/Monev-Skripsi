<?php

/**
 * PROSES TAMBAH DATA MAHASISWA
 * Monev Skripsi
 */

session_start();
require_once '../database/config.php';

/* ================= VALIDASI AKSES ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
	header("Location: ../login/logout.php");
	exit;
}

/* ================= VALIDASI SUBMIT ================= */
if (!isset($_POST['tambahmhs'])) {
	header("Location: tambahmhs.php");
	exit;
}

/* ================= AMBIL & AMANKAN DATA ================= */
$nim        = mysqli_real_escape_string($conn, trim($_POST['nim']));
$nama       = mysqli_real_escape_string($conn, trim($_POST['nama']));
$prodi      = mysqli_real_escape_string($conn, trim($_POST['prodi']));
$angkatan   = mysqli_real_escape_string($conn, trim($_POST['angkatan']));
$status     = (int) $_POST['status'];
$nip_dosen  = mysqli_real_escape_string($conn, trim($_POST['nip_dosen']));
$aktif      = 1;

/* ================= CEK DUPLIKAT ================= */
$cek = mysqli_query($conn, "SELECT nim FROM tbl_mahasiswa WHERE nim='$nim'");
if (mysqli_num_rows($cek) > 0) {
	$_SESSION['flash'] = [
		'type' => 'error',
		'msg'  => "Mahasiswa dengan NIM $nim sudah terdaftar"
	];
	header("Location: tambahmhs.php");
	exit;
}

/* ================= VALIDASI ANGKATAN ================= */
$qAngkatan = mysqli_query($conn, "SELECT kode_angkatan FROM tbl_angkatan WHERE kode_angkatan='$angkatan'");

if (mysqli_num_rows($qAngkatan) == 0) {
	$_SESSION['flash'] = [
		'type' => 'error',
		'msg'  => "Angkatan $angkatan belum tersedia di sistem"
	];
	header("Location: tambahmhs.php");
	exit;
}

$qDosen = mysqli_query($conn, "SELECT nip FROM tbl_dosen WHERE nip='$nip_dosen'");

if (mysqli_num_rows($qDosen) == 0) {
	$_SESSION['flash'] = [
		'type' => 'error',
		'msg'  => 'Dosen pembimbing tidak ditemukan'
	];
	header("Location: tambahmhs.php");
	exit;
}

$dosen = mysqli_fetch_assoc($qDosen);
$nama_dosen = $dosen['nip'];

/* ================= INSERT TBL MAHASISWA ================= */
$query_mhs = "INSERT INTO tbl_mahasiswa (nim, nama, prodi, angkatan, status_skripsi, dosen_pembimbing, aktif) VALUES ('$nim', '$nama', '$prodi', '$angkatan', '$status', '$nama_dosen', '$aktif')";

mysqli_query($conn, $query_mhs) or die(mysqli_error($conn));

/* ================= BUAT AKUN LOGIN ================= */
$pass = sha1($nim); // password default
$role_user = 'mahasiswa';
$status_user = 'aktif';

$query_user = "INSERT INTO tbl_users (username, password, nama_lengkap, role, status, created_at) VALUES ('$nim', '$pass', '$nama', '$role_user', '$status_user', NOW())";

mysqli_query($conn, $query_user) or die(mysqli_error($conn));

/* ================= FLASH MESSAGE ================= */
$_SESSION['flash'] = [
	'type' => 'success',
	'msg'  => "Data mahasiswa <b>$nama</b> berhasil ditambahkan"
];

/* ================= REDIRECT ================= */
header("Location: ../admin_mahasiswa");
exit;
