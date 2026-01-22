<?php
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
$nim            = mysqli_real_escape_string($conn, trim($_POST['nim']));
$nama           = mysqli_real_escape_string($conn, trim($_POST['nama']));
$prodi          = mysqli_real_escape_string($conn, $_POST['prodi']);
$angkatan       = mysqli_real_escape_string($conn, $_POST['angkatan']);
$status_skripsi = (int) $_POST['status_skripsi'];
$nip_dosen      = mysqli_real_escape_string($conn, $_POST['nip_dosen']);

$judul          = mysqli_real_escape_string($conn, trim($_POST['judul']));
$id_periode     = (int) $_POST['id_periode'];

$aktif          = 1;
$created_at     = date('Y-m-d H:i:s');
$updated_at     = date('Y-m-d H:i:s');

/* ================= CEK DUPLIKAT MAHASISWA ================= */
$cek = mysqli_query($conn, "SELECT nim FROM tbl_mahasiswa WHERE nim='$nim'");
if (mysqli_num_rows($cek) > 0) {
	$_SESSION['flash'] = [
		'type' => 'error',
		'msg'  => "Mahasiswa dengan NIM <b>$nim</b> sudah terdaftar"
	];
	header("Location: tambahmhs.php");
	exit;
}

/* ================= INSERT TBL MAHASISWA ================= */
$query_mhs = "INSERT INTO tbl_mahasiswa (nim, nama, prodi, angkatan, status_skripsi, dosen_pembimbing, aktif, created_at, updated_at)
  VALUES ('$nim', '$nama', '$prodi', '$angkatan', '$status_skripsi', '$nip_dosen', '$aktif', '$created_at', '$updated_at')";

mysqli_query($conn, $query_mhs) or die(mysqli_error($conn));

/* ================= BUAT AKUN USER MAHASISWA ================= */
$password = sha1($nim); // password default
$role_user = 'mahasiswa';
$status_user = 'aktif';

$query_user = "INSERT INTO tbl_users (username, password, nama_lengkap, role, status, created_at)
  VALUES ('$nim', '$password', '$nama', '$role_user', '$status_user', '$created_at')";

mysqli_query($conn, $query_user) or die(mysqli_error($conn));

$id_user = mysqli_insert_id($conn);

/* ================= INSERT TBL SKRIPSI ================= */
$query_skripsi = "INSERT INTO tbl_skripsi (id_user, username, judul, status_skripsi, id_periode, created_at, updated_at)
  VALUES ('$id_user', '$nim', '$judul', '$status_skripsi', '$id_periode', '$created_at', '$updated_at')";

mysqli_query($conn, $query_skripsi) or die(mysqli_error($conn));

/* ================= FLASH MESSAGE ================= */
$_SESSION['flash'] = [
	'type' => 'success',
	'msg'  => "Data mahasiswa <b>$nama</b> dan skripsi berhasil ditambahkan"
];

/* ================= REDIRECT ================= */
header("Location: ../admin_mahasiswa");
exit;
