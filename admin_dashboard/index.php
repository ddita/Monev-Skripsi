<?php
session_start();
$konstruktor = 'admin_dashboard';
require_once '../database/config.php';

// CEK LOGIN
if (!isset($_SESSION['role'])) {
  header("Location: ../login/logout.php");
  exit;
}

// CEK ROLE (HARUS ADMIN)
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
}
?>

<?php
// TOTAL MAHASISWA
$qMhs = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_mahasiswa");
$total_mhs = mysqli_fetch_assoc($qMhs)['total'];

// TOTAL DOSEN
$qDsn = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_dosen");
$total_dsn = mysqli_fetch_assoc($qDsn)['total'];

// MAHASISWA AKTIF SKRIPSI
$qAktif = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_skripsi WHERE status_skripsi = 'aktif'");
$aktif = mysqli_fetch_assoc($qAktif)['total'];

// SIAP SIDANG
$qSidang = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_skripsi WHERE status_skripsi = 'siap_sidang'");
$siap_sidang = mysqli_fetch_assoc($qSidang)['total'];
?>

<?php
function statusBadge($status)
{
  switch ($status) {
    case 'siap_sidang':
      return '<span class="badge bg-success">
      <i class="fas fa-check-circle"></i> Siap Sidang
      </span>';

    case 'seminar':
      return '<span class="badge bg-primary">
      <i class="fas fa-chalkboard-teacher"></i> Seminar
      </span>';

    case 'bimbingan':
      return '<span class="badge bg-warning">
      <i class="fas fa-user-edit"></i> Bimbingan
      </span>';

    case 'revisi':
      return '<span class="badge bg-warning text-dark">
      <i class="fas fa-sync-alt"></i> Revisi
      </span>';

    default:
      return '<span class="badge bg-secondary">
      <i class="fas fa-file-alt"></i> Draft
      </span>';
  }
}
?>

<style>
  /* Hilangkan gap navbar ke konten */
  body.layout-navbar-fixed .content-wrapper {
    margin-top: 0 !important;
    padding-top: 0 !important;
  }

  /* Rapikan header dashboard */
  .content-header {
    padding-top: 6px !important;
    padding-bottom: 0 !important;
    margin-bottom: 0 !important;
  }
</style>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monev Skripsi | Administrator</title>
  <?php include '../mhs_listlink.php'; ?>
</head>

<script>
  document.addEventListener("DOMContentLoaded", function() {
    var tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  });
</script>


<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php include '../mhs_navbar.php'; ?>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="#" class="brand-link">
        <img src="../images/profile.png" class="brand-image img-circle elevation-3">
        <span class="brand-text font-weight-light">Monev Skripsi</span>
      </a>
      <div class="sidebar">
        <nav class="mt-2">
          <?php include '../admin_sidebar.php'; ?>
        </nav>
      </div>
    </aside>
    <div class="content-wrapper">
      <!-- HEADER -->
      <section class="content-header pb-0">
        <div class="container-fluid">
          <div class="row mb-1">
            <div class="col-sm-12">
              <h1 class="m-0">Dashboard Administrator</h1>
              <small class="text-muted">
                Selamat datang, <b><?= htmlspecialchars($_SESSION['nama_user']); ?></b>
              </small>
            </div>
          </div>
        </div>
      </section>

      <!-- CONTENT -->
      <section class="content">
        <div class="container-fluid">

          <!-- INFO BOX -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?= $total_mhs; ?></h3>
                  <p>Total Mahasiswa</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
              </div>
            </div>

            <div class="col-lg-3 col-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?= $total_dsn; ?></h3>
                  <p>Total Dosen</p>
                </div>
                <div class="icon"><i class="fas fa-user-tie"></i></div>
              </div>
            </div>

            <div class="col-lg-3 col-6">
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><?= $aktif; ?></h3>
                  <p>Mahasiswa Aktif Skripsi</p>
                </div>
                <div class="icon"><i class="fas fa-book"></i></div>
              </div>
            </div>

            <div class="col-lg-3 col-6">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><?= $siap_sidang; ?></h3>
                  <p>Siap Sidang</p>
                </div>
                <div class="icon"><i class="fas fa-graduation-cap"></i></div>
              </div>
            </div>
          </div>

          <?php
          // AKTIVITAS SKRIPSI TERAKHIR
          $qAktivitas = mysqli_query($conn, "SELECT id_skripsi, username, judul, status_skripsi, updated_at, DATEDIFF(NOW(), updated_at) AS selisih_hari
          FROM tbl_skripsi ORDER BY updated_at DESC LIMIT 5");
          ?>

          <!-- AKTIVITAS -->
          <div class="card">
            <div class="card-header bg-primary">
              <h3 class="card-title">Aktivitas Skripsi Terakhir</h3>
            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Username</th>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Update</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (mysqli_num_rows($qAktivitas) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($qAktivitas)): ?>

                      <?php
                      // highlight jika >14 hari tidak update
                      $rowClass = ($row['selisih_hari'] > 14) ? 'table-danger' : '';
                      ?>

                      <tr class="<?= $rowClass ?>">
                        <!-- USERNAME -->
                        <td>
                          <a href="detail_skripsi.php?id=<?= $row['id_skripsi']; ?>"
                            class="fw-bold text-decoration-none"
                            data-bs-toggle="tooltip"
                            title="Lihat detail skripsi">
                            <i class="fas fa-user"></i>
                            <?= htmlspecialchars($row['username']); ?>
                          </a>
                        </td>

                        <!-- JUDUL -->
                        <td>
                          <i class="fas fa-book text-secondary"></i>
                          <?= strlen($row['judul']) > 40
                            ? substr(htmlspecialchars($row['judul']), 0, 40) . '...'
                            : htmlspecialchars($row['judul']); ?>
                        </td>

                        <!-- STATUS -->
                        <td>
                          <?= statusBadge($row['status_skripsi']); ?>
                        </td>

                        <!-- UPDATE -->
                        <td>
                          <i class="far fa-clock"></i>
                          <?= date('d M Y H:i', strtotime($row['updated_at'])); ?>

                          <?php if ($row['selisih_hari'] > 14): ?>
                            <div class="text-danger small mt-1">
                              <i class="fas fa-exclamation-triangle"></i>
                              Tidak update <?= $row['selisih_hari']; ?> hari
                            </div>
                          <?php endif; ?>
                        </td>
                      </tr>

                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" class="text-center text-muted">
                        Belum ada aktivitas skripsi
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- REKOMENDASI -->
          <div class="card card-success">
            <div class="card-header">
              <h3 class="card-title">Rekomendasi Sistem</h3>
            </div>
            <div class="card-body">
              <ul>
                <li>Pastikan mahasiswa siap sidang segera diproses.</li>
                <li>Periksa mahasiswa tanpa progres > 14 hari.</li>
                <li>Evaluasi beban dosen pembimbing.</li>
                <li>Backup database secara berkala.</li>
              </ul>
            </div>
          </div>

        </div>
      </section>
    </div>

    <?php include '../footer.php'; ?>
    <?php include '../mhs_script.php'; ?>

  </div>
</body>

</html>