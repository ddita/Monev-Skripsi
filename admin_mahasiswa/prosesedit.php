<?php
session_start();
require_once '../database/config.php';

/* ================= CEK LOGIN ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login/logout.php");
  exit;
}

/* ================= VALIDASI POST ================= */
if (!isset($_POST['editmhs'])) {
  header("Location: ../admin_mahasiswa.php");
  exit;
}

/* ================= AMBIL DATA ================= */
$nim              = trim($_POST['nim']);
$nama             = trim($_POST['nama']);
$prodi            = $_POST['prodi'];
$angkatan         = $_POST['angkatan'];
$status_skripsi   = $_POST['status_skripsi'];
$dosen_pembimbing = $_POST['nip_dosen'];
$aktif            = $_POST['aktif'];
$updated_at       = date('Y-m-d H:i:s');

/* ================= TRANSAKSI ================= */
mysqli_begin_transaction($conn);

try {

  /* ================= UPDATE tbl_mahasiswa ================= */
  $stmtMhs = mysqli_prepare($conn, "
    UPDATE tbl_mahasiswa SET
      nama = ?,
      prodi = ?,
      angkatan = ?,
      status_skripsi = ?,
      dosen_pembimbing = ?,
      aktif = ?,
      updated_at = ?
    WHERE nim = ?
  ");

  mysqli_stmt_bind_param(
    $stmtMhs,
    "sssisiis",
    $nama,
    $prodi,
    $angkatan,
    $status_skripsi,
    $dosen_pembimbing,
    $aktif,
    $updated_at,
    $nim
  );

  if (!mysqli_stmt_execute($stmtMhs)) {
    throw new Exception(mysqli_error($conn));
  }

  mysqli_stmt_close($stmtMhs);

  /* ================= UPDATE tbl_skripsi (JIKA ADA) ================= */
  /* ================= CEK DATA SKRIPSI BERDASARKAN NIM (username) ================= */
  $stmtCek = mysqli_prepare($conn, "
  SELECT id_skripsi FROM tbl_skripsi WHERE username = ?
");

  if (!$stmtCek) {
    throw new Exception(mysqli_error($conn));
  }

  mysqli_stmt_bind_param($stmtCek, "s", $nim);
  mysqli_stmt_execute($stmtCek);
  $resultCek = mysqli_stmt_get_result($stmtCek);

  if ($resultCek && mysqli_num_rows($resultCek) > 0) {

    /* ================= UPDATE tbl_skripsi ================= */
    $stmtSkripsi = mysqli_prepare($conn, "
    UPDATE tbl_skripsi SET
      status_skripsi = ?,
      updated_at = ?
    WHERE username = ?
  ");

    if (!$stmtSkripsi) {
      throw new Exception(mysqli_error($conn));
    }

    mysqli_stmt_bind_param(
      $stmtSkripsi,
      "sss",
      $status_skripsi,
      $updated_at,
      $nim
    );

    if (!mysqli_stmt_execute($stmtSkripsi)) {
      throw new Exception(mysqli_error($conn));
    }

    mysqli_stmt_close($stmtSkripsi);
  }

  mysqli_stmt_close($stmtCek);

  /* ================= COMMIT ================= */
  mysqli_commit($conn);
  $success = true;
} catch (Exception $e) {

  mysqli_rollback($conn);
  $success = false;
  $error   = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Proses Edit Mahasiswa</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

  <?php if ($success): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Data mahasiswa & skripsi berhasil diperbarui',
        confirmButtonText: 'OK'
      }).then(() => {
        window.location.href = '../admin_mahasiswa';
      });
    </script>
  <?php else: ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '<?= addslashes($error) ?>',
        confirmButtonText: 'Kembali'
      }).then(() => {
        history.back();
      });
    </script>
  <?php endif; ?>

</body>

</html>