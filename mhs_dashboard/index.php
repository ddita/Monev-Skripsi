<?php
session_start();
$konstruktor = 'mhs_dashboard';
require_once '../database/config.php';

if ($_SESSION['status'] != 2) {
  $usr = $_SESSION['username'];
  $waktu = date('Y-m-d H:i:s');
  $auth = $_SESSION['status'];
  $nama = $_SESSION['nama_user'];

  if ($auth == 0) {
    $tersangka = "Administrator";
  } elseif ($auth == 1) {
    $tersangka = "Dosen";
  }

  $ket = "Pengguna dengan username $usr, nama : $nama melakukan cross authority dengan akses sebagai $tersangka";
  mysqli_query($conn, "INSERT INTO tbl_cross_auth VALUES ('', '$usr', '$waktu', '$ket')");
  echo '<script>window.location="../login/logout.php"</script>';
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monev Skripsi | Mahasiswa</title>
  <?php include '../mhs_listlink.php'; ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="../images/UP.png" alt="Monev Skripsi" height="60" width="60">
  </div>

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <?php include '../mhs_navbar.php'; ?>
  </nav>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
      <img src="../images/graduate.png" class="brand-image img-circle elevation-3">
      <span class="brand-text font-weight-light">Monev Skripsi</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <?php include '../mhs_sidebar.php'; ?>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">

    <!-- Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard Mahasiswa</h1>
            <small>Selamat datang, <strong><?= $_SESSION['nama_user']; ?></strong></small>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard Mahasiswa</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <!-- Ringkasan -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3>Proposal</h3>
                <p>Status Skripsi</p>
              </div>
              <div class="icon"><i class="fas fa-book"></i></div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>5</h3>
                <p>Total Bimbingan</p>
              </div>
              <div class="icon"><i class="fas fa-comments"></i></div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>45%</h3>
                <p>Progress Skripsi</p>
              </div>
              <div class="icon"><i class="fas fa-chart-line"></i></div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>Belum</h3>
                <p>Pengajuan Sidang</p>
              </div>
              <div class="icon"><i class="fas fa-graduation-cap"></i></div>
            </div>
          </div>
        </div>

        <!-- Progress -->
        <div class="row">
          <div class="col-md-12">
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Progress Skripsi</h3>
              </div>
              <div class="card-body">
                <p>Proposal & Bab I–III</p>
                <div class="progress">
                  <div class="progress-bar bg-success" style="width:45%">45%</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Jadwal & Pengumuman -->
        <div class="row">
          <div class="col-md-6">
            <div class="card card-info">
              <div class="card-header">
                <h3 class="card-title">Jadwal Terdekat</h3>
              </div>
              <div class="card-body">
                <ul>
                  <li>Bimbingan: 20 Januari 2026</li>
                  <li>Deadline Revisi: 25 Januari 2026</li>
                  <li>Sidang: Belum Dibuka</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title">Pengumuman</h3>
              </div>
              <div class="card-body">
                <p>🔔 Pengajuan judul dibuka sampai 30 Januari 2026</p>
                <p>🔔 Minimal 8 kali bimbingan sebelum sidang</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Saran -->
        <div class="row">
          <div class="col-md-12">
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">Saran untuk Mahasiswa</h3>
              </div>
              <div class="card-body">
                <ul>
                  <li>Lakukan bimbingan secara rutin</li>
                  <li>Catat semua revisi dosen</li>
                  <li>Periksa format sesuai pedoman</li>
                  <li>Jangan menunda unggah laporan</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Action -->
        <div class="row">
          <div class="col-md-12 text-center">
            <a href="kelas_bimbingan.php" class="btn btn-primary m-1"><i class="fas fa-comments"></i> Kelas Bimbingan</a>
            <a href="upload_laporan.php" class="btn btn-success m-1"><i class="fas fa-upload"></i> Upload Laporan</a>
            <a href="ajukan_judul.php" class="btn btn-info m-1"><i class="fas fa-file"></i> Ajukan Judul</a>
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
