<ul class="navbar-nav">
  <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
  </li>
</ul>

<!-- Right navbar links -->
<ul class="navbar-nav ml-auto">
  <!-- Notifications Dropdown Menu -->
  <li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
      Selamat Datang, <?= $_SESSION['nama_user'];?>  <i class="fas fa-user"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
      <div class="dropdown-divider"></div>
      <a href="<?php if($_SESSION['status']==0){echo '../admin_gantipw';} if($_SESSION['status']==1){echo '../dosen_gantipw';} if($_SESSION['status']==2){echo '../mhs_gantipw';}?>" class="dropdown-item">
        <i class="fas fa-lock mr-2"></i> Ganti Password
      </a>
      <div class="dropdown-divider"></div>
      <a href="../login/logout.php" class="dropdown-item">
        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
      </a>
    </div>
  </li>
</ul>