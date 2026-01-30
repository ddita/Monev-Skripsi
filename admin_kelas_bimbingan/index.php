<?php
session_start();
$konstruktor = 'admin_kelas_bimbingan';
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

/* ================= AUTO SELESAIKAN BIMBINGAN JIKA SKRIPSI LULUS ================= */
mysqli_query($conn, "UPDATE tbl_kelas_bimbingan kb
  JOIN tbl_skripsi s ON kb.id_skripsi = s.id_skripsi
  SET kb.status_bimbingan = 'selesai'
  WHERE s.id_status = 6
  AND kb.status_bimbingan != 'selesai'
");
// ================= RINGKASAN =================
$qKelas = mysqli_query($conn, "SELECT kb.id_kelas, kb.status_bimbingan, kb.created_at, m.nim, m.nama AS nama_mhs, d.nama_dosen AS nama_dosen, s.judul,
    s.id_status AS status_skripsi, p.nama_periode AS tahun_akademik FROM tbl_kelas_bimbingan kb
    JOIN tbl_mahasiswa m ON kb.nim = m.nim
    JOIN tbl_dosen d ON kb.nip = d.nip
    JOIN tbl_skripsi s ON kb.id_skripsi = s.id_skripsi
    JOIN tbl_periode p ON kb.id_periode = p.id_periode
    ORDER BY kb.created_at DESC
");
$total_kelas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM tbl_kelas_bimbingan"))['total'] ?? 0;
$kelas_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM tbl_kelas_bimbingan WHERE status_bimbingan='aktif'"))['total'] ?? 0;
$kelas_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM tbl_kelas_bimbingan WHERE status_bimbingan='selesai'"))['total'] ?? 0;
$kelas_dibatalkan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) total FROM tbl_kelas_bimbingan WHERE status_bimbingan='dibatalkan'"))['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monev Skripsi | Kelas Bimbingan</title>
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
              <h1 class="m-0">
                <i class="fas fa-users text-primary"></i>
                Kelas Bimbingan
              </h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item active">Kelas Bimbingan</li>
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
                  <h3><?= $total_kelas ?></h3>
                  <p>Total Kelas Bimbingan</p>
                </div>
                <div class="icon">
                  <i class="fas fa-user-tie"></i>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="small-box bg-primary">
                <div class="inner">
                  <h3><?= $kelas_aktif ?></h3>
                  <p>Kelas Aktif</p>
                </div>
                <div class="icon">
                  <i class="fas fa-chalkboard-teacher"></i>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?= $kelas_selesai ?></h3>
                  <p>Kelas Selesai</p>
                </div>
                <div class="icon">
                  <i class="fas fa-check"></i>
                </div>
              </div>
            </div>

            <div class="col-lg-3 col-md-6">
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><?= $kelas_dibatalkan ?></h3>
                  <p>Kelas Dibatalkan</p>
                </div>
                <div class="icon">
                  <i class="fas fa-exclamation-triangle"></i>
                </div>
              </div>
            </div>

          </div>

          <!-- TABEL KELAS BIMBINGAN -->
          <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-list"></i> Daftar Dosen
              </h3>
              <div class="card-tools">
                <a href="tambah.php" class="btn btn-primary btn-sm">
                  <i class="fas fa-plus"></i> Tambah
                </a>
              </div>
            </div>

            <div class="card-body table-responsive">
              <table id="example1" class="table table-bordered table-striped table-hover">
                <thead class="text-center">
                  <tr>
                    <th>No</th>
                    <th>Mahasiswa</th>
                    <th>Dosen Pembimbing</th>
                    <th>Judul Skripsi</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th width="12%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $no = 1;
                  while ($row = mysqli_fetch_assoc($qKelas)) {

                    switch ($row['status_bimbingan']) {
                      case 'aktif':
                        $badge = 'success';
                        $label = 'Aktif';
                        break;
                      case 'selesai':
                        $badge = 'primary';
                        $label = 'Selesai';
                        break;
                      case 'dibatalkan':
                        $badge = 'danger';
                        $label = 'Dibatalkan';
                        break;
                      default:
                        $badge = 'secondary';
                        $label = 'Unknown';
                    }
                  ?>
                    <tr>
                      <td class="text-center"><?= $no++ ?></td>

                      <td>
                        <b><?= htmlspecialchars($row['nim']) ?></b><br>
                        <small><?= htmlspecialchars($row['nama_mhs']) ?></small>
                      </td>

                      <td><?= htmlspecialchars($row['nama_dosen']) ?></td>

                      <td><?= htmlspecialchars($row['judul']) ?></td>

                      <td class="text-center"><?= htmlspecialchars($row['tahun_akademik']) ?></td>

                      <td class="text-center">
                        <span class="badge badge-<?= $badge ?>">
                          <?= $label ?>
                        </span>
                      </td>

                      <td class="text-center">

                        <!-- DETAIL KELAS -->
                        <a href="detail_kelas.php?id_kelas=<?= encriptData($row['id_kelas']) ?>"
                          class="btn btn-sm btn-secondary"
                          title="Detail Kelas">
                          <i class="fas fa-eye"></i>
                        </a>

                        <!-- HAPUS -->
                        <a href="proses.php?action=hapus&id=<?= encriptData($row['id_kelas']) ?>"
                          onclick="return confirm('Hapus kelas bimbingan ini?')"
                          class="btn btn-sm btn-danger"
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
      <!-- Modal Edit -->
      <div class="modal fade" id="modalEditStatus" tabindex="-1">
        <div class="modal-dialog">
          <form method="POST" action="proses.php">
            <div class="modal-content">

              <div class="modal-header bg-warning">
                <h5 class="modal-title">
                  <i class="fas fa-edit"></i> Ubah Status Bimbingan
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>

              <div class="modal-body">

                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id_kelas" id="id_kelas">

                <div class="form-group">
                  <label>NIM</label>
                  <input type="text" class="form-control" id="nim" readonly>
                </div>

                <div class="form-group">
                  <label>Nama Mahasiswa</label>
                  <input type="text" class="form-control" id="nama_mhs" readonly>
                </div>

                <div class="form-group">
                  <label>Status Bimbingan</label>
                  <select name="status_bimbingan" id="status_bimbingan" class="form-control" required>
                    <option value="aktif">Aktif</option>
                    <option value="selesai">Selesai</option>
                    <option value="dibatalkan">Dibatalkan</option>
                  </select>
                </div>

              </div>

              <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save"></i> Simpan
                </button>
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
  <script>
    $('.btn-edit-status').on('click', function() {

      const statusSkripsi = $(this).data('status-skripsi');

      $('#id_kelas').val($(this).data('id'));
      $('#nim').val($(this).data('nim'));
      $('#nama_mhs').val($(this).data('nama'));
      $('#status_bimbingan').val($(this).data('status'));

      if (parseInt(statusSkripsi) === 6) {
        $('#status_bimbingan').val('selesai');
        $('#status_bimbingan option[value="aktif"]').prop('disabled', true);
      } else {
        $('#status_bimbingan option[value="aktif"]').prop('disabled', false);
      }
    });
  </script>
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