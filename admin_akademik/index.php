<?php
session_start();
$konstruktor = 'admin_akademik';
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

  $ket = "Pengguna $usr ($nama) mencoba akses Akademik Manajemen sebagai $role";

  mysqli_query(
    $conn,
    "INSERT INTO tbl_cross_auth (username, waktu, keterangan)
     VALUES ('$usr','$waktu','$ket')"
  );

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

/* ================= QUERY DATA ================= */
$query = mysqli_query($conn, "SELECT * FROM tbl_tahun_akademik ORDER BY status_aktif DESC, tahun_akademik DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monev Skripsi | Tahun Akademik</title>
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
              <h1>Manajemen Tahun Akademik</h1>
              <!-- <small class="text-muted">Kelola data dosen & beban bimbingan skripsi</small> -->
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Tahun Akademik</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <section class="content">
        <div class="container-fluid">
          <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-calendar-alt"></i> Tahun Akademik
              </h3>

              <div class="card-tools">
                <a href="tambah.php" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus"></i> Tambah
                </a>
              </div>
            </div>

            <!-- ================= TABEL DATA ================= -->
            <div class="card-body table-responsive">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead class="text-center">
                  <tr>
                    <th width="5%">No</th>
                    <th width="20%">Tahun Akademik</th>
                    <th width="15%">Status</th>
                    <th>Keterangan</th>
                    <th width="25%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  if (mysqli_num_rows($query) > 0) :
                    while ($row = mysqli_fetch_assoc($query)) :
                      $statusAktif = ($row['status_aktif'] == 'Aktif');
                  ?>
                      <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['tahun_akademik']); ?></td>

                        <!-- STATUS -->
                        <td class="text-center">
                          <?php if ($row['status_aktif'] === 'Aktif') : ?>
                            <span class="badge badge-success">
                              <i class="fas fa-check-circle"></i> Aktif
                            </span>
                          <?php else : ?>
                            <span class="badge badge-secondary">
                              <i class="fas fa-minus-circle"></i> Nonaktif
                            </span>
                          <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($row['keterangan'] ?? '-'); ?></td>

                        <!-- AKSI -->
                        <td class="text-center">
                          <!-- EDIT -->
                          <a href="edit.php?id_tahun=<?= encriptData($row['id_tahun']); ?>"
                            class="btn btn-sm btn-warning"
                            title="Edit Data">
                            <i class="fas fa-edit"></i>
                          </a>

                          <!-- NONAKTIFKAN -->
                          <?php if ($row['status_aktif'] === 'Aktif') : ?>
                            <a href="proses.php?action=toggle&id_tahun=<?= encriptData($row['id_tahun']); ?>"
                              class="btn btn-sm btn-danger"
                              onclick="return confirm('Nonaktifkan tahun akademik ini?')"
                              title="Nonaktifkan">
                              <i class="fas fa-power-off"></i>
                            </a>
                          <?php else: ?>
                            <!-- AKTIFKAN -->
                            <a href="proses.php?action=toggle&id_tahun=<?= encriptData($row['id_tahun']); ?>"
                              class="btn btn-sm btn-success"
                              onclick="return confirm('Aktifkan kembali tahun akademik ini? Tahun aktif lain akan dinonaktifkan')"
                              title="Aktifkan">
                              <i class="fas fa-check"></i>
                            </a>
                          <?php endif; ?>

                          <!-- HAPUS -->
                          <a href="proses.php?action=hapus&id_tahun=<?= encriptData($row['id_tahun']); ?>"
                            class="btn btn-sm btn-outline-danger"
                            onclick="return confirm('Hapus permanen data tahun akademik ini?')"
                            title="Hapus">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php
                    endwhile;
                  else :
                    ?>
                    <tr>
                      <td colspan="5" class="text-center text-muted">
                        Data tahun akademik belum tersedia
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <!-- ================= CATATAN UX ================= -->
        <div class="crad card-navy">
          <div class="card-header">
            <h5 class="card-tile">Catatan</h5>
          </div>
          <div class="card-body">
            <ul>
              <li>Hanya <strong>1 Tahun Akademik</strong> yang boleh <strong>Aktif</strong></li>
              <li>Tahun aktif digunakan otomatis di <strong>Periode Skripsi</strong>, <strong>Data Skripsi</strong>, dan <strong>Laporan</strong></li>
              <li>Tahun yang sudah dipakai <strong>tidak bisa dihapus</strong></li>
            </ul>
          </div>
        </div>
      </section>
    </div>
    <?php include '../footer.php'; ?>
  </div>
  <?php include '../mhs_script.php'; ?>
</body>

</html>