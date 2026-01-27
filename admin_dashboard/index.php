<?php
session_start();
$konstruktor = 'admin_dashboard';
require_once '../database/config.php';

/* ================= CEK LOGIN ================= */
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
    "INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES ('$usr', '$waktu', '$ket')"
  );

  header("Location: ../login/logout.php");
  exit;
}
?>

<?php
// TOTAL MAHASISWA
$qTotalMhs = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_mahasiswa");
$total_mhs = mysqli_fetch_assoc($qTotalMhs)['total'];

// MAHASISWA AKTIF
$qMhsAktif = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_mahasiswa WHERE aktif = 1");
$mhs_aktif = mysqli_fetch_assoc($qMhsAktif)['total'];

// MAHASISWA NONAKTIF
$qMhsNonaktif = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_mahasiswa WHERE aktif = 0");
$mhs_nonaktif = mysqli_fetch_assoc($qMhsNonaktif)['total'];

// TOTAL DOSEN
$qDsn = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_dosen");
$total_dsn = mysqli_fetch_assoc($qDsn)['total'];

// DOSEN AKTIF
$qDsnAktif = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_dosen WHERE aktif = 1");
$dsn_aktif = mysqli_fetch_assoc($qDsnAktif)['total'];

// MAHASISWA NONAKTIF
$qDsnNonaktif = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_dosen WHERE aktif = 0");
$dsn_nonaktif = mysqli_fetch_assoc($qDsnNonaktif)['total'];

