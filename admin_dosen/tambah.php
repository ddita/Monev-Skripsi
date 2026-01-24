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
  <html lang="id">

  <head>
    <meta charset="utf-8">
    <title>Tambah Dosen</title>
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
            <h1>Tambah Dosen</h1>
          </div>
        </section>

        <section class="content">
          <div class="container-fluid">

            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title"><i class="fas fa-user-tie"></i> Form Dosen</h3>
              </div>

              <form method="POST" action="proses.php">
                <div class="card-body">

                  <div class="form-group">
                    <label>NIP</label>
                    <input type="text" name="nip" class="form-control" required autocomplete="off">
                  </div>

                  <div class="form-group">
                    <label>Nama Dosen</label>
                    <input type="text" name="nama_dosen" class="form-control" required>
                  </div>

                </div>

                <div class="card-footer">
                  <button type="submit" name="action" value="tambah_dosen" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                  </button>
                  <a href="../admin_dosen" class="btn btn-secondary">Batal</a>
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