<?php
require_once '../database/config.php';
require __DIR__ . '/../vendor/autoload.php'; // PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\IOFactory;

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  die("Hanya admin yang bisa akses");
}

if (!isset($_FILES['file_excel'])) {
  die("File Excel harus diupload!");
}

$file = $_FILES['file_excel']['tmp_name'];

// Load Excel
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

mysqli_begin_transaction($conn);

try {
  // Ambil periode aktif (WAJIB ADA)
  $qPeriode = mysqli_query(
    $conn,
    "SELECT id_periode 
   FROM tbl_periode 
   ORDER BY id_periode DESC 
   LIMIT 1"
  );

  if (!$qPeriode) {
    throw new Exception("Query periode gagal: " . mysqli_error($conn));
  }

  if (mysqli_num_rows($qPeriode) == 0) {
    throw new Exception("Data periode kosong");
  }

  $id_periode = mysqli_fetch_assoc($qPeriode)['id_periode'];

  for ($i = 1; $i < count($rows); $i++) {
    $row = $rows[$i];

    $nim            = trim($row[1]);
    $nama           = trim($row[2]);
    $prodi          = trim($row[3]);
    $angkatan       = trim($row[4]);
    $status_skripsi = trim($row[5]);
    $nama_dosen     = trim($row[6]);
    $aktif          = strtolower(trim($row[7])) == 'aktif' ? 1 : 0;
    $judul          = trim($row[8]);
    $now            = date('Y-m-d H:i:s');

    // 1️⃣ tbl_users dulu
    $checkUser = mysqli_query($conn, "SELECT id_user FROM tbl_users WHERE username='$nim'");
    if (mysqli_num_rows($checkUser) > 0) {
      $user = mysqli_fetch_assoc($checkUser);
      $id_user = $user['id_user'];
      $sqlUser = "UPDATE tbl_users 
                        SET nama_lengkap='$nama', status='" . ($aktif ? 'aktif' : 'nonaktif') . "', updated_at='$now'
                        WHERE username='$nim'";
      if (!mysqli_query($conn, $sqlUser)) throw new Exception(mysqli_error($conn));
    } else {
      $password = password_hash('123456', PASSWORD_DEFAULT);
      $sqlUser = "INSERT INTO tbl_users (username, password, nama_lengkap, role, status, created_at)
                        VALUES ('$nim','$password','$nama','mahasiswa','" . ($aktif ? 'aktif' : 'nonaktif') . "','$now')";
      if (!mysqli_query($conn, $sqlUser)) throw new Exception(mysqli_error($conn));

      // Ambil id_user baru
      $id_user = mysqli_insert_id($conn);
    }

    // 2️⃣ tbl_mahasiswa
    $checkMhs = mysqli_query($conn, "SELECT nim FROM tbl_mahasiswa WHERE nim='$nim'");
    $nip_dosen = "(SELECT nip FROM tbl_dosen WHERE nama_dosen='$nama_dosen')";
    if (mysqli_num_rows($checkMhs) > 0) {
      $sqlMhs = "UPDATE tbl_mahasiswa 
                       SET nama='$nama', prodi='$prodi', angkatan='$angkatan', status_skripsi='$status_skripsi', dosen_pembimbing=$nip_dosen, aktif='$aktif', updated_at='$now'
                       WHERE nim='$nim'";
    } else {
      $sqlMhs = "INSERT INTO tbl_mahasiswa (nim, nama, prodi, angkatan, status_skripsi, dosen_pembimbing, aktif, created_at, updated_at)
                       VALUES ('$nim','$nama','$prodi','$angkatan','$status_skripsi',$nip_dosen,'$aktif','$now','$now')";
    }
    if (!mysqli_query($conn, $sqlMhs)) throw new Exception(mysqli_error($conn));

    // 3️⃣ tbl_skripsi
    $checkSkripsi = mysqli_query($conn, "SELECT username FROM tbl_skripsi WHERE username='$nim'");
    if (mysqli_num_rows($checkSkripsi) > 0) {
      $sqlSkrip = "UPDATE tbl_skripsi
             SET id_user='$id_user',
                 judul='$judul',
                 status_skripsi='$status_skripsi',
                 id_periode='$id_periode',
                 updated_at='$now'
             WHERE username='$nim'";
    } else {
      $sqlSkrip = "INSERT INTO tbl_skripsi
             (id_user, username, judul, status_skripsi, id_periode, created_at, updated_at)
             VALUES
             ('$id_user','$nim','$judul','$status_skripsi','$id_periode','$now','$now')";
    }
    if (!mysqli_query($conn, $sqlSkrip)) throw new Exception(mysqli_error($conn));
  }

  mysqli_commit($conn);
  echo "Import berhasil ✅";
} catch (Exception $e) {
  mysqli_rollback($conn);
  die("Gagal import: " . $e->getMessage());
}

// Redirect kembali ke halaman mahasiswa
header("Location: ../admin_mahasiswa?pesan=nonaktif");
