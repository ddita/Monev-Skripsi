<?php
session_start();
$konstruktor = 'admin_kelas_bimbingan';
require_once '../database/config.php';

/* ================= CEK LOGIN ================= */
if (!isset($_SESSION['role'])) {
  header("Location: ../login/logout.php");
  exit;
}

// HARUS ADMIN
if ($_SESSION['role'] !== 'admin') {

  $usr   = $_SESSION['username'] ?? '-';
  $nama  = $_SESSION['nama_user'] ?? '-';
  $role  = $_SESSION['role'] ?? '-';
  $waktu = date('Y-m-d H:i:s');

  $ket = "Pengguna $usr ($nama) mencoba akses Detail Kelas Bimbingan sebagai $role";

  mysqli_query(
    $conn,
    "INSERT INTO tbl_cross_auth (username, waktu, keterangan)
     VALUES ('$usr','$waktu','$ket')"
  );

  header("Location: ../login/logout.php");
  exit;
}

/* ================= DEKRIP ================= */
function decriptData($data)
{
  $key = 'monev_skripsi_2024';
  return openssl_decrypt(
    base64_decode(urldecode($data)),
    'AES-128-ECB',
    $key
  );
}

/* ================= VALIDASI PARAM ================= */
if (!isset($_GET['id_kelas'])) {
  die('Parameter tidak valid');
}

$id_kelas = decriptData($_GET['id_kelas']);
if (!$id_kelas) {
  die('Data rusak');
}

/* ================= DATA KELAS ================= */
$q = mysqli_query($conn, "SELECT kb.*, m.nim, m.nama AS nama_mhs, d.nama_dosen, s.judul, p.nama_periode AS tahun_akademik FROM tbl_kelas_bimbingan kb
  LEFT JOIN tbl_mahasiswa m ON kb.nim = m.nim
  LEFT JOIN tbl_dosen d ON kb.nip = d.nip
  LEFT JOIN tbl_skripsi s ON kb.id_skripsi = s.id_skripsi
  LEFT JOIN tbl_periode p ON kb.id_periode = p.id_periode
  WHERE kb.id_kelas = '$id_kelas'
");

$data = mysqli_fetch_assoc($q);

if (!$data) {
  $_SESSION['alert_warning'] = "Data kelas bimbingan tidak ditemukan";
  header("Location: ../admin_kelas_bimbingan");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Monev Skripsi | Detail Kelas Bimbingan</title>
  <?php include '../mhs_listlink.php'; ?>
  <script>
    (function() {
      const theme = localStorage.getItem("theme") || "dark";
      document.documentElement.classList.add(theme + "-mode");
    })();
  </script>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">
  <div class="wrapper">

    <?php include '../mhs_navbar.php'; ?>
    <?php include '../admin_sidebar.php'; ?>

    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="../images/UP.png" alt="Monev-Skripsi" height="60" width="60">
    </div>

    <div class="content-wrapper">

      <!-- HEADER -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Detail Kelas Bimbingan</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item">
                  <a href="../admin_dashboard">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                  <a href="../admin_kelas_bimbingan">Kelas Bimbingan</a>
                </li>
                <li class="breadcrumb-item active">Detail</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <!-- CONTENT -->
      <section class="content">
        <div class="container-fluid">

          <a href="../admin_kelas_bimbingan" class="btn btn-warning btn-sm mb-3">
            <i class="fas fa-chevron-left"></i> Kembali
          </a>

          <div class="card">
            <!-- HEADER CARD -->
            <div class="card-header bg-info">
              <h5 class="mb-0 text-white">
                <i class="fas fa-users mr-1"></i>
                <?= htmlspecialchars($data['nama_mhs']) ?>
              </h5>
              <small class="text-white-50">
                NIM: <strong><?= htmlspecialchars($data['nim']) ?></strong>
              </small>
            </div>

            <!-- BODY -->
            <div class="card-body">
              <table class="table table-bordered">
                <tr>
                  <th width="30%">Dosen Pembimbing</th>
                  <td><?= htmlspecialchars($data['nama_dosen']) ?></td>
                </tr>
                <tr>
                  <th>Judul Skripsi</th>
                  <td><?= htmlspecialchars($data['judul'] ?? '-') ?></td>
                </tr>
                <tr>
                  <th>Periode Akademik</th>
                  <td><?= htmlspecialchars($data['tahun_akademik']) ?></td>
                </tr>
                <tr>
                  <th>Status Bimbingan</th>
                  <td>
                    <span class="badge badge-<?=
                                              $data['status_bimbingan'] == 'aktif' ? 'success' : ($data['status_bimbingan'] == 'selesai' ? 'primary' : 'danger')
                                              ?>">
                      <?= strtoupper($data['status_bimbingan']) ?>
                    </span>
                  </td>
                </tr>
                <tr>
                  <th>Dibuat Pada</th>
                  <td><?= $data['created_at'] ?></td>
                </tr>
                <tr>
                  <th>Terakhir Update</th>
                  <td><?= $data['updated_at'] ?? '-' ?></td>
                </tr>
              </table>
            </div>

          </div>
        </div>
      </section>
    </div>

    <?php include '../footer.php'; ?>
  </div>

  <?php include '../mhs_script.php'; ?>
</body>

</html>