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

  $ket = "Pengguna $usr ($nama) mencoba akses Detail Mahasiswa sebagai $role";

  mysqli_query(
    $conn,
    "INSERT INTO tbl_cross_auth (username,waktu,keterangan)
     VALUES ('$usr','$waktu','$ket')"
  );

  header("Location: ../login/logout.php");
  exit;
}

/* ================= FUNGSI DEKRIP ================= */
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
if (!isset($_GET['nim'])) {
  header("Location: index.php");
  exit;
}

$nim_decrypt = decriptData($_GET['nim']);
if (!$nim_decrypt) {
  header("Location: index.php?err=invalid");
  exit;
}

$nim = mysqli_real_escape_string($conn, $nim_decrypt);

/* ================= QUERY DETAIL MAHASISWA ================= */
$qMhs = mysqli_query($conn, "
  SELECT 
    m.nim, m.nama, m.aktif,
    p.nama_prodi,
    a.keterangan AS angkatan,
    s.status AS status_skripsi,
    d.nama_dosen
  FROM tbl_mahasiswa m
  LEFT JOIN tbl_prodi p ON m.prodi = p.kode_prodi
  LEFT JOIN tbl_angkatan a ON m.angkatan = a.kode_angkatan
  LEFT JOIN tbl_status s ON m.status_skripsi = s.id
  LEFT JOIN tbl_dosen d ON m.dosen_pembimbing = d.nip
  WHERE m.nim='$nim'
") or die(mysqli_error($conn));

if (mysqli_num_rows($qMhs) === 0) {
  header("Location: index.php?err=notfound");
  exit;
}

$mhs = mysqli_fetch_assoc($qMhs);

/* ================= TIMELINE STATUS ================= */
$qStatus = mysqli_query($conn, "
  SELECT l.waktu, s.status, l.keterangan
  FROM tbl_status_log l
  JOIN tbl_status s ON l.status_id = s.id
  WHERE l.nim='$nim'
  ORDER BY l.waktu DESC
");

/* ================= TIMELINE BIMBINGAN ================= */
$qBimbingan = mysqli_query($conn, "
  SELECT tanggal, topik, catatan
  FROM tbl_bimbingan
  WHERE nim='$nim'
  ORDER BY tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Detail Mahasiswa | Monev Skripsi</title>
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
      <img class="animation__shake" src="../images/UP.png" height="60">
    </div>

    <div class="content-wrapper">

      <!-- HEADER -->
      <section class="content-header">
        <div class="container-fluid">
          <h1>Detail Mahasiswa</h1>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">

          <!-- INFO MAHASISWA -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-user-graduate"></i> Informasi Akademik
              </h3>
            </div>
            <div class="card-body">
              <table class="table table-sm table-bordered">
                <tr>
                  <th width="200">NIM</th>
                  <td><?= $mhs['nim']; ?></td>
                </tr>
                <tr>
                  <th>Nama</th>
                  <td><?= htmlspecialchars($mhs['nama']); ?></td>
                </tr>
                <tr>
                  <th>Program Studi</th>
                  <td><?= $mhs['nama_prodi']; ?></td>
                </tr>
                <tr>
                  <th>Angkatan</th>
                  <td><?= $mhs['angkatan']; ?></td>
                </tr>
                <tr>
                  <th>Status Aktif</th>
                  <td>
                    <?= $mhs['aktif']
                      ? '<span class="badge badge-success">Aktif</span>'
                      : '<span class="badge badge-secondary">Nonaktif</span>'; ?>
                  </td>
                </tr>
                <tr>
                  <th>Dosen Pembimbing</th>
                  <td><?= $mhs['nama_dosen'] ?? '-'; ?></td>
                </tr>
                <tr>
                  <th>Status Skripsi</th>
                  <td><?= $mhs['status_skripsi'] ?? 'Draft'; ?></td>
                </tr>
              </table>
            </div>
          </div>

          <div class="row">

            <!-- TIMELINE STATUS -->
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-stream"></i> Timeline Status Skripsi
                  </h3>
                </div>
                <div class="card-body">
                  <?php if (mysqli_num_rows($qStatus) > 0) { ?>
                    <ul class="timeline">
                      <?php while ($s = mysqli_fetch_assoc($qStatus)) { ?>
                        <li>
                          <i class="fas fa-flag bg-primary"></i>
                          <div class="timeline-item">
                            <span class="time">
                              <i class="far fa-clock"></i>
                              <?= date('d M Y H:i', strtotime($s['waktu'])); ?>
                            </span>
                            <h3 class="timeline-header">
                              <?= $s['status']; ?>
                            </h3>
                            <div class="timeline-body">
                              <?= $s['keterangan'] ?? '-'; ?>
                            </div>
                          </div>
                        </li>
                      <?php } ?>
                    </ul>
                  <?php } else { ?>
                    <p class="text-muted">Belum ada riwayat status.</p>
                  <?php } ?>
                </div>
              </div>
            </div>

            <!-- TIMELINE BIMBINGAN -->
            <div class="col-md-6">
              <div class="card">
                <div class="card-header">
                  <h3 class="card-title">
                    <i class="fas fa-comments"></i> Timeline Bimbingan
                  </h3>
                </div>
                <div class="card-body">
                  <?php if (mysqli_num_rows($qBimbingan) > 0) { ?>
                    <ul class="timeline">
                      <?php while ($b = mysqli_fetch_assoc($qBimbingan)) { ?>
                        <li>
                          <i class="fas fa-comment bg-warning"></i>
                          <div class="timeline-item">
                            <span class="time">
                              <i class="far fa-calendar"></i>
                              <?= date('d M Y', strtotime($b['tanggal'])); ?>
                            </span>
                            <h3 class="timeline-header">
                              <?= $b['topik']; ?>
                            </h3>
                            <div class="timeline-body">
                              <?= nl2br(htmlspecialchars($b['catatan'])); ?>
                            </div>
                          </div>
                        </li>
                      <?php } ?>
                    </ul>
                  <?php } else { ?>
                    <p class="text-muted">Belum ada bimbingan.</p>
                  <?php } ?>
                </div>
              </div>
            </div>

          </div>

          <a href="index.php" class="btn btn-secondary mt-3">
            <i class="fas fa-arrow-left"></i> Kembali
          </a>

        </div>
      </section>

    </div>

    <?php include '../footer.php'; ?>
  </div>

  <?php include '../mhs_script.php'; ?>
</body>

</html>