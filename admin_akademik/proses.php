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

function encriptData($data)
{
  $key = 'monev_skripsi_2024';
  return urlencode(
    base64_encode(
      openssl_encrypt($data, 'AES-128-ECB', $key)
    )
  );
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

  /* ================== TAMBAH TAHUN AKADEMIK ================== */
  if ($action === 'tambah') {

    $tahun_akademik = trim($_POST['tahun_akademik']);

    if ($tahun_akademik === '') {
      throw new Exception("Tahun akademik wajib diisi");
    }

    // enum resmi
    $status_aktif = 'Aktif';
    $keterangan   = 'Tahun Berjalan';

    /* === CEK DUPLIKAT === */
    $cek = mysqli_prepare($conn, "SELECT 1 FROM tbl_tahun_akademik WHERE tahun_akademik = ?");
    mysqli_stmt_bind_param($cek, "s", $tahun_akademik);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) > 0) {
      throw new Exception("Tahun akademik $tahun_akademik sudah terdaftar");
    }
    mysqli_stmt_close($cek);

    /* === NONAKTIFKAN SEMUA === */
    mysqli_query($conn,"UPDATE tbl_tahun_akademik SET status_aktif='Nonaktif', keterangan='Arsip'");

    /* === INSERT BARU === */
    $stmt = mysqli_prepare($conn,"INSERT INTO tbl_tahun_akademik (tahun_akademik, status_aktif, keterangan) VALUES (?, ?, ?)");

    mysqli_stmt_bind_param($stmt, "sss", $tahun_akademik, $status_aktif, $keterangan);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    logAktivitas($conn, "Menambahkan Tahun Akademik $tahun_akademik");
  }

  /* ================== EDIT TAHUN AKADEMIK ================== */
  else if ($action === 'update_tahun') {

    $id_tahun     = (int)$_POST['id_tahun'];
    $status_aktif = $_POST['status_aktif'];
    $keterangan   = $_POST['keterangan'];

    // whitelist enum
    $status_valid = ['Aktif', 'Nonaktif'];
    $ket_valid    = ['Tahun Berjalan', 'Arsip'];

    if (!in_array($status_aktif, $status_valid) || !in_array($keterangan, $ket_valid)) {
      throw new Exception("Nilai status tidak valid");
    }

    if ($status_aktif === 'Aktif') {
      mysqli_query($conn,"UPDATE tbl_tahun_akademik SET status_aktif='Nonaktif', keterangan='Arsip'");
    }

    $stmt = mysqli_prepare($conn,"UPDATE tbl_tahun_akademik SET status_aktif=?, keterangan=? WHERE id_tahun=?");

    mysqli_stmt_bind_param($stmt, "ssi", $status_aktif, $keterangan, $id_tahun);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    logAktivitas($conn, "Mengubah Tahun Akademik ID $id_tahun");
  }

  /* ================== TOGGLE AKTIF / NONAKTIF TAHUN AKADEMIK ================== */
  elseif ($action === 'toggle') {

    $id_tahun = decriptData($_GET['id_tahun']);
    if (!$id_tahun) {
      throw new Exception("ID Tahun tidak valid");
    }

    $q = mysqli_query($conn,"SELECT status_aktif FROM tbl_tahun_akademik WHERE id_tahun='$id_tahun'");

    $row = mysqli_fetch_assoc($q);
    $status = $row['status_aktif'];

    if ($status === 'Aktif') {

      mysqli_query($conn,"UPDATE tbl_tahun_akademik SET status_aktif='Nonaktif', keterangan='Arsip' WHERE id_tahun='$id_tahun'");

      $log = "Menonaktifkan Tahun Akademik ID $id_tahun";
    } else {

      mysqli_query($conn,"UPDATE tbl_tahun_akademik SET status_aktif='Nonaktif', keterangan='Arsip'");

      mysqli_query($conn,"UPDATE tbl_tahun_akademik SET status_aktif='Aktif', keterangan='Tahun Berjalan' WHERE id_tahun='$id_tahun'");

      $log = "Mengaktifkan Tahun Akademik ID $id_tahun";
    }

    logAktivitas($conn, $log);
  }

  /* ================== HAPUS TAHUN AKADEMIK ================== */
  elseif ($action === 'hapus') {

    $id_tahun = decriptData($_GET['id_tahun']);

    mysqli_query($conn, "DELETE FROM tbl_tahun_akademik WHERE id_tahun='$id_tahun'");

    logAktivitas($conn, "Menghapus tahun akademik $id_tahun");
  } else {
    throw new Exception("Aksi tidak valid");
  }

  mysqli_commit($conn);
  header("Location: ../admin_akademik?status=success");
  exit;
} catch (Exception $e) {
  mysqli_rollback($conn);
  header("Location: ../admin_akademik?status=error&msg=" . urlencode($e->getMessage()));
  exit;
}
