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

  /* ================== TAMBAH MAHASISWA ================== */
  if ($action === 'tambah') {
    $nim        = trim($_POST['nim']);
    $nama       = trim($_POST['nama']);
    $prodi      = $_POST['prodi'];
    $angkatan   = $_POST['angkatan'];
    $id_status  = (int) $_POST['id_status'];
    $nip_dosen  = $_POST['nip_dosen'];
    $judul      = trim($_POST['judul']);
    $id_periode = (int) $_POST['id_periode'];

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
      "INSERT INTO tbl_mahasiswa (nim,nama,prodi,angkatan,status_skripsi,dosen_pembimbing,aktif,created_at,updated_at,id_periode)
    VALUES (?,?,?,?,?,?,1,NOW(),NOW(),?)"
    );

    mysqli_stmt_bind_param(
      $stmt,
      "ssssisi",
      $nim,            // s
      $nama,           // s
      $prodi,          // s
      $angkatan,       // s
      $id_status,      // i  ← dari tbl_status.id
      $nip_dosen,      // s
      $id_periode      // i  ← dari tbl_periode.id_periode
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Insert user
    $password = sha1($nim);

    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_users (username,password,nama_lengkap,role,status,created_at)
    VALUES (?,?,?,'mahasiswa','aktif',NOW())");

    mysqli_stmt_bind_param($stmt, "sss", $nim, $password, $nama);
    mysqli_stmt_execute($stmt);
    $id_user = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Insert skripsi
    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_skripsi (id_user,username,judul,id_status,id_periode,created_at,updated_at)
    VALUES (?,?,?,?,?,NOW(),NOW())");

    mysqli_stmt_bind_param($stmt,"issii",$id_user,$nim,$judul,$id_status,$id_periode);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    logAktivitas($conn, "Menambahkan mahasiswa $nim");
  }

  /* ================== EDIT MAHASISWA ================== */ elseif ($action === 'edit') {

    $nim              = trim($_POST['nim']);
    $prodi            = $_POST['prodi'];
    $angkatan         = $_POST['angkatan'];
    $id_status        = (int) $_POST['id_status'];
    $id_periode       = (int) $_POST['id_periode'];
    $dosen_pembimbing = $_POST['nip_dosen'];
    $judul            = trim($_POST['judul']);
    $aktif            = (int) $_POST['aktif'];

    /* ================= UPDATE TBL_MAHASISWA ================= */
    $stmt = mysqli_prepare($conn, "UPDATE tbl_mahasiswa SET prodi=?,angkatan=?,id_status=?,dosen_pembimbing=?,id_periode=?,aktif=?,updated_at=NOW()WHERE nim=?");

    mysqli_stmt_bind_param($stmt,"ssisiis",$prodi,$angkatan,$id_status,$dosen_pembimbing,$id_periode,$aktif,$nim);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    /* ================= UPDATE TBL_SKRIPSI ================= */
    $stmt = mysqli_prepare($conn,"UPDATE tbl_skripsi SET judul=?,id_status=?,id_periode=?,updated_at=NOW()WHERE username=?");

    mysqli_stmt_bind_param($stmt,"siis",$judul,$id_status,$id_periode,$nim);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    logAktivitas($conn, "Mengedit data mahasiswa $nim");
  }

  /* ================== TOGGLE AKTIF / NONAKTIF MAHASISWA ================== */ elseif ($action === 'toggle') {

    if (!isset($_GET['nim'])) {
      throw new Exception("NIM tidak ditemukan");
    }

    $nim = decriptData($_GET['nim']);
    if (!$nim) {
      throw new Exception("NIM tidak valid");
    }

    $nim = mysqli_real_escape_string($conn, $nim);

    // Ambil status saat ini
    $q = mysqli_query($conn, "SELECT aktif FROM tbl_mahasiswa WHERE nim='$nim'");
    if (!$q || mysqli_num_rows($q) == 0) {
      throw new Exception("Data mahasiswa tidak ditemukan");
    }

    $row = mysqli_fetch_assoc($q);
    $aktif_sekarang = (int)$row['aktif'];

    // 🔄 Toggle status
    if ($aktif_sekarang === 1) {
      $aktif_baru  = 0;
      $status_user = 'nonaktif';
      $log = "Menonaktifkan mahasiswa $nim";
    } else {
      $aktif_baru  = 1;
      $status_user = 'aktif';
      $log = "Mengaktifkan kembali mahasiswa $nim";
    }

    // 🔹 Update tbl_mahasiswa
    $q1 = mysqli_query(
      $conn,
      "UPDATE tbl_mahasiswa SET aktif='$aktif_baru' WHERE nim='$nim'"
    );

    // 🔹 Update tbl_users
    $q2 = mysqli_query(
      $conn,
      "UPDATE tbl_users SET status='$status_user' WHERE username='$nim'"
    );

    if (!$q1 || !$q2) {
      throw new Exception(mysqli_error($conn));
    }

    logAktivitas($conn, $log);
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
