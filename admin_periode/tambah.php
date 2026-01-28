<?php
session_start();
$konstruktor = 'admin_periode';
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

  $ket = "Pengguna $usr ($nama) mencoba akses Manajemen Periode sebagai $role";

  mysqli_query($conn, "INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES ('$usr','$waktu','$ket')");

  header("Location: ../login/logout.php");
  exit;
}

/* ================= AMBIL TAHUN AKADEMIK AKTIF ================= */
$qTahun = mysqli_query($conn, "SELECT id_tahun, tahun_akademik FROM tbl_tahun_akademik WHERE status_aktif = 'Aktif' LIMIT 1");
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
              <h1 class="m-0">Tambah Periode Akademik</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../admin_periode">Periode</a></li>
                <li class="breadcrumb-item active">Tambah Periode</li>
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
              <a href="../admin_periode" class="btn btn-warning btn-sm mb-3">
                <i class="fas fa-chevron-left"></i> Kembali
              </a>
              <!-- general form elements -->
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title"><i class="nav-icon fas fa-calendar"></i> Tambah Periode Akademik</h3>
                </div>
                <!-- /.card-header -->
                <!-- FORM TAMBAH PERIODE -->
                <form action="proses.php" method="post">
                  <div class="card-body">

                    <!-- NAMA PERIODE -->
                    <div class="form-group">
                      <label for="periode">Nama Periode</label>
                      <input type="text" class="form-control" id="nama_periode" name="nama_periode" maxlength="30" placeholder="Contoh: Periode Genap 2025" required>
                    </div>

                    <!-- TAHUN AKADEMIK -->
                    <div class="form-group">
                      <label for="id_tahun">Tahun Akademik</label>
                      <input type="hidden" name="action" value="tambah">
                      <select class="form-control" id="id_tahun" name="id_tahun" required>
                        <option value="">-- Pilih Tahun Akademik --</option>
                        <?php if (mysqli_num_rows($qTahun) > 0): ?>
                          <?php while ($t = mysqli_fetch_assoc($qTahun)): ?>
                            <option value="<?= $t['id_tahun']; ?>">
                              <?= htmlspecialchars($t['tahun_akademik']); ?>
                            </option>
                          <?php endwhile; ?>
                        <?php else: ?>
                          <option value="" disabled> Tidak ada tahun akademik aktif </option>
                        <?php endif; ?>
                      </select>
                      <small class="text-muted"> Tahun akademik diambil otomatis dari data aktif </small>
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