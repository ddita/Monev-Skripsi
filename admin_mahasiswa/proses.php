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

  /* =====================================================
     ➕ TAMBAH MAHASISWA
  ===================================================== */
  if ($action === 'tambah') {

    $nim            = trim($_POST['nim']);
    $nama           = trim($_POST['nama']);
    $prodi          = $_POST['prodi'];
    $angkatan       = $_POST['angkatan'];
    $status_skripsi = (int) $_POST['status_skripsi'];
    $nip_dosen      = $_POST['nip_dosen'];
    $judul          = trim($_POST['judul']);
    $id_periode     = (int) $_POST['id_periode'];

    // Cek duplikat
    $cek = mysqli_prepare($conn, "SELECT nim FROM tbl_mahasiswa WHERE nim=?");
    mysqli_stmt_bind_param($cek, "s", $nim);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) > 0) {
      throw new Exception("Mahasiswa dengan NIM $nim sudah terdaftar");
    }
    mysqli_stmt_close($cek);

    // Insert mahasiswa
    $stmt = mysqli_prepare(
      $conn,
      "INSERT INTO tbl_mahasiswa
      (nim,nama,prodi,angkatan,status_skripsi,dosen_pembimbing,aktif,created_at,updated_at)
      VALUES (?,?,?,?,?,?,1,NOW(),NOW())"
    );
    mysqli_stmt_bind_param($stmt, "sssiss", $nim, $nama, $prodi, $angkatan, $status_skripsi, $nip_dosen);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Insert user
    $password = sha1($nim);
    $stmt = mysqli_prepare(
      $conn,
      "INSERT INTO tbl_users (username,password,nama_lengkap,role,status,created_at)
       VALUES (?,?,?,'mahasiswa','aktif',NOW())"
    );
    mysqli_stmt_bind_param($stmt, "sss", $nim, $password, $nama);
    mysqli_stmt_execute($stmt);
    $id_user = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Insert skripsi
    $stmt = mysqli_prepare(
      $conn,
      "INSERT INTO tbl_skripsi
      (id_user,username,judul,status_skripsi,id_periode,created_at,updated_at)
      VALUES (?,?,?,?,?,NOW(),NOW())"
    );
    mysqli_stmt_bind_param($stmt, "issii", $id_user, $nim, $judul, $status_skripsi, $id_periode);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    logAktivitas($conn, "Menambahkan mahasiswa $nim");
  }

  /* =====================================================
     ✏️ EDIT MAHASISWA
  ===================================================== */ elseif ($action === 'edit') {

    $nim              = $_POST['nim'];
    $nama             = $_POST['nama'];
    $prodi            = $_POST['prodi'];
    $angkatan         = $_POST['angkatan'];
    $status_skripsi   = $_POST['status_skripsi'];
    $dosen_pembimbing = $_POST['nip_dosen'];
    $aktif            = $_POST['aktif'];

    $stmt = mysqli_prepare(
      $conn,
      "UPDATE tbl_mahasiswa SET
       nama=?, prodi=?, angkatan=?, status_skripsi=?,
       dosen_pembimbing=?, aktif=?, updated_at=NOW()
       WHERE nim=?"
    );
    mysqli_stmt_bind_param(
      $stmt,
      "sssisis",
      $nama,
      $prodi,
      $angkatan,
      $status_skripsi,
      $dosen_pembimbing,
      $aktif,
      $nim
    );
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Update skripsi jika ada
    $stmt = mysqli_prepare(
      $conn,
      "UPDATE tbl_skripsi SET status_skripsi=?, updated_at=NOW()
       WHERE username=?"
    );
    mysqli_stmt_bind_param($stmt, "is", $status_skripsi, $nim);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    logAktivitas($conn, "Mengedit data mahasiswa $nim");
  }

  /* =====================================================
     🚫 NONAKTIF MAHASISWA
  ===================================================== */ elseif ($action === 'nonaktif') {

    $nim = decriptData($_GET['nim']);

    mysqli_query($conn, "UPDATE tbl_mahasiswa SET aktif=0 WHERE nim='$nim'");
    mysqli_query($conn, "UPDATE tbl_users SET status='nonaktif' WHERE username='$nim'");

    logAktivitas($conn, "Menonaktifkan mahasiswa $nim");
  }

  /* =====================================================
     🗑️ HAPUS MAHASISWA
  ===================================================== */ elseif ($action === 'hapus') {

    $nim = decriptData($_GET['nim']);

    mysqli_query($conn, "DELETE FROM tbl_skripsi WHERE username='$nim'");
    mysqli_query($conn, "DELETE FROM tbl_users WHERE username='$nim'");
    mysqli_query($conn, "DELETE FROM tbl_mahasiswa WHERE nim='$nim'");

    logAktivitas($conn, "Menghapus mahasiswa $nim");
  } else {
    throw new Exception("Aksi tidak valid");
  }

  mysqli_commit($conn);
  header("Location: ../admin_mahasiswa?status=success");
  exit;
} catch (Exception $e) {
  mysqli_rollback($conn);
  header("Location: ../admin_mahasiswa?status=error&msg=" . urlencode($e->getMessage()));
  exit;
}
