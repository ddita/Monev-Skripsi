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

/* ================= AMBIL NIP ================= */
$nip = decriptData($_GET['nip'] ?? '');
if (!$nip) {
  die("NIP tidak valid");
}

/* ================= DATA DOSEN ================= */
$qDosen = mysqli_query($conn, "
  SELECT 
    d.nip,
    d.nama_dosen,
    d.aktif AS status_dosen,
    CASE 
      WHEN u.status IS NULL THEN 'nonaktif'
      ELSE u.status
    END AS status_user
  FROM tbl_dosen d
  LEFT JOIN tbl_users u ON u.username = d.nip
  WHERE d.nip = '$nip'
");

$dosen = mysqli_fetch_assoc($qDosen);
if (!$dosen) {
  die("Data dosen tidak ditemukan");
}

/* ================= TOTAL MAHASISWA ================= */
$qTotal = mysqli_query($conn, "
  SELECT COUNT(*) AS total
  FROM tbl_mahasiswa m
  LEFT JOIN tbl_skripsi s
    ON s.username = m.nim
   AND s.id_skripsi = (
      SELECT MAX(s2.id_skripsi)
      FROM tbl_skripsi s2
      WHERE s2.username = m.nim
   )
  WHERE m.dosen_pembimbing = '$nip'
    AND m.aktif = 1
    AND (s.status_skripsi IS NULL OR s.status_skripsi != 'lulus')
");

$totalMhs = mysqli_fetch_assoc($qTotal)['total'];

/* ================= BADGE BEBAN ================= */
if ($totalMhs <= 5) {
  $badgeBeban = '<span class="badge badge-success">Normal</span>';
} elseif ($totalMhs <= 10) {
  $badgeBeban = '<span class="badge badge-warning">Padat</span>';
} else {
  $badgeBeban = '<span class="badge badge-danger">Overload</span>';
}

/* ================= DATA MAHASISWA ================= */
$qMhs = mysqli_query($conn, "
    SELECT 
        m.nim,
        m.nama,
        m.prodi,
        m.angkatan,

        s.judul,
        COALESCE(s.status_skripsi, 'belum') AS status_skripsi,

        p.nama_periode

    FROM tbl_mahasiswa m

    LEFT JOIN tbl_skripsi s 
        ON s.username = m.nim
       AND s.id_skripsi = (
            SELECT MAX(s2.id_skripsi)
            FROM tbl_skripsi s2
            WHERE s2.username = m.nim
       )

    LEFT JOIN tbl_periode p 
        ON p.id_periode = s.id_periode

    WHERE m.dosen_pembimbing = '$nip'
      AND m.aktif = 1
      AND (s.status_skripsi IS NULL OR s.status_skripsi != 'lulus')

    ORDER BY m.nama ASC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Detail Dosen</title>
  <script>
    (function() {
      const theme = localStorage.getItem("theme") || "dark";
      document.documentElement.classList.add(theme + "-mode");
    })();
  </script>
  <?php include '../mhs_listlink.php'; ?>
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
          <h1>Detail Dosen</h1>
        </div>
      </section>

      <section class="content">

        <div class="container-fluid">
          <a href="../admin_dosen" class="btn btn-warning btn-sm mb-3">
            <i class="nav-icon fas fa-chevron-left"></i> Kembali
          </a>
          <!-- ================= KARTU DOSEN ================= -->
          <div class="card card-primary">
            <div class="card-body">
              <table class="table table-borderless">
                <tr>
                  <th width="220">Nama Dosen</th>
                  <td><strong><?= htmlspecialchars($dosen['nama_dosen']) ?></strong></td>
                </tr>
                <tr>
                  <th>NIP</th>
                  <td><?= $dosen['nip'] ?></td>
                </tr>
                <tr>
                  <th>Status Dosen</th>
                  <td>
                    <?= $dosen['status_dosen'] == 1
                      ? '<span class="badge badge-success">Aktif</span>'
                      : '<span class="badge badge-danger">Nonaktif</span>' ?>
                  </td>
                </tr>
                <tr>
                  <th>Status Akun</th>
                  <td>
                    <?php if ($dosen['status_user'] === 'aktif'): ?>
                      <span class="badge badge-success">Aktif</span>
                    <?php else: ?>
                      <span class="badge badge-danger">Nonaktif</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <th>Beban Bimbingan</th>
                  <td><?= $badgeBeban ?></td>
                </tr>
                <tr>
                  <th>Total Mahasiswa</th>
                  <td><strong><?= $totalMhs ?></strong> Mahasiswa</td>
                </tr>
              </table>
            </div>
          </div>

          <!-- ================= TABEL MAHASISWA ================= -->
          <div class="card card-outline card-info">
            <div class="card-header">
              <h3 class="card-title">Daftar Mahasiswa Bimbingan</h3>
            </div>

            <div class="card-body table-responsive">
              <table class="table table-bordered table-striped">
                <thead class="text-center">
                  <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th>Prodi</th>
                    <th>Judul Skripsi</th>
                    <th>Status Skripsi</th>
                    <th>Semester / Angkatan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  while ($m = mysqli_fetch_assoc($qMhs)) { ?>
                    <tr>
                      <td class="text-center"><?= $no++ ?></td>
                      <td><?= $m['nim'] ?></td>
                      <td><?= htmlspecialchars($m['nama']) ?></td>
                      <td><?= $m['prodi'] ?></td>
                      <td><?= $m['judul'] ?? '<em>Belum ada</em>' ?></td>
                      <td class="text-center">
                        <?php
                        $status = $m['status_skripsi'];
                        $badgeMap = [
                          'draft'             => 'secondary',
                          'bimbingan'         => 'warning',
                          'seminar proposal'  => 'primary',
                          'revisi'            => 'purple',
                          'siap sidang'       => 'success',
                          'lulus'             => 'dark'
                        ];

                        $badgeClass = $badgeMap[$status] ?? 'secondary';
                        $label      = ucfirst($status);

                        echo "<span class='badge badge-$badgeClass'>$label</span>";
                        ?>
                      </td>
                      <td class="text-center">
                        <?= $m['nama_periode'] ?? '-' ?> / <?= $m['angkatan'] ?>
                      </td>
                    </tr>
                  <?php } ?>

                  <?php if ($totalMhs == 0) { ?>
                    <tr>
                      <td colspan="7" class="text-center text-muted">
                        Belum ada mahasiswa bimbingan
                      </td>
                    </tr>
                  <?php } ?>
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