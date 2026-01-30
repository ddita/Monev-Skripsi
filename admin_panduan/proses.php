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

/* ================== UTIL ================== */
function logAktivitas($conn, $ket)
{
  $usr   = $_SESSION['username'] ?? '-';
  $waktu = date('Y-m-d H:i:s');

  $stmt = mysqli_prepare($conn,"INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES (?, ?, ?)");
  mysqli_stmt_bind_param($stmt, "sss", $usr, $waktu, $ket);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}

/* ================== KONFIG UPLOAD ================== */
$folder = "../upload";
$allow_ext = ['pdf', 'docx'];

/* ================== ACTION ================== */
$action = $_POST['action'] ?? $_GET['action'] ?? '';

mysqli_begin_transaction($conn);

try {

  /* ================== TAMBAH PANDUAN ================== */
  if ($action === 'tambah') {

    $judul = trim($_POST['judul']);
    $tahun = trim($_POST['tahun_akademik']);

    if (empty($_FILES['file']['name'])) {
      throw new Exception("File panduan wajib diupload");
    }

    $file = $_FILES['file']['name'];
    $tmp  = $_FILES['file']['tmp_name'];
    $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if (!in_array($ext, $allow_ext)) {
      throw new Exception("Format file harus PDF atau DOCX");
    }

    $nama_file = time() . '_' . preg_replace("/[^a-zA-Z0-9\.]/", "_", $file);

    if (!move_uploaded_file($tmp, $folder . $nama_file)) {
      throw new Exception("Gagal upload file");
    }

    $stmt = mysqli_prepare(
      $conn,
      "INSERT INTO tbl_panduan_skripsi (judul, tahun_akademik, file, uploaded_at)
       VALUES (?, ?, ?, NOW())"
    );
    mysqli_stmt_bind_param($stmt, "sss", $judul, $tahun, $nama_file);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    logAktivitas($conn, "Menambahkan panduan: $judul");
  }

  /* ================== EDIT PANDUAN ================== */
  elseif ($action === 'edit') {

    $id    = (int) $_POST['id_panduan'];
    $judul = trim($_POST['judul']);
    $tahun = trim($_POST['tahun_akademik']);
    $file_lama = $_POST['file_lama'];

    $nama_file = $file_lama;

    if (!empty($_FILES['file']['name'])) {

      $file = $_FILES['file']['name'];
      $tmp  = $_FILES['file']['tmp_name'];
      $ext  = strtolower(pathinfo($file, PATHINFO_EXTENSION));

      if (!in_array($ext, $allow_ext)) {
        throw new Exception("Format file harus PDF atau DOCX");
      }

      $nama_file = time() . '_' . preg_replace("/[^a-zA-Z0-9\.]/", "_", $file);

      if (!move_uploaded_file($tmp, $folder . $nama_file)) {
        throw new Exception("Gagal upload file baru");
      }

      if (!empty($file_lama) && file_exists($folder . $file_lama)) {
        unlink($folder . $file_lama);
      }
    }

    $stmt = mysqli_prepare(
      $conn,
      "UPDATE tbl_panduan_skripsi
       SET judul=?, tahun_akademik=?, file=?
       WHERE id_panduan=?"
    );
    mysqli_stmt_bind_param($stmt, "sssi", $judul, $tahun, $nama_file, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    logAktivitas($conn, "Mengedit panduan ID $id");
  }

  /* ================== HAPUS PANDUAN ================== */
  elseif ($action === 'hapus') {

    $id = (int) $_GET['id_panduan'];

    $q = mysqli_query($conn, "SELECT file FROM tbl_panduan_skripsi WHERE id_panduan='$id'");
    $data = mysqli_fetch_assoc($q);

    if (!$data) {
      throw new Exception("Data panduan tidak ditemukan");
    }

    if (!empty($data['file']) && file_exists($folder . $data['file'])) {
      unlink($folder . $data['file']);
    }

    mysqli_query($conn, "DELETE FROM tbl_panduan_skripsi WHERE id_panduan='$id'");

    logAktivitas($conn, "Menghapus panduan ID $id");
  }

  else {
    throw new Exception("Aksi tidak valid");
  }

  mysqli_commit($conn);
  header("Location: ../admin_panduan?status=success");
  exit;

} catch (Exception $e) {

  mysqli_rollback($conn);
  header("Location: ../admin_panduan?status=error&msg=" . urlencode($e->getMessage()));
  exit;
}
