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
            <!-- Small boxes (Stat box) -->
            <div class="row">
              <div class="col-lg-6">
                <!-- general form elements -->
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title"><i class="nav-icon fas fa-user-graduate"></i> Tambah Data Mahasiswa</h3>
                  </div>
                  <!-- /.card-header -->
                  <!-- form start -->
                  <form action="prosestambah.php" method="post" class="form-mahasiswa">
                    <div class="card-body">
                      <a href="../admin_mahasiswa" class="btn btn-warning btn-sm"><i class="nav-icon fas fa-chevron-left"></i> Kembali</a>
                      <div class="form-group">
                        <label for="nim">NIM</label>
                        <input type="text" class="form-control" id="nim" name="nim" maxlength="50" onkeypress="return IsNumeric(event);" placeholder="Input NIM Mahasiswa" autofocus required>
                      </div>
                      <div class="form-group">
                        <label for="nama">Nama Mahasiswa</label>
                        <input type="text" class="form-control" id="nama" name="nama" maxlength="150" placeholder="Input Nama Mahasiswa" required>
                      </div>
                      <div class="form-group">
                        <label for="prodi">Program Studi</label>
                        <select class="form-control" name="prodi" required>
                          <option value="">-- Pilih Prodi --</option>
                          <?php
                          //panggil data pada data tabel prodi
                          $pglprodi = mysqli_query($conn, "SELECT * FROM tbl_prodi") or die(mysqli_error($conn));
                          //variabel untuk menampung return value dari query panggil prodi
                          $rvprodi = mysqli_num_rows($pglprodi);

                          //kondisi jika tabel prodi memiliki <= 1 data
                          if ($rvprodi > 0) {
                            //melakukan perulangan untuk menampilkan data
                            while ($dt_prodi = mysqli_fetch_array($pglprodi)) {
                              //tampilkan data pada option di select elemen
                          ?>
                              <option value="<?= $dt_prodi['kode_prodi']; ?>">
                                <?= $dt_prodi['kode_prodi']; ?> - <?= $dt_prodi['nama_prodi']; ?>
                              </option>
                          <?php
                            }
                          }
                          //kondisi jika tabel prodi kosong
                          else {
                          }
                          ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" name="status" required>
                          <option value="">-- Pilih Status --</option>
                          <?php
                          //panggil data pada data tabel status
                          $pglstatus = mysqli_query($conn, "SELECT * FROM tbl_status") or die(mysqli_error($conn));
                          //variabel untuk menampung return value dari query panggil prodi
                          $rvstatus = mysqli_num_rows($pglstatus);

                          //kondisi jika tabel prodi memiliki <= 1 data
                          if ($rvstatus > 0) {
                            //melakukan perulangan untuk menampilkan data
                            while ($dt_status = mysqli_fetch_array($pglstatus)) {
                              //tampilkan data pada option di select elemen
                          ?>
                              <option value="<?= $dt_status['id']; ?>">
                                <?= $dt_status['id']; ?> - <?= $dt_status['status']; ?>
                              </option>
                          <?php
                            }
                          }
                          //kondisi jika tabel prodi kosong
                          else {
                          }
                          ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="nip_dosen">Dosen Pembimbing</label>
                        <select name="nip_dosen" class="form-control" required>
                          <option value="">-- Pilih Dosen Pembimbing --</option>
                          <?php
                          $qdosen = mysqli_query($conn, "SELECT nip, nama_dosen FROM tbl_dosen");
                          while ($dnip = mysqli_fetch_assoc($qdosen)) {
                            echo '<option value="' . $dnip['nip'] . '">' . $dnip['nama_dosen'] . '</option>';
                          }
                          ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="angkatan">Angkatan</label>
                        <select name="angkatan" class="form-control" required>
                          <option value="">-- Pilih Angkatan --</option>
                          <?php
                          $qAngkatan = mysqli_query($conn, "SELECT kode_angkatan FROM tbl_angkatan ORDER BY kode_angkatan DESC");
                          while ($a = mysqli_fetch_assoc($qAngkatan)) {
                          ?>
                            <option value="<?= $a['kode_angkatan']; ?>">
                              <?= $a['kode_angkatan']; ?>
                            </option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                      <button type="submit" class="btn btn-primary btn-block" name="tambahmhs"><i class="nav-icon fas fa-plus"></i> Tambah Data</button>
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