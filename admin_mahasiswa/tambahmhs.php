<?php
session_start();
$konstruktor = 'admin_mahasiswa';
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

  $ket = "Pengguna $usr ($nama) mencoba akses Master Mahasiswa sebagai $role";

  mysqli_query(
    $conn,
    "INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES ('$usr','$waktu','$ket')"
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
                <h1 class="m-0">Tambah Mahasiswa</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Dashboard Mahasiswa</a></li>
                  <li class="breadcrumb-item active">Tambah Data Mahasiswa</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <!-- general form elements -->
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title"><i class="nav-icon fas fa-user-graduate"></i> Tambah Data Mahasiswa</h3>
                  </div>
                  <!-- /.card-header -->
                  <!-- form start -->
                  <form action="proses.php" method="post" class="form-mahasiswa">
                    <input type="hidden" name="action" value="tambah">
                    <div class="card-body">
                      <a href="../admin_mahasiswa" class="btn btn-warning btn-sm mb-3">
                        <i class="nav-icon fas fa-chevron-left"></i> Kembali
                      </a>
                      <div class="row">
                        <!-- ================= KOLOM KIRI : DATA MAHASISWA ================= -->
                        <div class="col-md-6">
                          <h5 class="text-muted">Data Mahasiswa</h5>
                          <hr>

                          <div class="form-group">
                            <label>NIM</label>
                            <input type="text" name="nim" class="form-control" required>
                          </div>

                          <div class="form-group">
                            <label>Nama Mahasiswa</label>
                            <input type="text" name="nama" class="form-control" required>
                          </div>

                          <div class="form-group">
                            <label>Program Studi</label>
                            <select name="prodi" class="form-control" required>
                              <option value="" selected disabled>--- Pilih Program Studi ---</option>
                              <?php
                              $q = mysqli_query($conn, "SELECT * FROM tbl_prodi");
                              while ($p = mysqli_fetch_assoc($q)) {
                                echo "<option value='{$p['kode_prodi']}'>{$p['nama_prodi']}</option>";
                              }
                              ?>
                            </select>
                          </div>

                          <div class="form-group">
                            <label>Angkatan</label>
                            <select name="angkatan" class="form-control" required>
                              <option value="" selected disabled>--- Pilih Angkatan ---</option>
                              <?php
                              $q = mysqli_query($conn, "SELECT * FROM tbl_angkatan");
                              while ($a = mysqli_fetch_assoc($q)) {
                                echo "<option value='{$a['kode_angkatan']}'>{$a['kode_angkatan']}</option>";
                              }
                              ?>
                            </select>
                          </div>

                          <div class="form-group">
                            <label>Dosen Pembimbing</label>
                            <select name="nip_dosen" class="form-control" required>
                              <option value="" selected disabled>--- Pilih Dosen Pembimbing ---</option>
                              <?php
                              $q = mysqli_query($conn, "SELECT nip,nama_dosen FROM tbl_dosen");
                              while ($d = mysqli_fetch_assoc($q)) {
                                echo "<option value='{$d['nip']}'>{$d['nama_dosen']}</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>

                        <!-- ================= KOLOM KANAN : DATA SKRIPSI ================= -->
                        <div class="col-md-6">
                          <h5 class="text-muted">Data Skripsi</h5>
                          <div class="form-group">
                            <label>Judul Skripsi</label>
                            <textarea name="judul" class="form-control" rows="4" required></textarea>
                          </div>

                          <div class="form-group">
                            <label>Status Skripsi</label>
                            <select name="status_skripsi" class="form-control" required>
                              <option value="" selected disabled>--- Pilih Status Skripsi ---</option>
                              <?php
                              $q = mysqli_query($conn, "SELECT * FROM tbl_status");
                              while ($s = mysqli_fetch_assoc($q)) {
                                echo "<option value='{$s['id']}'>{$s['status']}</option>";
                              }
                              ?>
                            </select>
                          </div>

                          <div class="form-group">
                            <label>Periode</label>
                            <select name="id_periode" class="form-control" required>
                              <option value="" selected disabled>--- Pilih Periode ---</option>
                              <?php
                              $q = mysqli_query($conn, "SELECT * FROM tbl_periode");
                              while ($p = mysqli_fetch_assoc($q)) {
                                echo "<option value='{$p['id_periode']}'>{$p['nama_periode']}</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>

                      </div> <!-- /.row -->
                    </div>

                    <!-- ================= TOMBOL SUBMIT (SATU) ================= -->
                    <div class="card-footer">
                      <button type="submit" class="btn btn-primary btn-block" name="tambah">
                        <i class="nav-icon fas fa-plus"></i> Tambah Data
                      </button>
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