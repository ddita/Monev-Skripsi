<?php
session_start();
$konstruktor = 'admin_konfigurasi';
require_once '../database/config.php';
if ($_SESSION['status']!=0) {
  $usr = $_SESSION['username'];
  $waktu = date('Y-m-d H:i:s');
  $auth = $_SESSION['status'];
  $nama = $_SESSION['nama_user'];
  if ($auth==1) {
    $tersangka = "Dosen";
  }
  if ($auth==2) {
    $tersangka = "Mahasiswa";
  }
  
  $ket = "Pengguna dengan username ".$usr.", nama : ".$nama." melakukan cross authority dengan akses sebagai ".$tersangka;
  $querycrossauth = mysqli_query($conn, "INSERT INTO tbl_cross_auth VALUES ('', '$usr', '$waktu', '$ket')") or die(mysqli_error($conn));
  echo '<script>window.location="../login/logout.php"</script>';

}
else {
  ?>
  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monev Skripsi | Administrator</title>
    <?php
    include '../mhs_listlink.php';
    ?>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

      <!-- Preloader -->
      <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="../images/UP.png" alt="Monev-Skripsi" height="60" width="60">
      </div>

      <!-- Navbar -->
      <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <?php
        include '../mhs_navbar.php';
        ?>
      </nav>
      <!-- /.navbar -->

      <!-- Main Sidebar Container -->
      <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="index3.html" class="brand-link">
          <img src="../images/profile.png" alt="Monev-Skripsi" class="brand-image img-circle elevation-3" style="opacity: .8">
          <span class="brand-text font-weight-light">Monev Skripsi</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <?php
            include '../admin_sidebar.php';
            ?>
          </nav>
          <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0">Dashboard Administrator</h1>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Konfigurasi Sistem</li>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <!-- /.content-header -->
        <?php
        //memanggil semua kolom pada tabel konfigurasi
        $pgllogoapp = mysqli_query($conn, "SELECT * FROM tbl_konfigurasi WHERE id=1") or die(mysqli_error($conn));
        $pgllogotitle = mysqli_query($conn, "SELECT * FROM tbl_konfigurasi WHERE id=2") or die(mysqli_error($conn));
        //menampung array dari query
        $arrapp = mysqli_fetch_array($pgllogoapp);
        $arrtitle = mysqli_fetch_array($pgllogotitle);
        //
        $logoapp = $arrapp['lokasi_file'];
        $logotitle = $arrtitle['lokasi_file'];
        ?>

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-lg-12">
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title"><i class="nav-icon fas fa-cog"></i> Konfigurasi Sistem</h3>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="card-body">
                          <div class="card card-primary">
                            <div class="card-header">
                              <h3 class="card-title"><i class="nav-icon fas fa-image"></i> Logo Aplikasi</h3>
                            </div>
                            <div class="card-body">
                              <form action="updatelogoapp.php" method="post" enctype="multipart/form-data">
                                <center>
                                  <img src="<?=$logoapp;?>" height="100px" width="100px">
                                </br>
                              </br>
                              <input type="file" name="logoapp" class="form-control" accept="image/*" required>
                            </center>
                            Direkomendasikan menggunakan file(xxx.png)
                          </br>
                        </br>
                        <button type="submit" class="btn btn-primary btn-sm btn-block" name="uplogoapp"><i class="nav-icon fas fa-upload"></i> Update Logo App</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="card-body">
                  <div class="card card-primary">
                    <div class="card-header">
                      <h3 class="card-title"><i class="nav-icon fas fa-image"></i> Logo Title Bar</h3>
                    </div>
                    <div class="card-body">
                      <form action="updatelogotitle.php" method="post" enctype="multipart/form-data">
                        <center>
                          <img src="<?=$logotitle;?>" height="100px" width="100px">
                          </br>
                          </br>
                          <input type="file" name="logotitle" class="form-control" accept="image/*" required>
                        </center>
                        </br>
                        </br>
                        <button type="submit" class="btn btn-danger btn-sm btn-block" name="updtitle"><i class="nav-icon fas fa-upload"></i> Update Logo Title Bar</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-9">
                <div class="card car"></div>
                <label>Nama Aplikasi</label>
                <input type="text" name="nama-app" class="form-control" maxlength="25" required>
              </div>
              <div class="col-lg-3">
              </br>
              </br>
                <button class="btn-primary">Nama Aplikasi</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php
include '../footer.php';
?>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<?php
include '../mhs_script.php';
}
?>

</body>
</html>
