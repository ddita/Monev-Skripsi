<?php
session_start();
$konstruktor = 'admin_kelas_bimbingan';
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
                <h1 class="m-0">Dashboard Kelas Bimbingan</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Kelas Bimbingan</li>
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
              <div class="col-lg-12">
                <div class="card card-primary">
                  <div class="card-header">
                    <h5><i class="fas fa-chalkboard-teacher nav-icon"></i> Kelas Bimbingan</h5>
                  </div>
                  <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped table-sm">
                      <thead>
                        <tr>
                          <th width="10%">No</th>
                          <th>NIDN</th>
                          <th>Jumlah Mahasiswa</th>
                          <th>Progres</th>
                          <th>Status</th>
                          <th><center>Aksi</center></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no=1;
                        $qrkelasbimbingan = mysqli_query($conn, "SELECT * FROM tbl_kelas_bimbingan") or die(mysqli_error($conn));
                        $rvkelasbimbingan = mysqli_num_rows($qrkelasbimbingan);

                        if($rvkelasbimbingan>0) {
                          while ($datakelasbimbingan = mysqli_fetch_array($qrkelasbimbingan)) {
                            ?>
                            <tr>
                              <td><?=$no++?></td>
                              <td><?=$datakelasbimbingan['nidn']?></td>
                              <td>
                                <?php
                                $idkelasbimbingan = $datakelasbimbingan['id_kelas'];
                                $qrjumlah = mysqli_query($conn, "SELECT id_kelas FROM tbl_mhs_bimbingan WHERE id_kelas = '$idkelasbimbingan'") or die(mysqli_error($conn));
                                $jumlah = mysqli_num_rows($qrjumlah);
                                echo $jumlah;
                                ?>
                              </td>
                              <td>
                                <?php
                                //menampung nilai id_kelas dari tbl_kelas_bimbingan
                                $id_kelas = $datakelasbimbingan['id_kelas'];
                                //memanggil semua data dari tbl_mhs_bimbingan berdasarkan id_kelas
                                $qrprogres = mysqli_query($conn, "SELECT * FROM tbl_mhs_bimbingan WHERE id_kelas = '$id_kelas'") or die(mysqli_error($conn));
                                //cek jumlah data
                                $cekqrprogres = mysqli_num_rows($qrprogres);
                                $rata_rata_presentase = 0;

                                if($cekqrprogres>0) {
                                  //deklarasi variabel nilai total_presentasi 
                                  $total_presentase = 0;
                                  while($dataprogres = mysqli_fetch_array($qrprogres)) {
                                    $id_progres = $dataprogres['id_progres'];
                                    $qrpresentase = mysqli_query($conn,"SELECT presentase FROM tbl_progres WHERE id_progres = '$id_progres'") or die(mysqli_error($conn));
                                    $datapresentase = mysqli_fetch_assoc($qrpresentase);
                                    $presentase = $datapresentase['presentase'];
                                    $total_presentase += $presentase;
                                  }
                                  $rata_rata_presentase = $total_presentase/$cekqrprogres;
                                }
                                $rata_rata_presentase = number_format($rata_rata_presentase,2);
                                echo $rata_rata_presentase.'%';
                                ?>
                              </td>
                              <td>Status</td>
                              <td>
                                <center>
                                  <a href="detail.php?id_kelas=<?=$datakelasbimbingan['id_kelas'];?>" class="btn btn-sm btn-info"> <i class="nav-icon fas fa-edit"></i> Detail</a>
                                </center>
                              </td>
                            </tr>
                            <?php
                          }
                        }
                        ?>
                      </tbody>
                    </table>
                  </div>
                </div>
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
