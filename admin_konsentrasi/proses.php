<?php
session_start();
require_once '../database/config.php';

/* ================== CEK LOGIN ADMIN ================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login/logout.php");
  exit;
}

date_default_timezone_set('Asia/Jakarta');

/* ================== HELPER ================== */
function decriptData($data)
{
  $key = 'monev_skripsi_2024';
  return openssl_decrypt(base64_decode(urldecode($data)), 'AES-128-ECB', $key);
}

function logAktivitas($conn, $ket)
{
  $usr   = $_SESSION['username'] ?? '-';
  $waktu = date('Y-m-d H:i:s');

  $stmt = mysqli_prepare(
    $conn,
    "INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES (?, ?, ?)"
  );
  mysqli_stmt_bind_param($stmt, "sss", $usr, $waktu, $ket);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);
}

/* ================== ACTION ================== */
$action = $_GET['action'] ?? $_POST['action'] ?? '';

mysqli_begin_transaction($conn);

try {

  /* ================== TAMBAH BIDANG KONSENTRASI ================== */
  if ($action === 'tambah_bidang') {

    $id_prodi    = $_POST['prodi'];
    $nama_bidang = trim($_POST['nama_bidang']);

    if ($id_prodi == '' || $nama_bidang == '') {
      throw new Exception("Data belum lengkap");
    }

    // Cek duplikat bidang di prodi yang sama
    $cek = mysqli_prepare($conn, "SELECT id_bidang FROM tbl_bidang_skripsi WHERE id_prodi=? AND nama_bidang=?");

    mysqli_stmt_bind_param($cek, "is", $id_prodi, $nama_bidang);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) > 0) {
      throw new Exception("Bidang konsentrasi sudah ada");
    }
    mysqli_stmt_close($cek);

    // Insert
    $stmt = mysqli_prepare($conn,"INSERT INTO tbl_bidang_skripsi (id_prodi, nama_bidang, status_aktif) VALUES (?, ?, 'Aktif')");

    if (!$stmt) {
      throw new Exception("Prepare gagal: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "is", $id_prodi, $nama_bidang);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    logAktivitas($conn, "Menambahkan bidang konsentrasi: $nama_bidang");
  }

  /* ================== TOGGLE AKTIF / NONAKTIF ================== */ 
  elseif ($action === 'toggle') {

  if (!isset($_GET['id_bidang'])) {
    throw new Exception("ID bidang tidak ditemukan");
  }

  $id = decriptData($_GET['id_bidang']);

  // Hitung jumlah mahasiswa aktif
  $qCek = mysqli_query($conn, "SELECT COUNT(m.nim) AS total FROM tbl_skripsi s
    JOIN tbl_mahasiswa m ON m.nim = s.username
    WHERE s.id_bidang = '$id'
      AND m.aktif = 1
      AND s.id_status != 6
  ");

  $total = mysqli_fetch_assoc($qCek)['total'];

  // Ambil status sekarang
  $q = mysqli_query($conn, "SELECT status_aktif FROM tbl_bidang_skripsi WHERE id_bidang='$id'");

  if (mysqli_num_rows($q) == 0) {
    throw new Exception("Data bidang tidak ditemukan");
  }

  $row = mysqli_fetch_assoc($q);

  // TOLAK AKTIF JIKA MASIH OVERLOAD
  if ($row['status_aktif'] === 'Nonaktif' && $total > 4) {
    throw new Exception("Bidang masih overload, tidak dapat diaktifkan");
  }

  $status_baru = ($row['status_aktif'] === 'Aktif')
    ? 'Nonaktif'
    : 'Aktif';

  mysqli_query($conn, "UPDATE tbl_bidang_skripsi SET status_aktif='$status_baru' WHERE id_bidang='$id'");

  logAktivitas($conn, "Mengubah status bidang ID $id menjadi $status_baru");
}

  /* ================== HAPUS BIDANG ================== */ 
  elseif ($action === 'hapus') {

    if (!isset($_GET['id_bidang'])) {
      throw new Exception("ID bidang tidak ditemukan");
    }

    $id = decriptData($_GET['id_bidang']);

    mysqli_query($conn, "DELETE FROM tbl_bidang_skripsi WHERE id_bidang='$id'");

    logAktivitas($conn, "Menghapus bidang konsentrasi ID $id");
  } else {
    throw new Exception("Aksi tidak valid");
  }

  mysqli_commit($conn);
  header("Location: ../admin_konsentrasi?status=success");
  exit;
} catch (Exception $e) {

  mysqli_rollback($conn);
  header("Location: ../admin_konsentrasi?status=error&msg=" . urlencode($e->getMessage()));
  exit;
}
