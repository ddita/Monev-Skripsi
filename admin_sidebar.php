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

// daftar halaman manajemen pengguna
$manajemenPages = [
  'admin_mahasiswa',
  'admin_dosen',
  'admin_administrator'
];

$isManajemenOpen = in_array($konstruktor, $manajemenPages);
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
        role="menu"
        data-accordion="false">

        <!-- DASHBOARD -->
        <li class="nav-item">
          <a href="../admin_dashboard"
            class="nav-link <?= $konstruktor == 'admin_dashboard' ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-home"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <!-- MANAJEMEN PENGGUNA -->
        <li class="nav-item <?= $isManajemenOpen ? 'menu-open' : ''; ?>">
          <a href="#" class="nav-link <?= $isManajemenOpen ? 'active' : ''; ?>">
            <i class="nav-icon fas fa-users-cog"></i>
            <p>
              User Manajemen
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>

          <ul class="nav nav-treeview">

            <!-- MANAJEMEN MAHASISWA -->
            <li class="nav-item">
              <a href="../admin_mahasiswa"
                class="nav-link <?= $konstruktor == 'admin_mahasiswa' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Manajemen Mahasiswa</p>
              </a>
            </li>

            <!-- MANAJEMEN DOSEN -->
            <li class="nav-item">
              <a href="../admin_dosen"
                class="nav-link <?= $konstruktor == 'admin_dosen' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Manajemen Dosen</p>
              </a>
            </li>

            <!-- ADMINISTRATOR -->
            <li class="nav-item">
              <a href="../admin_administrator"
                class="nav-link <?= $konstruktor == 'admin_administrator' ? 'active' : ''; ?>">
                <i class="far fa-circle nav-icon"></i>
                <p>Administrator</p>
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