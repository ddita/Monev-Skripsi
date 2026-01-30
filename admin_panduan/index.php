<?php
session_start();
$konstruktor = 'admin_panduan';
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

$query = mysqli_query($conn, "SELECT * FROM tbl_panduan_skripsi ORDER BY uploaded_at DESC");
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
                Panduan & Template
              </h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Panduan</li>
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
                <i class="fas fa-list"></i> Data Panduan
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
                    <th>Judul Panduan</th>
                    <th>Tahun Akademik</th>
                    <th>File</th>
                    <th>Tanggal Upload</th>
                    <th width="15%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;

                  if (mysqli_num_rows($query) > 0):
                    while ($dt = mysqli_fetch_assoc($query)):
                  ?>
                      <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td><strong><?= htmlspecialchars($dt['judul']); ?></strong></td>
                        <td class="text-center"><?= htmlspecialchars($dt['tahun_akademik']); ?></td>
                        <td class="text-center">
                          <a href="../uploads/panduan/<?= htmlspecialchars($dt['file']); ?>"
                            target="_blank"
                            class="btn btn-sm btn-info">
                            <i class="fas fa-download"></i> Unduh
                          </a>
                        </td>
                        <td class="text-center">
                          <?= date('d-m-Y H:i', strtotime($dt['uploaded_at'])); ?>
                        </td>
                        <td class="text-center">

                          <!-- EDIT -->
                          <a href="edit.php?id_panduan=<?= encriptData($dt['id_panduan']); ?>"
                            class="btn btn-sm btn-warning"
                            title="Edit">
                            <i class="fas fa-edit"></i>
                          </a>

                          <!-- HAPUS -->
                          <a href="proses.php?action=hapus&id_panduan=<?= $dt['id_panduan']; ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Hapus panduan <?= $dt['judul']; ?> ?')"
                            title="Hapus">
                            <i class="fas fa-trash"></i>
                          </a>

                        </td>
                      </tr>

                    <?php endwhile;
                  else: ?>
                    <tr>
                      <td colspan="6" class="text-center text-muted">
                        <i class="fas fa-info-circle"></i> Tidak ada data panduan
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