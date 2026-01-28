<?php
session_start();
$konstruktor = 'admin_angkatan';
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

  $ket = "Pengguna $usr ($nama) mencoba akses Manajemen Akademik sebagai $role";

  mysqli_query($conn, "INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES ('$usr','$waktu','$ket')");

  header("Location: ../login/logout.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monev Skripsi | Administrator</title>
  <?php include '../mhs_listlink.php'; ?>
  <script>
    (function() {
      const theme = localStorage.getItem("theme") || "dark";
      document.documentElement.classList.add(theme + "-mode");
    })();
  </script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <?php if (isset($_SESSION['flash'])) : ?>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        swal({
          title: "Informasi",
          text: "<?= $_SESSION['flash']['msg']; ?>",
          icon: "<?= $_SESSION['flash']['type']; ?>",
          button: "OK"
        });
      });
    </script>
  <?php
    unset($_SESSION['flash']);
  endif;
  ?>
  <div class="wrapper">
    <?php include '../mhs_navbar.php'; ?>
    <?php include '../admin_sidebar.php'; ?>
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="../images/UP.png" alt="Monev-Skripsi" height="60" width="60">
    </div>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Tambah Tahun Angkatan</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../admin_angkatan">Tahun Angkatan</a></li>
                <li class="breadcrumb-item active">Tambah Tahun Angkatan</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-6">
              <a href="../admin_angkatan" class="btn btn-warning btn-sm mb-3">
                <i class="fas fa-chevron-left"></i> Kembali
              </a>
              <!-- general form elements -->
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title"><i class="nav-icon fas fa-open-book"></i> Tambah Tahun Angkatan</h3>
                </div>
                <!-- /.card-header -->
                <!-- FORM TAMBAH ANGKATAN -->
                <form action="proses.php" method="post">
                  <input type="hidden" name="action" value="tambah">
                  <div class="card-body">

                    <!-- KODE ANGKATAN -->
                    <div class="form-group">
                      <label for="kode_angkatan">Kode angkatan</label>
                      <input type="text" class="form-control" id="kode_angkatan" name="kode_angkatan" required>
                    </div>

                    <!-- KETERANGAN -->
                    <div class="form-group">
                      <label for="keterangan">Keterangan</label>
                      <input type="text" class="form-control" id="keterangan" name="keterangan" required>
                    </div>

                  </div>
                  <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block" name="tambah">
                      <i class="nav-icon fas fa-plus"></i> Tambah Data
                    </button>
                  </div>
                </form>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>

    </div><!-- /.container-fluid -->
    </section>

  </div><!-- /.content-wrapper -->
  <?php include '../footer.php'; ?>
  </div><!-- ./wrapper -->
  <?php include '../mhs_script.php'; ?>

</body>

</html>