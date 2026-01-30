<?php
session_start();
$konstruktor = 'admin_panduan';
require_once '../database/config.php';

/* ================= CEK LOGIN ================= */
if (!isset($_SESSION['role'])) {
  header("Location: ../login/logout.php");
  exit;
}

if ($_SESSION['role'] !== 'admin') {
  header("Location: ../login/logout.php");
  exit;
}

/* ================= HELPER ================= */
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
if (!isset($_GET['id_panduan'])) {
  header("Location: ../admin_panduan");
  exit;
}

$id_panduan = decriptData($_GET['id_panduan']);
if (!$id_panduan) {
  die("ID Panduan tidak valid");
}

$sql = "SELECT * FROM tbl_panduan_skripsi WHERE id_panduan='$id_panduan'";
$query = mysqli_query($conn, $sql);

if (!$query) {
  die("Query Error: " . mysqli_error($conn));
}

$data = mysqli_fetch_assoc($query);

if (!$data) {
  die("Data panduan tidak ditemukan");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monev Skripsi | Edit Panduan</title>

  <?php include '../mhs_listlink.php'; ?>

  <script>
    (function() {
      const theme = localStorage.getItem("theme") || "dark";
      document.documentElement.classList.add(theme + "-mode");
    })();
  </script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">

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
              <h1>Edit Panduan</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../admin_panduan">Panduan</a></li>
                <li class="breadcrumb-item active">Edit Panduan</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <!-- CONTENT -->
      <section class="content">
        <div class="container-fluid">
          <a href="../admin_panduan" class="btn btn-warning btn-sm mb-3">
            <i class="nav-icon fas fa-chevron-left"></i> Kembali
          </a>
          <div class="row">
            <div class="col-lg-12">
              <div class="card card-warning">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-edit"></i> Edit Data Panduan
                  </h3>
                </div>

                <form action="proses.php" method="POST" enctype="multipart/form-data">
                  <input type="hidden" name="id_panduan" value="<?= $data['id_panduan']; ?>">
                  <input type="hidden" name="file_lama" value="<?= $data['file']; ?>">

                  <div class="card-body">

                    <div class="form-group">
                      <label>Judul Panduan</label>
                      <input type="text" name="judul" value="<?= htmlspecialchars($data['judul']); ?>" class="form-control" required>
                    </div>

                    <div class="form-group">
                      <label>Tahun Akademik</label>
                      <input type="text" name="tahun_akademik" value="<?= $data['tahun_akademik']; ?>" class="form-control" required>
                    </div>

                    <div class="form-group">
                      <label>File Panduan</label>
                      <input type="file" name="file" class="form-control">
                      <small class="text-muted">Biarkan kosong jika tidak diganti</small>
                    </div>

                  </div>

                  <div class="card-footer text-right">
                    <button type="submit" name="action" value="edit" class="btn btn-warning">
                      <i class="fas fa-save"></i> Update
                    </button>
                    <a href="../admin_panduan" class="btn btn-secondary">Kembali</a>
                  </div>
                </form>
              </div>

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