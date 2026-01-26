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

  // 🔹 Ambil periode aktif
  $qPeriode = mysqli_query(
    $conn,
    "SELECT id_periode FROM tbl_periode ORDER BY id_periode DESC LIMIT 1"
  );

  if (mysqli_num_rows($qPeriode) == 0) {
    throw new Exception("Periode aktif belum tersedia");
  }

  $id_periode = mysqli_fetch_assoc($qPeriode)['id_periode'];

  // 🔁 LOOP DATA EXCEL
  for ($i = 1; $i < count($rows); $i++) {

    $row = $rows[$i];

    $nim        = trim($row[1]);
    $nama       = trim($row[2]);
    $prodi      = trim($row[3]);
    $angkatan   = trim($row[4]);
    $id_status  = trim($row[5]);
    $nama_dosen = trim($row[6]);
    $aktif      = strtolower(trim($row[7])) == 'aktif' ? 1 : 0;
    $judul      = trim($row[8]);
    $now        = date('Y-m-d H:i:s');

    if ($nim == '' || $nama == '') continue;

    // 🚫 CEK DUPLIKAT
    $cekDuplikat = mysqli_query(
      $conn,
      "SELECT nim FROM tbl_mahasiswa WHERE nim='$nim' AND nama='$nama'"
    );

    if (mysqli_num_rows($cekDuplikat) > 0) {
      $duplikat[] = "$nim - $nama (duplikat)";
      continue;
    }

    // 🔎 CEK PRODI VALID
    $cekProdi = mysqli_query(
      $conn,
      "SELECT kode_prodi FROM tbl_prodi_old WHERE kode_prodi='$prodi'"
    );

    if (mysqli_num_rows($cekProdi) == 0) {
      $duplikat[] = "$nim - $nama (Prodi tidak valid: $prodi)";
      continue;
    }

    // 1️⃣ TBL USERS
    $cekUser = mysqli_query(
      $conn,
      "SELECT id_user FROM tbl_users WHERE username='$nim'"
    );

    if (mysqli_num_rows($cekUser) > 0) {
      $user = mysqli_fetch_assoc($cekUser);
      $id_user = $user['id_user'];

      mysqli_query(
        $conn,
        "UPDATE tbl_users 
         SET nama_lengkap='$nama',
             status='" . ($aktif ? 'aktif' : 'nonaktif') . "'
         WHERE username='$nim'"
      );
    } else {
      $password = password_hash('123456', PASSWORD_DEFAULT);
      mysqli_query(
        $conn,
        "INSERT INTO tbl_users
         (username,password,nama_lengkap,role,status,created_at)
         VALUES
         ('$nim','$password','$nama','mahasiswa',
          '" . ($aktif ? 'aktif' : 'nonaktif') . "','$now')"
      );

      $id_user = mysqli_insert_id($conn);
    }

    // 2️⃣ TBL MAHASISWA
    $nip_dosen = "(SELECT nip FROM tbl_dosen WHERE nama_dosen='$nama_dosen')";

    $cekMhs = mysqli_query(
      $conn,
      "SELECT nim FROM tbl_mahasiswa WHERE nim='$nim'"
    );

    if (mysqli_num_rows($cekMhs) > 0) {
      $sqlMhs = "
        UPDATE tbl_mahasiswa SET
          nama='$nama',
          prodi='$prodi',
          angkatan='$angkatan',
          id_status='$id_status',
          dosen_pembimbing=$nip_dosen,
          aktif='$aktif',
          updated_at='$now'
        WHERE nim='$nim'
      ";
    } else {
      $sqlMhs = "
        INSERT INTO tbl_mahasiswa
        (nim,nama,prodi,angkatan,id_status,dosen_pembimbing,aktif,created_at,updated_at)
        VALUES
        ('$nim','$nama','$prodi','$angkatan','$id_status',
         $nip_dosen,'$aktif','$now','$now')
      ";
    }

    if (!mysqli_query($conn, $sqlMhs)) {
      throw new Exception(mysqli_error($conn));
    }

    // 3️⃣ TBL SKRIPSI
    $cekSkripsi = mysqli_query(
      $conn,
      "SELECT username FROM tbl_skripsi WHERE username='$nim'"
    );

    if (mysqli_num_rows($cekSkripsi) > 0) {
      $sqlSkripsi = "
        UPDATE tbl_skripsi SET
          id_user='$id_user',
          judul='$judul',
          id_status='$id_status',
          id_periode='$id_periode',
          updated_at='$now'
        WHERE username='$nim'
      ";
    } else {
      $sqlSkripsi = "
        INSERT INTO tbl_skripsi
        (id_user,username,judul,id_status,id_periode,created_at,updated_at)
        VALUES
        ('$id_user','$nim','$judul','$id_status',
         '$id_periode','$now','$now')
      ";
    }

    if (!mysqli_query($conn, $sqlSkripsi)) {
      throw new Exception(mysqli_error($conn));
    }

    $berhasil++; // ✅ DATA VALID
  }

  mysqli_commit($conn);

  if (!empty($duplikat)) {
    $_SESSION['alert_warning'] =
      "Import selesai, beberapa data dilewati:<br>" .
      implode("<br>", $duplikat);
  }

  $_SESSION['alert_success'] =
    "Import berhasil 🎉<br>Total data berhasil diproses: <b>$berhasil</b>";

} catch (Exception $e) {
  mysqli_rollback($conn);
  $_SESSION['alert_warning'] = "Import gagal ❌<br>" . $e->getMessage();
}

header("Location: ../admin_mahasiswa");
exit;