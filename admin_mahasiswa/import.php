<?php
require_once '../database/config.php';
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  $_SESSION['alert_warning'] = 'Akses ditolak';
  header("Location: ../admin_mahasiswa");
  exit;
}

if (!isset($_FILES['file_excel'])) {
  $_SESSION['alert_warning'] = 'File Excel wajib diupload';
  header("Location: ../admin_mahasiswa");
  exit;
}

$file = $_FILES['file_excel']['tmp_name'];
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

$duplikat = [];
$berhasil = 0;

mysqli_begin_transaction($conn);

try {

  /* ================= AMBIL PERIODE AKTIF ================= */
  $qPeriode = mysqli_query($conn, "SELECT id_periode FROM tbl_periode ORDER BY id_periode DESC LIMIT 1");

  if (mysqli_num_rows($qPeriode) == 0) {
    throw new Exception("Periode aktif belum tersedia");
  }

  $id_periode = mysqli_fetch_assoc($qPeriode)['id_periode'];
  $now = date('Y-m-d H:i:s');

  /* ================= LOOP DATA EXCEL ================= */
  for ($i = 1; $i < count($rows); $i++) {

    $row = $rows[$i];

    $nim         = trim($row[1]);
    $nama        = trim($row[2]);
    $kode_prodi  = trim($row[3]);
    $angkatan    = trim($row[4]);
    $id_status   = trim($row[5]);
    $nama_dosen  = trim($row[6]);
    $aktif       = strtolower(trim($row[7])) === 'aktif' ? 1 : 0;
    $judul       = trim($row[8]);
    $nama_bidang = trim(preg_replace('/\s+/', ' ', $row[9]));

    if ($nim == '' || $nama == '') continue;

    /* ================= CEK PRODI ================= */
    $qProdi = mysqli_query($conn, "SELECT id_prodi FROM tbl_prodi WHERE kode_prodi='$kode_prodi' LIMIT 1");

    if (mysqli_num_rows($qProdi) == 0) {
      $duplikat[] = "$nim - $nama (Prodi tidak ditemukan)";
      continue;
    }

    $id_prodi = mysqli_fetch_assoc($qProdi)['id_prodi'];

    /* ================= CEK BIDANG ================= */
    $qBidang = mysqli_query($conn, "SELECT id_bidang FROM tbl_bidang_skripsi
      WHERE LOWER(TRIM(nama_bidang)) = LOWER('$nama_bidang')
        AND id_prodi = '$id_prodi'
        AND status_aktif = 'Aktif'
      LIMIT 1
    ");

    if (mysqli_num_rows($qBidang) == 0) {
      $duplikat[] = "$nim - $nama (Bidang tidak valid / tidak aktif)";
      continue;
    }

    $id_bidang = mysqli_fetch_assoc($qBidang)['id_bidang'];

    /* ================= CEK DUPLIKAT MAHASISWA ================= */
    $cekMhs = mysqli_query($conn, "SELECT nim FROM tbl_mahasiswa WHERE nim='$nim'");

    if (mysqli_num_rows($cekMhs) > 0) {
      $duplikat[] = "$nim - $nama (sudah ada)";
      continue;
    }

    /* ================= TBL USERS ================= */
    $cekUser = mysqli_query($conn, "SELECT id_user FROM tbl_users WHERE username='$nim' LIMIT 1");

    if (mysqli_num_rows($cekUser) > 0) {
      $id_user = mysqli_fetch_assoc($cekUser)['id_user'];

      mysqli_query($conn, "UPDATE tbl_users SET nama_lengkap='$nama', status='" . ($aktif ? 'aktif' : 'nonaktif') . "' WHERE id_user='$id_user'");
    } else {
      $password = password_hash($nim, PASSWORD_DEFAULT);

      mysqli_query($conn, "
        INSERT INTO tbl_users
        (username, password, nama_lengkap, role, status, created_at)
        VALUES
        ('$nim', '$password', '$nama', 'mahasiswa',
         '" . ($aktif ? 'aktif' : 'nonaktif') . "', '$now')
      ");

      $id_user = mysqli_insert_id($conn);
    }

    /* ================= CEK DOSEN ================= */
    $qDosen = mysqli_query($conn, "
      SELECT nip FROM tbl_dosen WHERE nama_dosen='$nama_dosen' LIMIT 1
    ");

    if (mysqli_num_rows($qDosen) == 0) {
      $duplikat[] = "$nim - $nama (Dosen tidak ditemukan)";
      continue;
    }

    $nip_dosen = mysqli_fetch_assoc($qDosen)['nip'];

    /* ================= TBL MAHASISWA ================= */
    mysqli_query($conn, "
      INSERT INTO tbl_mahasiswa
      (nim, nama, prodi, angkatan, dosen_pembimbing, aktif,
       created_at, updated_at, id_periode, id_status)
      VALUES
      ('$nim', '$nama', '$kode_prodi', '$angkatan', '$nip_dosen',
       '$aktif', '$now', '$now', '$id_periode', '$id_status')
    ");

    /* ================= TBL SKRIPSI ================= */
    mysqli_query($conn, "
      INSERT INTO tbl_skripsi
      (id_user, username, judul, id_status, id_periode, id_bidang, created_at, updated_at)
      VALUES
      ('$id_user', '$nim', '$judul', '$id_status',
       '$id_periode', '$id_bidang', '$now', '$now')
    ");

    $berhasil++;
  }

  mysqli_commit($conn);

  if (!empty($duplikat)) {
    $_SESSION['alert_warning'] =
      "Import selesai, beberapa data dilewati:<br>" . implode("<br>", $duplikat);
  }

  $_SESSION['alert_success'] =
    "Import berhasil<br>Total data berhasil: <b>$berhasil</b>";
} catch (Exception $e) {
  mysqli_rollback($conn);
  $_SESSION['alert_warning'] = "Import gagal<br>" . $e->getMessage();
}

header("Location: ../admin_mahasiswa");
exit;
