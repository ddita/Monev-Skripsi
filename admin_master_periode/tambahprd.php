<?php
session_start();
$konstruktor = 'admin_master_periode';
require_once '../database/config.php';
if ($_SESSION['role'] !== 'admin') {

  $usr   = $_SESSION['username'] ?? '-';
  $nama  = $_SESSION['nama_user'] ?? '-';
  $role  = $_SESSION['role'] ?? '-';
  $waktu = date('Y-m-d H:i:s');

  // ROLE TERDETEKSI
  if ($role == 'dosen') {
    $tersangka = "Dosen";
  } elseif ($role == 'mahasiswa') {
    $tersangka = "Mahasiswa";
  } else {
    $tersangka = "Tidak diketahui";
  }

  $ket = "Pengguna dengan username $usr, nama: $nama melakukan cross authority dengan akses sebagai $tersangka";

  mysqli_query(
    $conn,
    "INSERT INTO tbl_cross_auth (username, waktu, keterangan)
     VALUES ('$usr', '$waktu', '$ket')"
  );

  header("Location: ../login/logout.php");
  exit;
} else {
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monev Skripsi | Administrator</title>
    <?php
    include '../mhs_listlink.php';
    ?>
  </head>

  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

      <!-- Preloader -->
      <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="../images/UP.png" alt="Monev-Skripsi" height="60" width="60">
      </div>

      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <?php
        include '../mhs_navbar.php';
        ?>
      </nav>
      <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="index3.html" class="brand-link">
          <img src="../images/profile.png" alt="Monev-Skripsi" class="brand-image img-circle elevation-3" style="opacity: .8">
          <span class="brand-text font-weight-light">Monev Skripsi</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <?php
            include '../admin_sidebar.php';
            ?>
          </nav>
          <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0">Tambah Periode Akademik</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Periode Akademik</a></li>
                  <li class="breadcrumb-item active">Tambah Periode Akademik</li>
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
                <!-- general form elements -->
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title"><i class="nav-icon fas fa-calendar"></i> Tambah Periode Akademik</h3>
                  </div>
                  <!-- /.card-header -->
                  <!-- FORM TAMBAH PERIODE -->
                  <form action="prosestambah.php" method="post">
                    <div class="card-body">

                      <a href="../admin_master_periode" class="btn btn-warning btn-sm mb-3">
                        <i class="fas fa-chevron-left"></i> Kembali
                      </a>

                      <!-- ID PERIODE -->
                      <div class="form-group">
                        <label for="id_periode">ID Periode</label>
                        <input type="text"
                          class="form-control"
                          id="id_periode"
                          name="id_periode"
                          maxlength="10"
                          placeholder="Contoh: 20251"
                          required>
                        <small class="text-muted">
                          Contoh: 20251 (Tahun 2025 Semester Ganjil)
                        </small>
                      </div>

                      <!-- NAMA PERIODE -->
                      <div class="form-group">
                        <label for="nama_periode">Nama Periode</label>
                        <input type="text"
                          class="form-control"
                          id="nama_periode"
                          name="nama_periode"
                          maxlength="30"
                          placeholder="Contoh: Periode Genap 2025"
                          required>
                      </div>

                      <!-- TAHUN AKADEMIK -->
                      <div class="form-group">
                        <label for="tahun_akademik">Tahun Akademik</label>
                        <input type="text"
                          class="form-control"
                          id="tahun_akademik"
                          name="tahun_akademik"
                          maxlength="9"
                          placeholder="Contoh: 2025/2026"
                          required>
                      </div>

                      <!-- SEMESTER -->
                      <div class="form-group">
                        <label for="semester">Semester</label>
                        <select class="form-control" id="semester" name="semester" required>
                          <option value="">-- Pilih Semester --</option>
                          <option value="Ganjil">Ganjil</option>
                          <option value="Genap">Genap</option>
                        </select>
                      </div>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary btn-block" name="tambahprd"><i class="nav-icon fas fa-plus"></i> Tambah Periode Akademik</button>
                </div>
                </form>
              </div>
              <!-- /.card -->
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