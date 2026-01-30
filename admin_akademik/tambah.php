<?php
session_start();
$konstruktor = 'admin_akademik';
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

  $ket = "Pengguna $usr ($nama) mencoba akses Akademik Manajemen sebagai $role";

  mysqli_query($conn, "INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES ('$usr','$waktu','$ket')");

  header("Location: ../login/logout.php");
  exit;
} else {
?>

  <!DOCTYPE html>
  <html lang="id">

  <head>
    <meta charset="utf-8">
    <title>Monev Skripsi | Tambah Tahun Akademik</title>
    <?php include '../mhs_listlink.php'; ?>
    <script>
      (function() {
        const theme = localStorage.getItem("theme") || "dark";
        document.documentElement.classList.add(theme + "-mode");
      })();
    </script>
  </head>

  <body class="hold-transition sidebar-mini">
    <div class="wrapper">

      <?php include '../mhs_navbar.php'; ?>
      <?php include '../admin_sidebar.php'; ?>
      <!-- Preloader -->
      <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="../images/UP.png" alt="Monev-Skripsi" height="60" width="60">
      </div>

      <div class="content-wrapper">
        <section class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1>Tambah Tahun Akademik</h1>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                  <li class="breadcrumb-item"><a href="../admin_akademik">Tahun Akademik</a></li>
                  <li class="breadcrumb-item active">Tambah</li>
                </ol>
              </div>
            </div>
          </div>
        </section>

        <section class="content col-md-6">
          <div class="container-fluid">
            <a href="../admin_akademik" class="btn btn-warning btn-sm mb-3">
              <i class="nav-icon fas fa-chevron-left"></i> Kembali
            </a>
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-tie"></i> Form Tahun akademik</h3>
              </div>

              <form method="POST" action="proses.php">
                <div class="card-body">

                  <div class="form-group">
                    <label>Tahun akademik</label>
                    <input type="text" name="tahun_akademik" class="form-control" required autocomplete="off">
                  </div>

                </div>

                <div class="card-footer">
                  <button type="submit" name="action" value="tambah" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                  </button>
                  <a href="../admin_akademik" class="btn btn-secondary">Batal</a>
                </div>
              </form>

            </div>

          </div>
        </section>
      </div>

      <?php include '../footer.php'; ?>
    </div>

  <?php include '../mhs_script.php';
}
  ?>
  </body>

  </html>