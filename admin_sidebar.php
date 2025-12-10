<ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
  <li class="nav-item">
    <a href="../admin_dashboard" class="nav-link <?php if($konstruktor=='admin_dashboard'){echo 'active';}?>">
      <i class="nav-icon fas fa-home"></i>
      <p>Dashboard</p>
    </a>
  </li>
  <li class="nav-item <?php if($konstruktor=='admin_master_mahasiswa'){echo 'menu-open';} if($konstruktor=='admin_master_angkatan'){echo 'menu-open';} if($konstruktor=='admin_master_prodi'){echo 'menu-open';} if($konstruktor=='admin_master_dosen'){echo 'menu-open';} if($konstruktor=='admin_master_periode'){echo 'menu-open';} if($konstruktor=='admin_master_status'){echo 'menu-open';} if($konstruktor=='admin_kelasbimbingan'){echo 'menu-open';} ?>"> 
    <a href="#" class="nav-link <?php if($konstruktor=='admin_master_mahasiswa'){echo 'menu-open';}if($konstruktor=='admin_master_angkatan'){echo 'menu-open';}if($konstruktor=='admin_master_prodi'){echo 'menu-open';} if($konstruktor=='admin_master_dosen'){echo 'menu-open';} if($konstruktor=='admin_master_periode'){echo 'menu-open';} if($konstruktor=='admin_master_status'){echo 'menu-open';} if($konstruktor=='admin_kelas_bimbingan'){echo 'menu-open';} ?> ">
      <i class="nav-icon fas fa-database"></i>
      <p>
        Master Data
        <i class="right fas fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="../admin_master_periode" class="nav-link <?php if($konstruktor=='admin_master_periode'){echo 'active';}?>">
          <i class="far fa-circle nav-icon"></i>
          <p>Periode Akademik</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="../admin_master_mahasiswa" class="nav-link <?php if($konstruktor=='admin_master_mahasiswa'){echo 'active';}?>">
          <i class="far fa-circle nav-icon"></i>
          <p>Mahasiswa</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="../admin_master_dosen" class="nav-link <?php if($konstruktor=='admin_master_dosen'){echo 'active';}?>">
          <i class="far fa-circle nav-icon"></i>
          <p>Dosen</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="../admin_master_prodi" class="nav-link <?php if($konstruktor=='admin_master_prodi'){echo 'active';}?>">
          <i class="far fa-circle nav-icon"></i>
          <p>Program Studi</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="../admin_master_angkatan" class="nav-link <?php if($konstruktor=='admin_master_angkatan'){echo 'active';}?>">
          <i class="far fa-circle nav-icon"></i>
          <p>Angkatan</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="../admin_master_status" class="nav-link <?php if($konstruktor=='admin_master_status'){echo 'active';}?>">
          <i class="far fa-circle nav-icon"></i>
          <p>Status Mahasiswa</p>
        </a>
      </li>
    </ul>
  </li>
  <li class="nav-item">
    <a href="../admin_kelas_bimbingan" class="nav-link <?php if($konstruktor=='admin_kelas_bimbingan'){echo 'active';}?>">
      <i class="fas fa-chalkboard-teacher nav-icon"></i>
      <p>Kelas Bimbingan</p>
    </a>
  </li>
  <li class="nav-item">
    <a href="../admin_konfigurasi" class="nav-link <?php if($konstruktor=='admin_konfigurasi'){echo 'active';}?>">
      <i class="fas fa-cog nav-icon"></i>
      <p>Konfigurasi Sistem</p>
    </a>
  </li>
  <li class="nav-item">
    <a href="../admin_gantipw" class="nav-link <?php if($konstruktor=='admin_gantipw'){echo 'active';}?>">
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
</li>