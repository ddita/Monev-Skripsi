<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
<!-- Add icons to the links using the .nav-icon class
  with font-awesome or any other icon font library -->
  <li class="nav-item">
    <a href="../mhs_dashboard" class="nav-link <?php if($konstruktor=="mhs_dashboard"){echo 'active';}?>">
      <i class="fas fa-home nav-icon"></i>
      <p>Dashboard</p>
    </a>
  </li>
  <li class="nav-item">
    <a href="../mhs_kelas_bimbingan" class="nav-link <?php if($konstruktor=='mhs_kelas_bimbingan'){echo 'active';}?>">
      <i class="fas fa-chalkboard-teacher nav-icon"></i>
      <p>Kelas Bimbingan</p>
    </a>
  </li>
  <li class="nav-item">
    <a href="../mhs_gantipw" class="nav-link <?php if($konstruktor=="mhs_gantipw"){echo 'active';}?>">
      <i class="fas fa-lock nav-icon"></i>
      <p>Ganti Password</p>
    </a>
  </li>
  <li class="nav-item">
    <a href="../login/logout.php" class="nav-link">
      <i class="fas fa-sign-out-alt nav-icon"></i>
      <p>Keluar</p>
    </a>
  </li>
</ul>