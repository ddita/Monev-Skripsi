<?php
session_start();
$konstruktor = 'admin_master_status';
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
                <h1 class="m-0">Master Data Status Mahasiswa</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Admin Dashboard</a></li>
                  <li class="breadcrumb-item active">Master Data Status Mahasiswa</li>
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
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title"><i class="nav-icon fas fa-user-graduate"></i> Status Mahasiswa</h3>
                  </div>
                  <div class="card-body">
                    <a href="tambahsts.php" class="btn btn-primary btn-sm"><i class="nav-icon fas fa-download"></i> Tambah Data</a>
                    <a href="resetdata.php?reset=reset_data" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin akan mereset data ini?')"><i class="nav-icon fas fa-sync"></i> Reset Data</a>
                    <br>
                    <br>
                    <table id="example1" class="table table-bordered table-striped table-sm">
                      <thead>
                        <tr>
                          <th width="5%">No</th>
                          <th>ID</th>
                          <th>Status</th>
                          <th><center>Aksi</center></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no=1;
                        $querysts = mysqli_query($conn, "SELECT * FROM tbl_status") or die(mysqli_error($conn));
                        if (mysqli_num_rows($querysts)>0){
                          while ($dt_status = mysqli_fetch_array($querysts)){
                        ?>
                        <tr>
                          <td><?=$no++;?></td>
                          <td><?=$dt_status['id'];?></td>
                          <td><?=$dt_status['status'];?></td>
                          <td>
                            <center>
                              <a href="proseshapus.php?kd_sts=<?=encriptData($dt_status['id'])?>&hapus=hapus" class="btn btn-sm btn-danger" onclick="return confirm('Anda akan menghapus data status mahasiswa dengan ID [<?=$dt_status['id'];?>] - Status : [<?=$dt_status['status'];?>]')">
                                <i class="nav-icon fas fa-trash"></i>
                              </a>
                            </center>
                          </td>
                        </tr>
                        <?php
                      }
                    } else{
                      ?>
                      <tr>
                        <td colspan="8"><center>Tidak ditemukan data periode pada database</center></td>
                      </tr>
                      <?php
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- /.row (main row) -->
      </div>
      <!-- /.container-fluid -->
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