// MAHASISWA AKTIF SKRIPSI
$qAktif = mysqli_query($conn, "SELECT COUNT(*) AS total FROM tbl_skripsi s
  JOIN tbl_status st ON st.id = s.id_status WHERE LOWER(st.status) = 'bimbingan'
");
$aktif = mysqli_fetch_assoc($qAktif)['total'];

// SIAP SIDANG
$qSidang = mysqli_query($conn, "SELECT COUNT(*) total FROM tbl_skripsi s
  JOIN tbl_status st ON st.id = s.id_status WHERE LOWER(st.status) = 'siap sidang'");
$siap_sidang = mysqli_fetch_assoc($qSidang)['total'];
?>

<?php
function statusBadge($status)
{
  $status = strtolower(trim($status));

  switch ($status) {
    case 'siap sidang':
      return '<span class="badge bg-success">
        <i class="fas fa-check-circle"></i> Siap Sidang
      </span>';

    case 'seminar proposal':
      return '<span class="badge bg-primary">
        <i class="fas fa-chalkboard-teacher"></i> Seminar Proposal
      </span>';

    case 'bimbingan':
      return '<span class="badge bg-warning">
        <i class="fas fa-user-edit"></i> Bimbingan
      </span>';

    case 'revisi':
      return '<span class="badge bg-warning text-dark">
        <i class="fas fa-sync-alt"></i> Revisi
      </span>';

    case 'lulus':
      return '<span class="badge bg-dark">
        <i class="fas fa-graduation-cap"></i> Lulus
      </span>';

    default:
      return '<span class="badge bg-secondary">
        <i class="fas fa-file-alt"></i> Draft
      </span>';
  }
}

function encryptData($data)
{
  $key = 'monev_skripsi_2024'; // HARUS sama dengan detailmhs.php

  return urlencode(
    base64_encode(
      openssl_encrypt($data, 'AES-128-ECB', $key)
    )
  );
}

?>

<?php
/* ================= PROGRES CHART ================= */
$qProgres = mysqli_query(
  $conn,
  "SELECT st.status, COUNT(*) AS total FROM tbl_skripsi s
   JOIN tbl_status st ON st.id = s.id_status
   JOIN tbl_mahasiswa m ON m.nim = s.username
   WHERE m.aktif = 1
   GROUP BY st.status"
);

$labelProgres = [];
$dataProgres  = [];

while ($p = mysqli_fetch_assoc($qProgres)) {
  $labelProgres[] = $p['status'];
  $dataProgres[]  = $p['total'];
}

/* ================= KETERLAMBATAN ================= */
$total_terlambat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM tbl_skripsi WHERE DATEDIFF(NOW(), updated_at) > 14"))['total'];

/* ================= RATA-RATA PENYELESAIAN ================= */
$qRata = mysqli_query($conn, "SELECT AVG(DATEDIFF(s.updated_at, s.created_at)) AS rata_hari FROM tbl_skripsi s
  JOIN tbl_status st ON s.id_status = st.id WHERE st.status = 'Lulus'");

$dataRata  = mysqli_fetch_assoc($qRata);
$rata_hari = round($dataRata['rata_hari'] ?? 0);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monev Skripsi | Admin</title>
  <?php include '../mhs_listlink.php'; ?>
  <script>
    (function() {
      const theme = localStorage.getItem("theme") || "dark";
      document.documentElement.classList.add(theme + "-mode");
    })();
  </script>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed">

  <div class="wrapper">
    <?php include '../mhs_navbar.php'; ?>
    <?php include '../admin_sidebar.php'; ?>
    <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="../images/UP.png" alt="Monev-Skripsi" height="60" width="60">
    </div>

    <div class="content-wrapper">
      <!-- HEADER -->
      <section class="content-header pb-0">
        <div class="container-fluid">
          <div class="row mb-1">
            <div class="col-sm-12">
              <h1 class="m-0">
                <i class="fas fa-home text-primary"></i>
                Dashboard
              </h1>
              <small class="text-muted">
                Selamat datang, <b><?= htmlspecialchars($_SESSION['nama_user']); ?></b>
              </small>
            </div>
          </div>
        </div>
      </section>

      <?php if ($total_terlambat > 0): ?>
        <div class="alert alert-danger alert-dismissible fade show">
          <i class="fas fa-bell"></i>
          <b>Perhatian!</b> Ada <?= $total_terlambat; ?> mahasiswa tidak update progres > 14 hari.
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
      <?php endif; ?>

      <!-- CONTENT -->
      <section class="content">
        <div class="container-fluid">
          <!-- ================= CHART + METRIK ================= -->
          <div class="row align-items-stretch">

            <!-- CHART -->
            <div class="col-md-8 d-flex">
              <div class="card flex-fill">
                <div class="card card-outline card-primary shadow-sm">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                      <i class="fas fa-chart-bar"></i> Progres Skripsi
                    </h3>
                  </div>
                  <div class="card-body">
                    <div style="height:200px;">
                      <canvas id="grafikProgres"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- SIDEBAR METRIK -->
            <div class="col-md-4 d-flex">
              <div class="w-100 d-flex flex-column gap-2">

                <div class="info-box bg-danger">
                  <span class="info-box-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                  </span>
                  <div class="info-box-content">
                    <span class="info-box-text">Mahasiswa Terlambat</span>
                    <span class="info-box-number"><?= $total_terlambat; ?></span>
                    <small>> 14 hari</small>
                  </div>
                </div>

                <div class="info-box bg-info">
                  <span class="info-box-icon">
                    <i class="fas fa-stopwatch"></i>
                  </span>
                  <div class="info-box-content">
                    <span class="info-box-text">Rata-rata Penyelesaian</span>
                    <span class="info-box-number"><?= $rata_hari; ?> Hari</span>
                    <small>Skripsi Lulus</small>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- ================= INFO BOX ================= -->
          <div class="row info-box-row">

            <!-- TOTAL MAHASISWA -->
            <div class="col-lg-3 col-md-6 mb-3 d-flex">
              <a href="../admin_mahasiswa" class="text-white w-100">
                <div class="small-box bg-gradient-blue h-100">
                  <div class="inner">
                    <h3><?= $total_mhs; ?></h3>
                    <p>Total Mahasiswa</p>
                    <div class="d-flex justify-content-between mt-2 small">
                      <span>Aktif: <?= $mhs_aktif; ?></span>
                      <span>Nonaktif: <?= $mhs_nonaktif; ?></span>
                    </div>
                  </div>
                  <div class="icon">
                    <i class="fas fa-users"></i>
                  </div>
                </div>
              </a>
            </div>

            <!-- TOTAL DOSEN -->
            <div class="col-lg-3 col-md-6 mb-3 d-flex">
              <a href="../admin_dosen" class="text-white w-100">
                <div class="small-box bg-gradient-navy h-100">
                  <div class="inner">
                    <h3><?= $total_dsn; ?></h3>
                    <p>Total Dosen</p>
                    <div class="d-flex justify-content-between mt-2 small">
                      <span>Aktif: <?= $dsn_aktif; ?></span>
                      <span>Nonaktif: <?= $dsn_nonaktif; ?></span>
                    </div>
                  </div>
                  <div class="icon">
                    <i class="fas fa-user-tie"></i>
                  </div>
                </div>
              </a>
            </div>

            <!-- AKTIF SKRIPSI -->
            <div class="col-lg-3 col-md-6 mb-3 d-flex">
              <a href="../admin_mahasiswa?progres=bimbingan" class="text-white w-100">
                <div class="small-box bg-gradient-orange h-100">
                  <div class="inner">
                    <h3><?= $aktif; ?></h3>
                    <p>Aktif Skripsi</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-book"></i>
                  </div>
                </div>
              </a>
            </div>

            <!-- SIAP SIDANG -->
            <div class="col-lg-3 col-md-6 mb-3 d-flex">
              <a href="../admin_mahasiswa?progres=siap_sidang" class="text-white w-100">
                <div class="small-box bg-gradient-blue h-100">
                  <div class="inner">
                    <h3><?= $siap_sidang; ?></h3>
                    <p>Siap Sidang</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-graduation-cap"></i>
                  </div>
                </div>
              </a>
            </div>

          </div>

          <?php
          // AKTIVITAS SKRIPSI TERAKHIR
          $qAktivitas = mysqli_query($conn, "SELECT s.id_skripsi, s.username, m.nama AS nama_mahasiswa, d.nip AS nip_dosen, d.nama_dosen, st.status AS status_skripsi, s.updated_at, DATEDIFF(NOW(), s.updated_at) AS selisih_hari
            FROM tbl_skripsi s
            JOIN tbl_mahasiswa m ON m.nim = s.username
            LEFT JOIN tbl_dosen d ON d.nip = m.dosen_pembimbing
            JOIN tbl_status st ON st.id = s.id_status
            ORDER BY s.updated_at DESC
            LIMIT 10
          ");
          ?>

          <!-- AKTIVITAS -->
          <div class="card">
            <div class="card card-outline card-primary shadow-sm">
              <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                  <i class="fas fa-history mr-1"></i> Aktivitas Terakhir
                </h3>
              </div>

              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th class="text-center">Mahasiswa</th>
                      <th class="text-center">Dosen Pembimbing</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Update</th>
                    </tr>
                  </thead>

                  <?php if (mysqli_num_rows($qAktivitas) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($qAktivitas)): ?>

                      <?php
                      // highlight jika >14 hari tidak update
                      $rowClass = ($row['selisih_hari'] > 14) ? 'table-danger' : '';
                      ?>

                      <tr class="<?= $rowClass ?>">
                        <!-- USERNAME -->
                        <td>
                          <a href="../admin_mahasiswa/detailmhs.php?nim=<?= encryptData($row['username']); ?>"
                            class="fw-bold text-decoration-none"
                            data-bs-toggle="tooltip"
                            title="Lihat detail skripsi">
                            <i class="fas fa-user"></i>
                            <?= htmlspecialchars($row['nama_mahasiswa']); ?>
                          </a>
                        </td>

                        <!-- JUDUL -->
                        <td>
                          <?php if (!empty($row['nip_dosen'])): ?>
                            <a href="../admin_dosen/detaildosen.php?nip=<?= encryptData($row['nip_dosen']); ?>"
                              class="text-decoration-none fw-bold"
                              data-bs-toggle="tooltip"
                              title="Lihat detail dosen">
                              <i class="fas fa-user-tie text-primary"></i>
                              <?= htmlspecialchars($row['nama_dosen']); ?>
                            </a>
                          <?php else: ?>
                            <span class="text-muted fst-italic">
                              <i class="fas fa-user-slash"></i> Belum ditentukan
                            </span>
                          <?php endif; ?>
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
          </div>

          <!-- REKOMENDASI -->
          <div class="card card-navy">
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

  </div> <!-- /.content-wrapper -->

  <?php include '../footer.php'; ?>
  </div> <!-- /.wrapper -->

  <?php include '../mhs_script.php'; ?>

</body>

</html>