<?php
session_start();
$konstruktor = 'admin_prodi';
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
                <i class="fas fa-university text-primary"></i>
                Program Studi
              </h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Program Studi</li>
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
                <i class="fas fa-list"></i> Data Program Studi
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
                    <th>Kode Prodi</th>
                    <th>Nama Prodi</th>
                    <th>Jenjang</th>
                    <th>Status Aktif</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>

                  <?php
                  $no = 1;
                  $qprodi = mysqli_query($conn, "SELECT * FROM tbl_prodi");

                  if (mysqli_num_rows($qprodi) > 0):
                    while ($dt = mysqli_fetch_assoc($qprodi)):
                  ?>

                      <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center">
                          <strong><?= htmlspecialchars($dt['kode_prodi']); ?></strong>
                        </td>
                        <td>
                          <strong><?= htmlspecialchars($dt['nama_prodi']); ?></strong>
                        </td>
                        <td class="text-center">
                          <strong><?= htmlspecialchars($dt['jenjang']); ?></strong>
                        </td>
                        <td class="text-center">
                          <strong><?= htmlspecialchars($dt['status_aktif']); ?></strong>
                        </td>
                        <td class="text-center">
                          <!-- EDIT DATA -->
                          <a href="edit.php?id_prodi=<?= encriptData($dt['id_prodi']); ?>" class="btn btn-sm btn-warning" title="Edit Data">
                            <i class="fas fa-edit"></i>
                          </a>
                          <?php if ($dt['status_aktif'] == 'Aktif'): ?>
                            <!-- NONAKTIFKAN -->
                            <a href="proses.php?action=toggle&id_prodi=<?= encriptData($dt['id_prodi']); ?>"
                              class="btn btn-sm btn-danger"
                              onclick="return confirm('Nonaktifkan prodi ini?')"
                              title="Nonaktifkan">
                              <i class="fas fa-power-off"></i>
                            </a>
                          <?php else: ?>
                            <!-- AKTIFKAN -->
                            <a href="proses.php?action=toggle&id_prodi=<?= encriptData($dt['id_prodi']); ?>"
                              class="btn btn-sm btn-success"
                              onclick="return confirm('Aktifkan kembali prodi ini?')"
                              title="Aktifkan">
                              <i class="fas fa-check"></i>
                            </a>
                          <?php endif; ?>
                          <!-- HAPUS -->
                          <a href="proses.php?action=hapus&id_prodi=<?= encriptData($dt['id_prodi']); ?>"
                            class="btn btn-sm btn-outline-danger"
                            data-toggle="tooltip"
                            title="Hapus data"
                            onclick="return confirm('Hapus periode <?= $dt['nama_prodi']; ?> ?')">
                            <i class="fas fa-trash"></i>
                          </a>
                        </td>
                      </tr>

                    <?php endwhile;
                  else: ?>

                    <tr>
                      <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-info-circle"></i>
                        Tidak ada data
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