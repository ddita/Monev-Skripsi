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

  $ket = "Pengguna $usr ($nama) mencoba akses Master Mahasiswa sebagai $role";

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

$filterProgres = $_GET['progres'] ?? null;

$whereProgres = '';
if ($filterProgres === 'bimbingan') {
  $whereProgres = "AND LOWER(st.status) = 'bimbingan'";
} elseif ($filterProgres === 'siap_sidang') {
  $whereProgres = "AND LOWER(st.status) = 'siap sidang'";
}

/* ================= QUERY MAHASISWA ================= */
$qMhs = mysqli_query($conn, "SELECT m.nim, m.nama, sk.judul, a.keterangan AS angkatan, COALESCE(st.status, 'Draft') AS status_skripsi, d.nama_dosen, m.aktif
        FROM tbl_mahasiswa m
        LEFT JOIN tbl_skripsi sk ON sk.username = m.nim
        LEFT JOIN tbl_status st ON st.id = sk.status_skripsi
        LEFT JOIN tbl_angkatan a ON m.angkatan = a.kode_angkatan
        LEFT JOIN tbl_dosen d ON m.dosen_pembimbing = d.nip
        WHERE 1=1 $whereProgres
        ORDER BY m.nama ASC"
) or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monev Skripsi | Master Mahasiswa</title>
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
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Manajemen Mahasiswa</h1>
              <small class="text-muted">Data Mahasiswa Skripsi</small>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Mahasiswa</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <!-- CONTENT -->
      <section class="content">
        <div class="container-fluid">

          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-user-graduate"></i> Data Mahasiswa
              </h3>

              <div class="card-tools">
                <a href="tambahmhs.php" class="btn btn-primary btn-sm">
                  <i class="fas fa-user-plus"></i> Tambah
                </a>
                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#importModal">
                  <i class="fas fa-file"></i> Import
                </button>
                <a href="export.php?type=excel" class="btn btn-success btn-sm">
                  <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="export.php?type=pdf" class="btn btn-danger btn-sm">
                  <i class="fas fa-file-pdf"></i> Export PDF
                </a>
              </div>
            </div>

            <div class="card-body table-responsive">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead class="text-center">
                  <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Judul Skripsi</th>
                    <th>Angkatan</th>
                    <th>Progres</th>
                    <th>Dosen</th>
                    <th>Status</th>
                    <th>Aksi</th>
                  </tr>
                </thead>

                <tbody>
                  <?php $no = 1;
                  while ($m = mysqli_fetch_assoc($qMhs)) { ?>
                    <tr class="<?= $m['aktif'] == 0 ? 'row-nonaktif' : '' ?>">
                      <td class="text-center"><?= $no++; ?></td>
                      <td><b><?= $m['nim']; ?></b></td>
                      <td>
                        <?= htmlspecialchars($m['nama']); ?>
                        <?php if ($m['aktif'] == 0): ?>
                          <small class="text-muted">(nonaktif)</small>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?= $m['judul']
                          ? htmlspecialchars(mb_strimwidth($m['judul'], 0, 50, '...'))
                          : '<span class="text-muted">Belum ada judul</span>'; ?>
                      </td>
                      <td class="text-center"><?= $m['angkatan']; ?></td>
                      <td class="text-center">
                        <?php
                        $status = strtolower(trim($m['status_skripsi']));
                        $map = [
                          'draft'             => 'secondary',
                          'bimbingan'         => 'warning',
                          'seminar proposal'  => 'primary',
                          'revisi'            => 'purple',
                          'siap sidang'       => 'success',
                          'lulus'             => 'dark'
                        ];
                        $badge = $map[$status] ?? 'secondary';
                        ?>
                        <span class="badge badge-<?= $badge; ?>">
                          <?= ucfirst($m['status_skripsi']); ?>
                        </span>
                      </td>
                      <td><?= $m['nama_dosen'] ?? '-'; ?></td>
                      <td class="text-center">
                        <?= $m['aktif'] ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-secondary">Nonaktif</span>'; ?>
                      </td>
                      <td class="text-center">
                        <!-- EDIT DATA -->
                        <?php if ($m['aktif'] == 1) { ?>
                          <a href="editmhs.php?nim=<?= encriptData($m['nim']); ?>" class="btn btn-sm btn-warning" title="Edit Data">
                            <i class="fas fa-edit"></i>
                          </a>
                        <?php } ?>

                        <!-- DETAIL MAHASISWA -->
                        <a href="detailmhs.php?nim=<?= encriptData($m['nim']); ?>" class="btn btn-sm btn-secondary" title="Detail & Timeline">
                          <i class="fas fa-eye"></i>
                        </a>

                        <!-- NONAKTIFKAN -->
                        <?php if ($m['aktif'] == 1) { ?>
                          <a href="proses.php?action=nonaktif&nim=<?= encriptData($m['nim']); ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Nonaktifkan mahasiswa ini?')"
                            title="Nonaktifkan">
                            <i class="fas fa-user-slash"></i>
                          </a>
                        <?php } ?>
                        <!-- HAPUS -->
                        <a href="proses.php?action=hapus&nim=<?= encriptData($m['nim']); ?>"
                          class="btn btn-sm btn-dark"
                          onclick="return confirm('Hapus permanen data mahasiswa?')"
                          title="Hapus">
                          <i class="fas fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>

              </table>
            </div>
          </div>

        </div>
      </section>

      <!-- Modal Import -->
      <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <form method="POST" enctype="multipart/form-data" action="import.php">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data Mahasiswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <p>Pilih file Excel (.xlsx / .xls) yang akan diimport.</p>
                <input type="file" name="file_excel" accept=".xlsx,.xls" required>
              </div>
              <div class="modal-footer">
                <button type="submit" name="import" class="btn btn-primary">Import</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
    <?php include '../footer.php'; ?>
  </div>
  <?php include '../mhs_script.php'; ?>

</body>

</html>