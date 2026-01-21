<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Ambil data session dengan aman
$nama_user = $_SESSION['username'] ?? 'User';
$role      = $_SESSION['role'] ?? '';

// Mapping halaman ganti password
$gantiPasswordLink = [
  1           => '../admin_gantipw',
  2           => '../dosen_gantipw',
  3           => '../mhs_gantipw',
  'admin'     => '../admin_gantipw',
  'dosen'     => '../dosen_gantipw',
  'mahasiswa' => '../mhs_gantipw'
];

// Tentukan link ganti password
$linkGantiPw = $gantiPasswordLink[$role] ?? null;
?>

<?php include '../mhs_listlink.php'; ?>

<nav class="main-header navbar navbar-expand navbar-dark custom-navbar">
  <!-- Left navbar -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button">
        <i class="fas fa-bars"></i>
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="#" id="themeToggle" title="Dark / Light Mode">
        <i class="fas fa-moon"></i>
      </a>
    </li>
  </ul>

  <!-- Right navbar -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
        <?= htmlspecialchars($nama_user); ?>
        <i class="fas fa-user-circle"></i>
      </a>

      <div class="dropdown-menu dropdown-menu-right">
        <?php if ($linkGantiPw): ?>
          <a href="<?= $linkGantiPw; ?>" class="dropdown-item">
            <i class="fas fa-lock mr-2"></i> Ganti Password
          </a>
          <div class="dropdown-divider"></div>
        <?php endif; ?>

        <a href="../login/logout.php" class="dropdown-item text-danger">
          <i class="fas fa-sign-out-alt mr-2"></i> Keluar
        </a>
      </div>
    </li>
  </ul>
</nav>