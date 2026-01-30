<?php
session_start();
require_once '../database/config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login/logout.php");
  exit;
}

function decriptData($data)
{
  $key = 'monev_skripsi_2024';
  return openssl_decrypt(
    base64_decode(urldecode($data)),
    'AES-128-ECB',
    $key
  );
}

$id_kelas = decriptData($_POST['id_kelas']);

$cek = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT s.status 
  FROM tbl_kelas_bimbingan kb
  JOIN tbl_skripsi s ON kb.id_skripsi = s.id_skripsi
  WHERE kb.id_kelas = '$id_kelas'
"));

if ($cek && $cek['id_status'] == 6) {
  $_SESSION['alert_warning'] = "Status tidak dapat diubah. Skripsi sudah SIDANG (LULUS).";
  header("Location: ../admin_kelas_bimbingan");
  exit;
}

/* ================= TAMBAH KELAS ================= */
if (isset($_POST['action']) && $_POST['action'] === 'tambah') {

  $nim     = $_POST['nim'];
  $nip     = $_POST['nip'];
  $skripsi = $_POST['id_skripsi'];
  $periode = $_POST['id_periode'];
  $status  = $_POST['status_bimbingan'];

  mysqli_query($conn, "INSERT INTO tbl_kelas_bimbingan (nim, nip, id_skripsi, id_periode, status_bimbingan, created_at)
    VALUES ('$nim','$nip','$skripsi','$periode','$status',NOW())");

  $_SESSION['alert_success'] = "Kelas bimbingan berhasil ditambahkan";
  header("Location: ../admin_kelas_bimbingan");
  exit;
}

/* ================= UPDATE STATUS ================= */ else if (isset($_POST['update_status'])) {

  $id_kelas = decriptData($_POST['id_kelas']);
  $status   = mysqli_real_escape_string($conn, $_POST['status_bimbingan']);

  mysqli_query($conn, "UPDATE tbl_kelas_bimbingan SET status_bimbingan='$status', updated_at=NOW() WHERE id_kelas='$id_kelas'");

  $_SESSION['alert_success'] = "Status kelas berhasil diperbarui";
  header("Location: ../admin_kelas_bimbingan");
  exit;
}

/* ================= HAPUS ================= */
if (isset($_GET['action']) && $_GET['action'] == 'hapus') {

  $id_kelas = decriptData($_GET['id']);

  mysqli_query($conn, "DELETE FROM tbl_kelas_bimbingan WHERE id_kelas='$id_kelas'");

  $_SESSION['alert_success'] = "Kelas bimbingan berhasil dihapus";
  header("Location: ../admin_kelas_bimbingan");
  exit;
}

header("Location: ../admin_kelas_bimbingan");
