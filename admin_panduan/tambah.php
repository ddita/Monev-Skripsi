<?php
session_start();
$konstruktor = 'admin_panduan';
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
} else {
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monev Skripsi | Tambah Panduan</title>
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
                <h1 class="m-0">Tambah Panduan</h1>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                  <li class="breadcrumb-item"><a href="../admin_panduan">Panduan</a></li>
                  <li class="breadcrumb-item active">Tambah Panduan</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <a href="../admin_panduan" class="btn btn-warning btn-sm mb-3">
              <i class="nav-icon fas fa-chevron-left"></i> Kembali
            </a>
            <div class="row">
              <div class="col-lg-12">
                <!-- general form elements -->
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title"><i class="nav-icon fas fa-book"></i> Tambah Panduan</h3>
                  </div>
                  <!-- /.card-header -->

                  <form action="proses.php" method="POST" enctype="multipart/form-data">
                    <div class="card-body">

                      <div class="form-group">
                        <label>Judul Panduan</label>
                        <input type="text" name="judul" class="form-control" required>
                      </div>

                      <div class="form-group">
                        <label>Tahun Akademik</label>
                        <input type="text" name="tahun_akademik" class="form-control" placeholder="2024/2025" required>
                      </div>

                      <div class="form-group">
                        <label>File Panduan (PDF / DOCX)</label>
                        <input type="file" name="file" class="form-control" required>
                      </div>

                    </div>

                    <div class="card-footer text-right">
                      <button type="submit" name="action" value="tambah" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                      </button>
                      <a href="../admin_panduan" class="btn btn-secondary">Kembali</a>
                    </div>
                  </form>
                </div>

              </div>
            </div>
            <!-- /.row (main row) -->
          </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
      </div>
      <!-- /.content-wrapper -->
      <?php
      include '../footer.php';
      ?>

      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
      </aside>
      <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
  <?php
  include '../mhs_script.php';
}
  ?>

  </body>

  </html>