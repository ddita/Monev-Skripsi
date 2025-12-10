<?php
session_start();
$konstruktor = 'admin_gantipw';
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
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Dashboard Administrator</a></li>
                  <li class="breadcrumb-item active">Tambah Data Dosen</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
          </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->

        <section class="content">
          <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
              <div class="col-lg-6">
                <!-- general form elements -->
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title"><i class="nav-icon fas fa-plus"></i> Tambah Data Dosen</h3>
                  </div>
                  <!-- /.card-header -->
                  <!-- form start -->
                  <form action="proses.php" method="post">
                    <div class="card-body">
                      <a href="../admin_master_dosen" class="btn btn-warning btn-sm"><i class="nav-icon fas fa-chevron-left"></i> Kembali</a>
                      <div class="form-group">
                        <label for="nidn">NIDN</label>
                        <input type="text" class="form-control" id="nidn" name="nidn" maxlength="50" onkeypress="return IsNumeric(event);" placeholder="Input NIDN Dosen" autofocus required>
                      </div>
                      <div class="form-group">
                        <label for="nama">Nama Dosen (dengan gelar)</label>
                        <input type="text" class="form-control" id="nama" name="nama" maxlength="150" placeholder="Input Nama Dosen(dengan gelar)" required>
                      </div>
                      <div class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" maxlength="50" placeholder="Input Email Dosen" required>
                      </div>
                      <div class="form-group">
                        <label for="kontak">Kontak</label>
                        <input type="text" class="form-control" id="kontak" name="kontak" maxlength="15" placeholder="Input Kontak Dosen" onkeypress="return IsNumeric(event);" required>
                      </div>
                      <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" class="form-control">
                          <option value="1">Aktif</option>
                          <option value="2">Nonaktif</option>
                        </select>
                      </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                      <button type="submit" class="btn btn-primary btn-block" name="tambahdosen"><i class="nav-icon fas fa-plus"></i> Tambah Data Dosen</button>
                    </div>
                  </form>
                </div>
                <!-- /.card -->
              </div>
            </div>
            <!-- /.row (main row) -->
          </div><!-- /.container-fluid -->
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
