<?php
// ambil koneksi
require_once '../database/config.php';

// ambil username dari session
$username = $_SESSION['username'] ?? '';

// default fallback
$namaLengkap = 'Administrator';

// query nama lengkap
if ($username !== '') {
  $qUser = mysqli_query($conn, "
    SELECT nama_lengkap 
    FROM tbl_users 
    WHERE username = '$username'
    LIMIT 1
  ");

  if ($qUser && mysqli_num_rows($qUser) > 0) {
    $dataUser = mysqli_fetch_assoc($qUser);
    $namaLengkap = $dataUser['nama_lengkap'];
  }
}

// USER MANAJEMEN
$userManajemenPages = [
  'admin_mahasiswa',
  'admin_dosen'
];

// AKADEMIK
$akademikPages = [
  'admin_akademik',
  'admin_periode',
  'admin_prodi',
  'admin_angkatan',
  'admin_konsentrasi',
  'admin_status',
  'admin_panduan'
];

$isUserManajemenOpen = in_array($konstruktor, $userManajemenPages);
$isAkademikOpen      = in_array($konstruktor, $akademikPages);

/*
 | Pastikan hanya satu menu parent yang aktif
 */
if ($isUserManajemenOpen) {
  $isAkademikOpen = false;
} elseif ($isAkademikOpen) {
  $isUserManajemenOpen = false;
}

?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand -->
  <a href="../admin_dashboard" class="brand-link">
    <img src="../images/profile.png" class="brand-image img-circle elevation-3">
    <span class="brand-text font-weight-light">
      <?= htmlspecialchars($namaLengkap); ?>
    </span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column"
        data-widget="treeview"
        data-accordion="true">

        <!-- DASHBOARD -->
        <li class="nav-item">
          <a href="../admin_dashboard"
            class="nav-link <?= $konstruktor == 'admin_dashboard' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- MANAJEMEN PENGGUNA -->
        <li class="nav-item <?= $isUserManajemenOpen ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?= $isUserManajemenOpen ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-users-cog"></i>
            <p>
              User Manajemen
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">

            <!-- MAHASISWA -->
            <li class="nav-item">
              <a href="../admin_mahasiswa"
                class="nav-link <?= $konstruktor == 'admin_mahasiswa' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Mahasiswa</p>
              </a>
            </li>

            <!-- DOSEN -->
            <li class="nav-item">
              <a href="../admin_dosen"
                class="nav-link <?= $konstruktor == 'admin_dosen' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Dosen</p>
              </a>
            </li>

          </ul>
        </li>

        <!-- MANAJEMEN AKADEMIK -->
        <li class="nav-item <?= $isAkademikOpen ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?= $isAkademikOpen ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-university"></i>
            <p>
              Manajemen Akademik
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">

            <!-- TAHUN AKADEMIK -->
            <li class="nav-item">
              <a href="../admin_akademik"
                class="nav-link <?= $konstruktor == 'admin_akademik' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Tahun Akademik</p>
              </a>
            </li>

            <!-- PERIODE -->
            <li class="nav-item">
              <a href="../admin_periode"
                class="nav-link <?= $konstruktor == 'admin_periode' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Periode</p>
              </a>
            </li>

            <!-- PRODI -->
            <li class="nav-item">
              <a href="../admin_prodi" class="nav-link <?= $konstruktor == 'admin_prodi' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Program Studi</p>
              </a>
            </li>

            <!-- ANGKATAN -->
            <li class="nav-item">
              <a href="../admin_angkatan" class="nav-link <?= $konstruktor == 'admin_angkatan' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Tahun Angkatan</p>
              </a>
            </li>

            <!-- KONSETNRASI BIDANG SKRIPSI -->
            <li class="nav-item">
              <a href="../admin_konsentrasi"
                class="nav-link <?= $konstruktor == 'admin_konsentrasi' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Bidang Skripsi</p>
              </a>
            </li>

            <!-- STATUS SKRIPSI -->
            <li class="nav-item">
              <a href="../admin_status"
                class="nav-link <?= $konstruktor == 'admin_status' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Status Skripsi</p>
              </a>
            </li>

            <!-- BUKU PANDUAN SKRIPSI -->
            <li class="nav-item">
              <a href="../admin_panduan"
                class="nav-link <?= $konstruktor == 'admin_panduan' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Dokumen & Template</p>
              </a>
            </li>

          </ul>
        </li>

        <!-- KELAS BIMBINGAN -->
        <li class="nav-item">
          <a href="../admin_kelas_bimbingan"
            class="nav-link <?= $konstruktor == 'admin_kelas_bimbingan' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-chalkboard-teacher"></i>
            <p>Kelas Bimbingan</p>
          </a>
        </li>

        <!-- KONFIGURASI -->
        <li class="nav-item">
          <a href="../admin_konfigurasi"
            class="nav-link <?= $konstruktor == 'admin_konfigurasi' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-cog"></i>
            <p>Konfigurasi Sistem</p>
          </a>
        </li>

        <!-- GANTI PASSWORD -->
        <li class="nav-item">
          <a href="../admin_gantipw"
            class="nav-link <?= $konstruktor == 'admin_gantipw' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-lock"></i>
            <p>Ganti Password</p>
          </a>
        </li>

        <!-- LOGOUT -->
        <li class="nav-item">
          <a href="../login/logout.php" class="nav-link text-danger">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Keluar</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>