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

  $ket = "Pengguna $usr ($nama) mencoba akses Manajemen Dosen sebagai $role";

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

// ================= RINGKASAN =================
$total_dosen_result = mysqli_query($conn, "SELECT COUNT(*) total FROM tbl_dosen");
$total_dosen = $total_dosen_result ? mysqli_fetch_assoc($total_dosen_result)['total'] : 0;

$dosen_aktif_result = mysqli_query($conn, "SELECT COUNT(DISTINCT dosen_pembimbing) total FROM tbl_mahasiswa
                      WHERE dosen_pembimbing IS NOT NULL
                      AND aktif = 1
                      AND id_status != 6
");
$dosen_aktif = $dosen_aktif_result ? mysqli_fetch_assoc($dosen_aktif_result)['total'] : 0;

$dosen_overload_result = mysqli_query($conn, "SELECT COUNT(*) total FROM (SELECT dosen_pembimbing FROM tbl_mahasiswa WHERE dosen_pembimbing IS NOT NULL
                                              AND aktif = 1
                                              AND id_status != 6
                                              GROUP BY dosen_pembimbing
                                              HAVING COUNT(*) > 8) x
");
$dosen_overload = $dosen_overload_result ? mysqli_fetch_assoc($dosen_overload_result)['total'] : 0;
// Tambahkan di admin_dosen, setelah query $dosen_overload
$labelProgres = []; // Kosong karena tidak ada chart di halaman ini
$dataProgres = [];
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monev Skripsi | Master Dosen</title>
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
              <h1>Manajemen Dosen</h1>
              <small class="text-muted">Kelola data dosen & beban bimbingan skripsi</small>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Dosen</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <!-- INFO BOX -->
      <section class="content">
        <div class="container-fluid">

          <div class="row">
            <div class="col-lg-3 col-md-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?= $total_dosen ?></h3>
                  <p>Total Dosen</p>
                </div>
                <div class="icon">
                  <i class="fas fa-user-tie"></i>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?= $dosen_aktif ?></h3>
                  <p>Dosen Aktif Membimbing</p>
                </div>
                <div class="icon">
                  <i class="fas fa-chalkboard-teacher"></i>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><?= $dosen_overload ?></h3>
                  <p>Dosen Overload</p>
                </div>
                <div class="icon">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
              </div>
            </div>

          </div>

          <!-- TABEL DOSEN -->
          <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-users"></i> Daftar Dosen
              </h3>
              <div class="card-tools">
                <a href="tambah.php" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus"></i> Tambah Dosen
                </a>
                <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#importModal">
                  <i class="fas fa-file"></i> Import
                </button>
              </div>
            </div>

            <div class="card-body table-responsive">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead class="text-center">
                  <tr>
                    <th>No</th>
                    <th>NIP</th>
                    <th>Nama Dosen</th>
                    <th>Jumlah Mahasiswa</th>
                    <th>Status Beban</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;

                  $qDosen = mysqli_query($conn, "SELECT d.nip, d.nama_dosen, d.aktif, COUNT(m.nim) AS jumlah_mhs FROM tbl_dosen d
                            LEFT JOIN tbl_mahasiswa m ON m.dosen_pembimbing = d.nip
                            AND m.aktif = 1
                            AND m.id_status != 6
                            GROUP BY d.nip, d.nama_dosen, d.aktif
                            ORDER BY d.nama_dosen ASC
                  ");

                  if (!$qDosen) {
                    echo '<tr>';
                    echo '<td colspan="6" class="text-danger text-center">';
                    echo 'Gagal memuat data dosen<br><small>' . mysqli_error($conn) . '</small>';
                    echo '</td>';
                    echo '</tr>';
                  } else {

                    while ($row = mysqli_fetch_assoc($qDosen)) {

                      $jml = (int)$row['jumlah_mhs'];

                      if ($jml > 8) {
                        $badge = 'danger';
                        $label = 'Overload';
                        $rowClass = 'table-danger';
                      } elseif ($jml >= 6) {
                        $badge = 'warning';
                        $label = 'Padat';
                        $rowClass = '';
                      } else {
                        $badge = 'success';
                        $label = 'Normal';
                        $rowClass = '';
                      }
                  ?>
                      <tr class="<?= $row['aktif'] == 0 ? 'row-nonaktif' : '' ?>">
                        <td class="text-center"><?= $no++ ?></td>
                        <td><b><?= htmlspecialchars($row['nip']) ?></b></td>
                        <td>
                          <?= htmlspecialchars($row['nama_dosen']) ?>
                          <?php if ($row['aktif'] == 0): ?>
                            <small class="text-muted">(nonaktif)</small>
                          <?php endif; ?>
                        </td>
                        <td class="text-center"><?= $jml ?></td>
                        <td class="text-center">
                          <span class="badge badge-<?= $badge ?>"><?= $label ?></span>
                        </td>
                        <td class="text-center">

                          <!-- DETAIL -->
                          <a href="detaildosen.php?nip=<?= encriptData($row['nip']); ?>"
                            class="btn btn-sm btn-secondary"
                            title="Detail & Timeline">
                            <i class="fas fa-eye"></i>
                          </a>

                          <!-- DETAIL MAHASISWA BIMBINGAN -->
                          <a href="detail_bimbingan.php?nip=<?= encriptData($row['nip']); ?>"
                            class="btn btn-sm btn-info"
                            title="Detail Mahasiswa Bimbingan">
                            <i class="fas fa-users"></i>
                          </a>

                          <!-- EDIT -->
                          <a href="editdosen.php?nip=<?= encriptData($row['nip']); ?>"
                            class="btn btn-sm btn-warning"
                            title="Edit Data">
                            <i class="fas fa-edit"></i>
                          </a>

                          <!-- NONAKTIFKAN -->
                          <?php if ($row['aktif'] == 1): ?>
                            <a href="proses.php?action=toggle&nip=<?= encriptData($row['nip']); ?>"
                              class="btn btn-sm btn-danger"
                              onclick="return confirm('Nonaktifkan dosen ini?')"
                              title="Nonaktifkan">
                              <i class="fas fa-user-slash"></i>
                            </a>
                          <?php else: ?>
                            <!-- AKTIFKAN -->
                            <a href="proses.php?action=toggle&nip=<?= encriptData($row['nip']); ?>"
                              class="btn btn-sm btn-success"
                              onclick="return confirm('Aktifkan kembali dosen ini?')"
                              title="Aktifkan">
                              <i class="fas fa-user-check"></i>
                            </a>
                          <?php endif; ?>

                          <!-- HAPUS -->
                          <a href="proses.php?action=hapus&nip=<?= encriptData($row['nip']); ?>"
                            class="btn btn-sm btn-dark"
                            onclick="return confirm('Hapus permanen data dosen?')"
                            title="Hapus">
                            <i class="fas fa-trash"></i>
                          </a>

                        </td>
                      </tr>
                  <?php
                    }
                  }
                  ?>
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
                <h5 class="modal-title" id="importModalLabel">Import Data Dosen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <p>Pilih file Excel (.xlsx / .xls) yang akan diimport.</p>
                <input type="file" name="file_excel" accept=".xlsx,.xls" required>
              </div>
              <div class="modal-footer">
                <button type="submit" name="import" class="btn btn-primary">Import File</button>
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
  <?php if (isset($_SESSION['alert_success'])): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        html: '<?= $_SESSION['alert_success']; ?>'
      });
    </script>
  <?php unset($_SESSION['alert_success']);
  endif; ?>

  <?php if (isset($_SESSION['alert_warning'])): ?>
    <script>
      Swal.fire({
        icon: 'warning',
        title: 'Perhatian',
        html: '<?= $_SESSION['alert_warning']; ?>'
      });
    </script>
  <?php unset($_SESSION['alert_warning']);
  endif; ?>

</body>

</html>