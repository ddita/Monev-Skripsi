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

  $ket = "Pengguna $usr ($nama) mencoba akses Detail Skripsi sebagai $role";

  mysqli_query(
    $conn,
    "INSERT INTO tbl_cross_auth (username, waktu, keterangan)
     VALUES ('$usr','$waktu','$ket')"
  );

  header("Location: ../login/logout.php");
  exit;
}

/* ================= AMBIL DATA ================= */
$nim_encrypted = $_GET['nim'] ?? '';

if (empty($nim_encrypted)) {
  echo "<div style='padding:20px'>
          <h4>Data tidak ditemukan</h4>
          <p>NIM tidak dikirim.</p>
        </div>";
  exit;
}

function decryptData($data)
{
  $key = 'monev_skripsi_2024'; // HARUS sama dengan enkripsi

  return openssl_decrypt(
    base64_decode(urldecode($data)),
    'AES-128-ECB',
    $key
  );
}
$nim = decryptData($nim_encrypted);
$nim = mysqli_real_escape_string($conn, $nim);

if (empty($nim)) {
  echo "<div style='padding:20px'>
          <h4>Data tidak valid</h4>
          <p>NIM gagal didekripsi.</p>
        </div>";
  exit;
}

$q = mysqli_query($conn, "SELECT m.nim, m.nama, m.aktif, p.nama_prodi, a.keterangan AS angkatan, d.nama_dosen AS pembimbing, s.id_skripsi, s.judul,
    s.created_at, s.updated_at, st.status, pr.nama_periode, pr.semester, ta.tahun_akademik, b.nama_bidang FROM tbl_mahasiswa m
    LEFT JOIN tbl_prodi p ON m.prodi = p.kode_prodi
    LEFT JOIN tbl_angkatan a ON m.angkatan = a.kode_angkatan
    LEFT JOIN tbl_dosen d ON m.dosen_pembimbing = d.nip
    LEFT JOIN tbl_skripsi s ON m.nim = s.username
    LEFT JOIN tbl_status st ON m.id_status = st.id
    LEFT JOIN tbl_periode pr ON m.id_periode = pr.id_periode
    LEFT JOIN tbl_tahun_akademik ta ON pr.id_tahun = ta.id_tahun
    LEFT JOIN tbl_bidang_skripsi b ON s.id_bidang = b.id_bidang
    WHERE m.nim = '$nim'
") or die(mysqli_error($conn));

if (mysqli_num_rows($q) == 0) {
  echo "<div style='padding:20px'>
          <h4>Data tidak ditemukan</h4>
          <p>Mahasiswa dengan NIM <b>$nim</b> tidak terdaftar.</p>
        </div>";
  exit;
}

$data = mysqli_fetch_assoc($q);

/* ================= PROGRESS ================= */
$progressMap = [
  'Draft'  => 0,
  'Bimbingan' => 20,
  'Seminar Proposal' => 40,
  'Revisi'   => 60,
  'Sidang Skripsi'    => 80,
  'Lulus'    => 100
];
$progress = $progressMap[$data['status'] ?? ''] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Monev Skripsi | Detail Skripsi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Light / Dark Mode -->
  <script>
    (function() {
      const theme = localStorage.getItem("theme") || "dark";
      document.documentElement.classList.add(theme + "-mode");
    })();
  </script>
  <?php include '../mhs_listlink.php'; ?>
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
      <!-- CONTENT HEADER -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Detail & Timeline Skripsi</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Dashboard Mahasiswa</a></li>
                <li class="breadcrumb-item active">Detail Mahasiswa</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <!-- CONTENT -->
      <section class="content">

        <div class="container-fluid">
          <a href="../admin_mahasiswa" class="btn btn-warning btn-sm mb-3">
            <i class="nav-icon fas fa-chevron-left"></i> Kembali
          </a>
          <div class="row">
            <!-- ================= KOLOM KIRI ================= -->
            <div class="col-lg-6 col-md-12">

              <!-- DATA MAHASISWA -->
              <div class="card card-primary">
                <div class="card-header">
                  <h3 class="card-title">Data Mahasiswa</h3>
                </div>
                <div class="card-body">
                  <?php
                  $badge = 'secondary';
                  if ($data['status'] == 'Lulus') $badge = 'success';
                  elseif ($data['status'] == 'Bimbingan') $badge = 'warning';
                  elseif ($data['status'] == 'Seminar Proposal') $badge = 'info';
                  elseif ($data['status'] == 'Revisi') $badge = 'danger';
                  elseif ($data['status'] == 'Sidang Skripsi') $badge = 'primary';
                  ?>
                  <span class="badge badge-<?= $badge ?>">
                    <?=
                    $data['status'] ?? 'Belum Mengajukan';
                    ?>
                  </span>

                  <table class="table table-sm table-borderless mt-2">
                    <tr>
                      <th>NIM</th>
                      <td><?= $data['nim']; ?></td>
                    </tr>
                    <tr>
                      <th>Nama</th>
                      <td><?= $data['nama']; ?></td>
                    </tr>
                    <tr>
                      <th>Prodi</th>
                      <td><?= $data['nama_prodi']; ?></td>
                    </tr>
                    <tr>
                      <th>Angkatan</th>
                      <td><?= $data['angkatan']; ?></td>
                    </tr>
                    <tr>
                      <th>Pembimbing</th>
                      <td><?= $data['pembimbing'] ?? '-'; ?></td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td>
                        <span class="badge badge-info">
                          <?= $data['status'] ?? 'Belum Mengajukan'; ?>
                        </span>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>

            </div>

            <!-- ================= KOLOM KANAN ================= -->
            <div class="col-lg-6 col-md-12">
              <!-- INFORMASI SKRIPSI -->
              <div class="card card-success">
                <div class="card-header">
                  <h3 class="card-title">Informasi Skripsi</h3>
                </div>
                <div class="card-body">
                  <p><strong>Judul:</strong><br><?= $data['judul'] ?? '-'; ?></p>
                  <p><strong>Bidang Skripsi:</strong><?= $data['nama_bidang'] ?? '-' ?></p>
                  <p><strong>Periode:</strong><?= $data['nama_periode'] ?? '-'; ?></p>
                  <p><strong>Tahun Akademik:</strong><?= $data['tahun_akademik']; ?> (<?= $data['semester']; ?>)</p>
                  <p><strong>Tanggal Pengajuan:</strong>
                    <?= isset($data['created_at'])
                      ? date('d-m-Y', strtotime($data['created_at']))
                      : '-' ?>
                  </p>
                </div>
              </div>
            </div>

            <!-- PROGRESS SKRIPSI -->
            <div class="card card-info col-md-12">
              <div class="card-header">
                <h3 class="card-title">Progress Skripsi</h3>
              </div>
              <div class="card-body">
                <div class="progress">
                  <div class="progress-bar progress-bar-striped bg-info"
                    style="width: <?= $progress ?>%">
                    <?= $progress ?>%
                  </div>
                </div>
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