<?php
session_start();
$konstruktor = 'admin_prodi';
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
if (!isset($_GET['id_prodi'])) {
  header("Location: ../admin_prodi");
  exit;
}

$id_prodi = decriptData($_GET['id_prodi']);

/* ================= Data Program Studi ================= */
$qProdi = mysqli_query($conn, "SELECT id_prodi, kode_prodi, nama_prodi, jenjang, status_aktif FROM tbl_prodi WHERE id_prodi='$id_prodi'");

$prodi = mysqli_fetch_assoc($qProdi);

if (!$prodi) {
  die("Data Program Studi tidak ditemukan");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monev Skripsi | Edit Program Studi</title>

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
              <h1>Edit Program Studi</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../admin_prodi">Program Studi</a></li>
                <li class="breadcrumb-item active">Edit</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <!-- CONTENT -->
      <section class="content">
        <div class="container-fluid">
          <a href="../admin_prodi" class="btn btn-warning btn-sm mb-3">
            <i class="nav-icon fas fa-chevron-left"></i> Kembali
          </a>
          <div class="row">
            <div class="col-lg-6">

              <div class="card card-warning">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-user-edit"></i> Edit Data Program Studi
                  </h3>
                </div>

                <form action="proses.php?action=edit" method="POST">
                  <div class="card-body">

                    <input type="hidden" name="id_prodi" value="<?= $prodi['id_prodi']; ?>">

                    <!-- KODE PRODI (READONLY) -->
                    <div class="form-group">
                      <label>Kode Program Studi</label>
                      <input type="text" class="form-control"
                        value="<?= htmlspecialchars($prodi['kode_prodi']); ?>" readonly>
                    </div>

                    <!-- NAMA PRODI (READONLY) -->
                    <div class="form-group">
                      <label>Nama Program Studi</label>
                      <input type="text" class="form-control"
                        value="<?= htmlspecialchars($prodi['nama_prodi']); ?>" readonly>
                    </div>

                    <!-- JENJANG -->
                    <div class="form-group">
                      <label>Jenjang</label>
                      <select name="jenjang" class="form-control" required>
                        <option value="D3" <?= $prodi['jenjang'] === 'D3' ? 'selected' : ''; ?>>D3</option>
                        <option value="S1" <?= $prodi['jenjang'] === 'S1' ? 'selected' : ''; ?>>S1</option>
                        <option value="S2" <?= $prodi['jenjang'] === 'S2' ? 'selected' : ''; ?>>S2</option>
                      </select>
                    </div>

                    <!-- STATUS AKTIF -->
                    <div class="form-group">
                      <label>Status Program Studi</label>
                      <select name="status_aktif" class="form-control" required>
                        <option value="Aktif" <?= $prodi['status_aktif'] === 'Aktif' ? 'selected' : ''; ?>>
                          Aktif
                        </option>
                        <option value="Nonaktif" <?= $prodi['status_aktif'] === 'Nonaktif' ? 'selected' : ''; ?>>
                          Nonaktif
                        </option>
                      </select>
                    </div>

                  </div>

                  <div class="card-footer">
                    <button type="submit" class="btn btn-warning btn-block">
                      <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="../admin_prodi" class="btn btn-secondary btn-block">Batal</a>
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