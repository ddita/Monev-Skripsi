<?php
session_start();
$konstruktor = 'admin_dosen';
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

  $ket = "Pengguna $usr ($nama) mencoba akses Master Dosen sebagai $role";

  mysqli_query($conn, "INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES ('$usr','$waktu','$ket')");

  header("Location: ../login/logout.php");
  exit;
}

/* ================= ENKRIPSI ================= */
function encriptData($data)
{
  $key = 'monev_skripsi_2024';
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

if (!isset($_GET['nip'])) {
  die('Parameter tidak valid');
}

$nip = decriptData($_GET['nip']);
if (!$nip) die('Data rusak');

/* ================= DATA DOSEN ================= */
$qDosen = mysqli_query($conn, "
  SELECT nip, nama_dosen 
  FROM tbl_dosen 
  WHERE nip='$nip'
");
$dosen = mysqli_fetch_assoc($qDosen);
if (!$dosen) die('Dosen tidak ditemukan');

/* === BARU ENKRIP UNTUK LINK EXPORT === */
$nipEnc = encriptData($dosen['nip']);

/* ================= DATA MAHASISWA ================= */
$qMhs = mysqli_query($conn, "SELECT m.nim, m.nama, m.prodi, s.judul FROM tbl_mahasiswa m
          LEFT JOIN tbl_skripsi s ON s.username = m.nim
          WHERE m.dosen_pembimbing = '$nip'
          AND m.aktif = 1
          AND m.status_skripsi != 6
          ORDER BY m.nama ASC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Detail Bimbingan Dosen</title>
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
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Detail Mahasiswa Bimbingan</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../admin_dosen">Dosen</a></li>
                <li class="breadcrumb-item active">Mahasiswa Bimbingan</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <a href="../admin_dosen" class="btn btn-warning btn-sm mb-3">
            <i class="nav-icon fas fa-chevron-left"></i> Kembali
          </a>
          <div class="card">
            <div class="card-header bg-info">
              <div>
                <h5 class="mb-0 text-white">
                  <i class="fas fa-user-tie mr-1"></i>
                  <?= htmlspecialchars($dosen['nama_dosen']); ?>
                </h5>
                <small class="text-white-50">
                  NIP: <strong><?= htmlspecialchars($dosen['nip']); ?></strong>
                </small>
              </div>
            </div>

            <div class="card-body">
              <div class="mb-3">
                <!-- EXPORT -->
                <a href="export.php?type=excel&nip=<?= $nipEnc ?>"
                  class="btn btn-success btn-sm mb-3">
                  <i class="fas fa-file-excel"></i> Export Excel
                </a>

                <a href="export.php?type=pdf&nip=<?= $nipEnc ?>"
                  class="btn btn-danger btn-sm mb-3">
                  <i class="fas fa-file-pdf"></i> Export PDF
                </a>
              </div>

              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead class="text-center">
                  <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Prodi</th>
                    <th>Judul Skripsi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;

                  if (!$qMhs) {
                    echo '<tr>';
                    echo '<td colspan="5" class="text-danger text-center">';
                    echo 'Query gagal:<br><small>' . mysqli_error($conn) . '</small>';
                    echo '</td>';
                    echo '</tr>';
                  } elseif (mysqli_num_rows($qMhs) == 0) {
                    echo '<tr>';
                    echo '<td colspan="5" class="text-center text-muted">';
                    echo 'Belum ada mahasiswa bimbingan';
                    echo '</td>';
                    echo '</tr>';
                  } else {
                    while ($m = mysqli_fetch_assoc($qMhs)) {
                  ?>
                      <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><?= htmlspecialchars($m['nim']); ?></td>
                        <td><?= htmlspecialchars($m['nama']); ?></td>
                        <td><?= htmlspecialchars($m['prodi']); ?></td>
                        <td><?= htmlspecialchars($m['judul'] ?? '-'); ?></td>
                      </tr>
                  <?php }
                  } ?>
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
</body>

</html>