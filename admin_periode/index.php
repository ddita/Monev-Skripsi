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

  $ket = "Pengguna $usr ($nama) mencoba akses Manajemen Akademik sebagai $role";

  mysqli_query(
    $conn,
    "INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES ('$usr','$waktu','$ket')"
  );

  header("Location: ../login/logout.php");
  exit;
}

function encriptData($data)
{
  $key = 'monev_skripsi_2024'; // ganti sesuai kebutuhan
  return urlencode(base64_encode(openssl_encrypt(
    $data,
    'AES-128-ECB',
    $key
  )));
}

function decriptData($data)
{
  $key = 'monev_skripsi_2024';
  return openssl_decrypt(
    base64_decode(urldecode($data)),
    'AES-128-ECB',
    $key
  );
}
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
  <div class="wrapper">

    <!-- PRELOADER -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img src="../images/UP.png" height="60">
    </div>

    <?php include '../mhs_navbar.php'; ?>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="#" class="brand-link">
        <img src="../images/profile.png" class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-light">Monev Skripsi</span>
      </a>
      <div class="sidebar">
        <?php include '../admin_sidebar.php'; ?>
      </div>
    </aside>

    <div class="content-wrapper">

      <!-- HEADER -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">
                <i class="fas fa-calendar-alt text-primary"></i>
                Periode Akademik
              </h1>
              <small class="text-muted">
                Manajemen periode aktif dan tahun akademik
              </small>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                <li class="breadcrumb-item active">Periode</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- CONTENT -->
      <section class="content">
        <div class="container-fluid">
          <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-list"></i> Data Periode Akademik
              </h3>

              <div class="card-tools">
                <a href="tambah.php" class="btn btn-sm btn-primary">
                  <i class="fas fa-plus"></i> Tambah
                </a>
              </div>
            </div>

            <div class="card-body table-responsive">

              <table id="example1"
                class="table table-hover table-bordered table-striped">
                <thead class="bg-light">
                  <tr class="text-center">
                    <th width="5%">No</th>
                    <th>Periode</th>
                    <th>Tahun Akademik</th>
                    <th>Semester</th>
                    <th width="10%">Aksi</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $no = 1;
                  $qperiode = mysqli_query($conn,"SELECT p.id_periode,p.nama_periode,p.semester,p.status_aktif,p.created_at,t.tahun_akademik
                  FROM tbl_periode p JOIN tbl_tahun_akademik t ON t.id_tahun = p.id_tahun
                  ORDER BY p.created_at DESC"
                  );

                  if (mysqli_num_rows($qperiode) > 0):
                    while ($dt = mysqli_fetch_assoc($qperiode)):
                  ?>

                      <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td>
                          <strong><?= htmlspecialchars($dt['nama_periode']); ?></strong>
                        </td>
                        <td class="text-center">
                          <span class="badge badge-info">
                            <?= htmlspecialchars($dt['tahun_akademik']); ?>
                          </span>
                        </td>
                        <td class="text-center">
                          <span class="badge badge-info">
                            <?= htmlspecialchars($dt['semester']); ?>
                          </span>
                        </td>
                        <td class="text-center">
                          <a href="proses.php?action=hapus&kd_prd=<?= encriptData($dt['id_periode']); ?>"
                            class="btn btn-sm btn-outline-danger"
                            data-toggle="tooltip"
                            title="Hapus data"
                            onclick="return confirm('Hapus periode <?= $dt['nama_periode']; ?> ?')">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>

                    <?php endwhile;
                  else: ?>

                    <tr>
                      <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle"></i>
                        Belum ada data periode akademik
                      </td>
                    </tr>

                  <?php endif; ?>

                </tbody>
              </table>

            </div>
          </div>

        </div>
      </section>
    </div>

    <?php include '../footer.php'; ?>
  </div>

  <?php include '../mhs_script.php'; ?>

  <script>
    $(function() {
      $('[data-toggle="tooltip"]').tooltip();
    });
  </script>

</body>

</html>