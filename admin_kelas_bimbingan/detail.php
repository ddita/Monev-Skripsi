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
                  <li class="breadcrumb-item active">Admin Kelas Bimbingan</li>
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
                    <h5><i class="fas fa-chalkboard-teacher nav-icon"></i> Detail Kelas Bimbingan</h5>
                  </div>
                  <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped table-sm">
                      <thead>
                        <tr>
                          <th width="5%">No</th>
                          <th>NIM-Mahasiswa</th>
                          <th>Progres</th>
                          <th>Kontak</th>
                          <th><center>Aksi</center></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        //membuat variabel nomor dengan nilai 1
                        $no = 1;
                        //menampung nilai id_kelas dengan get
                        $id_kelas = @$_GET['id_kelas'];
                        //mengambil semua data dari tbl_mhs_bimbingan berdasarkan id_kelas
                        $qrmhskelas = mysqli_query($conn, "SELECT * FROM tbl_mhs_bimbingan WHERE id_kelas = '$id_kelas'") or die(mysqli_error($conn));
                        //mengambil jumlah panggil data menggunakan mysqli_num_rows
                        $qrcek = mysqli_num_rows($qrmhskelas);

                        if($qrcek>0){
                          while ($data_mhs_kelas = mysqli_fetch_array($qrmhskelas)) {
                            ?>
                            <tr>
                              <td><?=$no++?></td>
                              <td>
                                <?php
                                //menampung nim dari tbl_mhs_bimbingan
                                $nim = $data_mhs_kelas['nim'];
                                //mengambil data mahasiswa nama dan kontak dari tbl_mahasiswa berdasarkan nim
                                $qrmhs = mysqli_query($conn, "SELECT nama, kontak FROM tbl_mahasiswa WHERE nim='$nim'") or die(mysqli_error($conn));
                                //mengambil jumlah dari baris data
                                $qrcekmhs = mysqli_num_rows($qrmhs);
                                if($qrcekmhs==1) {
                                  $datamhs = mysqli_fetch_assoc($qrmhs);
                                  //menampung nilai nama data mahasiwa
                                  $nama = $datamhs['nama'];
                                  //menampung nilai kontak data mahasiwa
                                  $kontak = $datamhs['kontak'];
                                } else {
                                  $nama = "Datanya Kosong";
                                  $kontak = "Datanya Kosong";
                                }
                                
                                ?>
                                <!-- menampilkan nilai nim dan nama  -->
                                <?=$nim;?> - <?=$nama;?>
                              </td>
                              <td>
                                <?php
                                //menampung nilai id_progres dari tbl_mhs_bimbingan
                                $id_progres = $data_mhs_kelas['id_progres'];
                                //mengambil semua data dari tbl_progres berdasarkan id_progres
                                $qrket_progres = mysqli_query($conn, "SELECT * FROM tbl_progres WHERE id_progres = $id_progres") or die(mysqli_error($conn));
                                //menampung jumlah dari baris data
                                $cekket_progres = mysqli_num_rows($qrket_progres);
                                if($cekket_progres>0) {
                                  //menampung data array dari kolom tabel di database
                                  $dataket_progres = mysqli_fetch_assoc($qrket_progres);
                                  //tampung nilai dari kolom keterangan pada tabel
                                  $ket_progres = $dataket_progres['keterangan'];
                                } else {
                                  $ket_progres = 'ERROR';
                                }
                                ?>
                                <!-- menampilak keterangan progres -->
                                <?=$ket_progres;?>
                              </td>
                              <td><?=$kontak?></td>
                              <td>
                                <center>
                                  <a href="detail.php?id_kelas=<?=$id_kelas;?>" class="btn btn-sm btn-info">
                                    <i class="nav-icon fas fa-edit"></i> Detail</a>
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
