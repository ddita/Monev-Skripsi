<?php
session_start();
$konstruktor = 'admin_akademik';
require_once '../database/config.php';

/* ================= CEK LOGIN ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login/logout.php");
  exit;
}

/* ================= DEKRIP ================= */
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
if (!isset($_GET['id_tahun'])) {
  die('ID Tahun Akademik tidak ditemukan');
}

$id_enkripsi = $_GET['id_tahun'];
$id_tahun = decriptData($id_enkripsi);

if (!$id_tahun) {
  die('Tahun Akademik tidak valid');
}

$id_tahun = mysqli_real_escape_string($conn, $id_tahun);

/* ================= AMBIL DATA TAHUN AKADEMIK ================= */
$qTahun = mysqli_query($conn, "SELECT id_tahun, tahun_akademik, status_aktif, keterangan FROM tbl_tahun_akademik WHERE id_tahun = '$id_tahun'");

$tahun = mysqli_fetch_assoc($qTahun);

if (!$tahun) {
  die('Data tahun akademik tidak ditemukan');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Monev Skripsi | Edit Tahun Akademik</title>
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
              <h1>Edit Tahun Akademik</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../admin_akademik">Tahun Akademik</a></li>
                <li class="breadcrumb-item active">Edit</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <a href="../admin_akademik" class="btn btn-warning btn-sm mb-3">
            <i class="nav-icon fas fa-chevron-left"></i> Kembali
          </a>
          <div class="row">

            <!-- FORM EDIT -->
            <div class="col-lg-6">
              <div class="card card-warning">
                <div class="card-header">
                  <h3 class="card-title"><i class="fas fa-edit"></i> Form Edit Tahun Akademik</h3>
                </div>

                <form method="post" action="proses.php">
                  <div class="card-body">

                    <input type="hidden" name="action" value="update_tahun">
                    <input type="hidden" name="id_tahun" value="<?= $tahun['id_tahun']; ?>">

                    <!-- TAHUN AKADEMIK -->
                    <div class="form-group">
                      <label>Tahun Akademik</label>
                      <input type="text"
                        name="tahun_akademik"
                        class="form-control"
                        value="<?= htmlspecialchars($tahun['tahun_akademik']); ?>"
                        readonly>
                    </div>

                    <!-- STATUS AKTIF -->
                    <div class="form-group">
                      <label>Status Aktif</label>
                      <select name="status_aktif" class="form-control" required>
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                      </select>
                    </div>

                    <!-- KETERANGAN -->
                    <div class="form-group">
                      <label>Keterangan</label>
                      <select name="keterangan" class="form-control" required>
                        <option value="Arsip" <?= $tahun['keterangan'] === 'Arsip' ? 'selected' : ''; ?>>
                          Arsip
                        </option>
                        <option value="Tahun Berjalan" <?= $tahun['keterangan'] === 'Tahun Berjalan' ? 'selected' : ''; ?>>
                          Tahun Berjalan
                        </option>
                      </select>
                    </div>

                  </div>

                  <div class="card-footer">
                    <button type="submit" class="btn btn-warning btn-block">
                      <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="../admin_akademik" class="btn btn-secondary btn-block">
                      Batal
                    </a>
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