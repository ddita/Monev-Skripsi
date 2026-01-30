<?php
session_start();
$konstruktor = 'admin_konsentrasi';
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

  mysqli_query($conn, "INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES ('$usr','$waktu','$ket')");

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

/* ================= AUTO NONAKTIF / AKTIF BERDASARKAN BEBAN ================= */
mysqli_query($conn, "UPDATE tbl_bidang_skripsi b LEFT JOIN (SELECT s.id_bidang, COUNT(m.nim) AS total_mhs FROM tbl_skripsi s
    JOIN tbl_mahasiswa m ON m.nim = s.username
    WHERE m.aktif = 1
      AND s.id_status != 6
    GROUP BY s.id_bidang) x ON x.id_bidang = b.id_bidang
    SET b.status_aktif = 
    CASE 
      WHEN IFNULL(x.total_mhs,0) > 4 THEN 'Nonaktif'
      ELSE 'Aktif'
    END
");

/* ================= QUERY LIST BIDANG ================= */
$qbidang = mysqli_query($conn, "SELECT b.id_bidang, b.nama_bidang, b.status_aktif, p.nama_prodi, COUNT(m.nim) AS jumlah_mhs
  FROM tbl_bidang_skripsi b
  JOIN tbl_prodi p ON p.id_prodi = b.id_prodi
  LEFT JOIN tbl_skripsi s ON s.id_bidang = b.id_bidang
  LEFT JOIN tbl_mahasiswa m 
    ON m.nim = s.username
    AND m.aktif = 1
    AND s.id_status != 6
  GROUP BY b.id_bidang, b.nama_bidang, b.status_aktif, p.nama_prodi
  ORDER BY b.status_aktif DESC, b.nama_bidang ASC
");

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
      <img class="animation__shake" src="../images/UP.png" alt="Monev-Skripsi" height="60" width="60">
    </div>

    <?php include '../mhs_navbar.php'; ?>
    <?php include '../admin_sidebar.php'; ?>

    <div class="content-wrapper">

      <!-- HEADER -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">
                <i class="fas fa-book text-primary"></i>
                Bidang Konsentrasi
              </h1>
              <small class="text-muted">
                Topik Penelitian Skripsi
              </small>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Bidang Konsentrasi</li>
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
                <i class="fas fa-list"></i> Data Konsentrasi Skripsi
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
                    <th>Program Studi</th>
                    <th>Nama Bidang</th>
                    <th>Jumlah Mahasiswa</th>
                    <th>Status Beban</th>
                    <th>Status</th>
                    <th width="12%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  while ($dt = mysqli_fetch_assoc($qbidang)) {

                    $jml = (int) $dt['jumlah_mhs'];

                    if ($jml > 4) {
                      $badgeBeban = 'danger';
                      $labelBeban = 'Overload';
                    } elseif ($jml >= 3) {
                      $badgeBeban = 'warning';
                      $labelBeban = 'Padat';
                    } else {
                      $badgeBeban = 'success';
                      $labelBeban = 'Normal';
                    }
                  ?>
                    <tr>
                      <td class="text-center"><?= $no++; ?></td>

                      <td class="text-center">
                        <strong><?= htmlspecialchars($dt['nama_prodi']); ?></strong>
                      </td>

                      <td class="text-center">
                        <?= htmlspecialchars($dt['nama_bidang']); ?>
                      </td>

                      <td class="text-center">
                        <?= $jml ?>
                      </td>

                      <td class="text-center">
                        <span class="badge badge-<?= $badgeBeban ?>">
                          <?= $labelBeban ?>
                        </span>
                      </td>

                      <td class="text-center">
                        <?php if ($dt['status_aktif'] === 'Aktif'): ?>
                          <span class="badge badge-success">
                            <i class="fas fa-check-circle"></i> Aktif
                          </span>
                        <?php else: ?>
                          <span class="badge badge-secondary">
                            <i class="fas fa-minus-circle"></i> Nonaktif
                          </span>
                        <?php endif; ?>
                      </td>

                      <td class="text-center">
                        <?php if ($dt['status_aktif'] === 'Aktif'): ?>
                          <a href="proses.php?action=toggle&id_bidang=<?= encriptData($dt['id_bidang']); ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Nonaktifkan bidang ini?')">
                            <i class="fas fa-power-off"></i>
                          </a>
                        <?php else: ?>
                          <a href="proses.php?action=toggle&id_bidang=<?= encriptData($dt['id_bidang']); ?>"
                            class="btn btn-sm btn-success"
                            onclick="return confirm('Aktifkan kembali bidang ini?')">
                            <i class="fas fa-check"></i>
                          </a>
                        <?php endif; ?>

                        <a href="proses.php?action=hapus&id_bidang=<?= encriptData($dt['id_bidang']); ?>"
                          class="btn btn-sm btn-outline-danger"
                          onclick="return confirm('Hapus bidang <?= $dt['nama_bidang']; ?> ?')">
                          <i class="fas fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                  <?php } ?>

                  <?php if (mysqli_num_rows($qbidang) == 0): ?>
                    <tr>
                      <td colspan="7" class="text-center text-muted">
                        Data bidang skripsi belum tersedia
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