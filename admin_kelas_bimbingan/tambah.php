<?php
session_start();
$konstruktor = 'admin_kelas_bimbingan';
require_once '../database/config.php';

/* ================= CEK LOGIN ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../login/logout.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Monev Skripsi | Tambah Kelas Bimbingan</title>
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
    <?php include '../mhs_navbar.php'; ?>
    <?php include '../admin_sidebar.php'; ?>

    <div class="content-wrapper">

      <!-- HEADER -->
      <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1><i class="fas fa-plus text-primary"></i> Tambah Kelas Bimbingan</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="../admin_kelas_bimbingan">Kelas Bimbingan</a></li>
                <li class="breadcrumb-item active">Tambah</li>
              </ol>
            </div>
          </div>
        </div>
      </section>

      <!-- CONTENT -->
      <section class="content">
        <div class="container-fluid">

          <a href="../admin_kelas_bimbingan/" class="btn btn-warning btn-sm mb-3">
            <i class="fas fa-chevron-left"></i> Kembali
          </a>

          <div class="card card-outline card-primary">
            <div class="card-header">
              <h3 class="card-title">
                <i class="fas fa-users"></i> Form Kelas Bimbingan
              </h3>
            </div>

            <form method="POST" action="proses.php">
              <input type="hidden" name="action" value="tambah">

              <div class="card-body">
                <div class="row">

                  <!-- MAHASISWA -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Mahasiswa</label>
                      <select name="nim" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Mahasiswa --</option>
                        <?php
                        $q = mysqli_query($conn, "SELECT nim, nama FROM tbl_mahasiswa ORDER BY nama ASC");
                        while ($m = mysqli_fetch_assoc($q)) {
                          echo "<option value='{$m['nim']}'>{$m['nim']} - {$m['nama']}</option>";
                        }
                        ?>
                      </select>
                    </div>
                  </div>

                  <!-- DOSEN -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Dosen Pembimbing</label>
                      <select name="nip" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Dosen --</option>
                        <?php
                        $q = mysqli_query($conn, "SELECT nip, nama_dosen FROM tbl_dosen ORDER BY nama_dosen ASC");
                        while ($d = mysqli_fetch_assoc($q)) {
                          echo "<option value='{$d['nip']}'>{$d['nama_dosen']}</option>";
                        }
                        ?>
                      </select>
                    </div>
                  </div>

                  <!-- SKRIPSI -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Judul Skripsi</label>
                      <select name="id_skripsi" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Skripsi --</option>
                        <?php
                        $q = mysqli_query($conn, "
                SELECT s.id_skripsi, s.judul, m.nama
                FROM tbl_skripsi s
                JOIN tbl_mahasiswa m ON s.nim = m.nim
                ORDER BY s.id_skripsi DESC
              ");
                        while ($s = mysqli_fetch_assoc($q)) {
                          echo "<option value='{$s['id_skripsi']}'>
                        {$s['nama']} - {$s['judul']}
                      </option>";
                        }
                        ?>
                      </select>
                    </div>
                  </div>

                  <!-- PERIODE -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Periode</label>
                      <select name="id_periode" class="form-control" required>
                        <option value="" disabled selected>-- Pilih Periode --</option>
                        <?php
                        $q = mysqli_query($conn, "SELECT id_periode, nama_periode FROM tbl_periode ORDER BY id_periode DESC");
                        while ($p = mysqli_fetch_assoc($q)) {
                          echo "<option value='{$p['id_periode']}'>{$p['nama_periode']}</option>";
                        }
                        ?>
                      </select>
                    </div>
                  </div>

                  <!-- STATUS -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Status Bimbingan</label>
                      <select name="status_bimbingan" class="form-control" required>
                        <option value="aktif" selected>Aktif</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                      </select>
                    </div>
                  </div>

                </div>
              </div>

              <div class="card-footer">
                <button type="submit" class="btn btn-primary btn-block">
                  <i class="fas fa-save"></i> Simpan Kelas Bimbingan
                </button>
              </div>

            </form>
          </div>

        </div>
      </section>
    </div>

    <?php include '../footer.php'; ?>
  </div>
  <?php include '../mhs_script.php'; ?>
</body>

</html>