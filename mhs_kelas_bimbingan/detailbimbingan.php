<?php
session_start();
$konstruktor = 'mhs_kelas_bimbingan';
require_once '../database/config.php';
if ($_SESSION['status']!=2) {
  $usr = $_SESSION['username'];
  $waktu = date('Y-m-d H:i:s');
  $auth = $_SESSION['status'];
  $nama = $_SESSION['nama_user'];
  if ($auth==0) {
    $tersangka = "Administrator";
  }
  if ($auth==1) {
    $tersangka = "Dosen";
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
    <title>Monev Skripsi | Mahasiswa</title>
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
          <img src="../images/graduate.png" alt="Monev-Skripsi" class="brand-image img-circle elevation-3" style="opacity: .8">
          <span class="brand-text font-weight-light">Monev Skripsi</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
          <!-- Sidebar Menu -->
          <nav class="mt-2">
            <?php
            include '../mhs_sidebar.php';
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
                <h1 class="m-0">Dashboard Mahasiswa</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Detail Kelas Bimbingan</li>
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
                <!-- general form elements -->
                <div class="card card-primary">
                  <div class="card-header">
                    <h3 class="card-title"><i class="nav-icon fas fa-chalkboard-teacher"></i> Detail Kelas Bimbingan</h3>
                  </div><!-- /.card-header -->
                  <div class="card-body">
                    <?php
                    $idbimbingan = @$_GET['id_kelas'];
                    $qweri = mysqli_query($conn, "SELECT nidn,kode_periode,nim FROM tbl_kelas_bimbingan WHERE id_kelas='$idbimbingan'") or die(mysqli_error($conn));
                    $arrqweri = mysqli_fetch_assoc($qweri);
                    $nidn = $arrqweri['nidn'];
                    $nim = $arrqweri['nim'];
                    $kd_periode = $arrqweri['kode_periode'];

                    $qweriprd = mysqli_query($conn, "SELECT periode FROM tbl_periode WHERE kode_periode='$kd_periode'") or die(mysqli_error($conn));
                    $arrqweriprd = mysqli_fetch_assoc($qweriprd);
                    $periode = $arrqweriprd['periode'];
                    
                    $qweridsn = mysqli_query($conn, "SELECT nama,foto FROM tbl_dosen WHERE nidn='$nidn'") or die(mysqli_error($conn));
                    $arrqweridsn = mysqli_fetch_assoc($qweridsn);
                    $nama_dosen = $arrqweridsn['nama'];
                    $foto_dosen = $arrqweridsn['foto'];

                    $qwerimhs = mysqli_query($conn, "SELECT nama,prodi,foto FROM tbl_mahasiswa WHERE nim='$nim'") or die(mysqli_error($conn));
                    $arrqwerimhs = mysqli_fetch_assoc($qwerimhs);
                    $nama_mhs = $arrqwerimhs['nama'];
                    $kode_prodi = $arrqwerimhs['prodi'];
                    $foto_mhs = $arrqwerimhs['foto'];

                    $qweriprodi = mysqli_query($conn, "SELECT nama_prodi FROM tbl_prodi WHERE kode_prodi='$kode_prodi'") or die(mysqli_error($conn));
                    $arrqweriprodi = mysqli_fetch_assoc($qweriprodi);
                    $nama_prodi = $arrqweriprodi['nama_prodi'];
                    $kode_prodi = $arrqwerimhs['prodi'];
                    ?>
                    <div class="row">
                      <div class="col-lg-6">
                        <table class="table table-sm table-borderless table-striped">
                          <tr>
                            <td width="35%">Periode Akademik</td>
                            <td width="2%">:</td>
                            <td>
                              <b><?=$kd_periode;?> - <?=$periode;?></b>
                            </td>
                          </tr>
                          <tr>
                            <td width="35%">NIDN</td>
                            <td width="2%">:</td>
                            <td>
                              <b><?=$nidn;?></b>
                            </td>
                          </tr>
                          <tr>
                            <td width="35%">Dosen Pembimbing</td>
                            <td width="2%">:</td>
                            <td>
                              <b><?=$nama_dosen;?></b>
                            </td>
                          </tr>
                        </table>
                      </div>
                      <div class="col-lg-6">
                        <table class="table table-sm table-borderless table-striped">
                          <tr>
                            <td width="35%">NIM</td>
                            <td width="2%">:</td>
                            <td>
                              <b><?=$nim;?></b>
                            </td>
                          </tr>
                          <tr>
                            <td width="35%">Nama Mahasiswa</td>
                            <td width="2%">:</td>
                            <td>
                              <b><?=$nama_mhs;?></b>
                            </td>
                          </tr>
                          <tr>
                            <td width="35%">Program Studi</td>
                            <td width="2%">:</td>
                            <td>
                              <b><?=$nama_prodi;?></b>
                            </td>
                          </tr>
                        </table>
                      </div>
                    </div>
                    <!-- /card-row -->
                    <div class="row">
                      <div class="col-lg-12">
                        <div class="card card-primary">
                          <div class="card-header">
                            <h3 class="card-title"><i class="nav-icon fas fa-copy"></i> Bimbingan Judul</h3>
                          </div>
                          <div class="card-body">
                            <center>
                              <h5><b>MONITORING SKRIPSI</b></h5>
                            </center>
                            <div class="row">
                              <div class="col-lg-8">
                                <!--Judul-->
                                <!-- DIRECT CHAT PRIMARY -->
                                <div class="card card-primary card-outline direct-chat direct-chat-primary shadow-none">
                                  <div class="card-header">
                                    <h3 class="card-title">Percakapan</h3>
                                  </div>
                                  <!-- /.card-header -->
                                  <div class="card-body">
                                    <!-- Conversations are loaded here -->
                                    <div class="direct-chat-messages">
                                      <?php
                                      $prg = @$_GET['progres'];
                                      $query_chat = mysqli_query($conn, "SELECT * FROM tbl_detail_bimbingan WHERE id_kelas_bimbingan = '$idbimbingan' AND progres = '$prg' ORDER BY id DESC") or die(mysqli_error($conn));
                                      $rvchat = mysqli_num_rows($query_chat);

                                      if($rvchat>0) {
                                        while($data_chat=mysqli_fetch_array($query_chat)){
                                          $tgl = strtotime($data_chat['tgl']);
                                          $fr_waktu = date('d F Y H:i', $tgl);
                                          if($data_chat['nim']==null) {
                                            //chat dari dosen (dari kiri)
                                            ?>
                                            <!-- Chat dari kiri -->
                                            <div class="direct-chat-msg">
                                              <div class="direct-chat-infos clearfix">
                                                <span class="direct-chat-name float-left"><?=$nama_dosen;?></span>
                                                <span class="direct-chat-timestamp float-right"><?=$fr_waktu;?></span>
                                              </div>
                                              <!-- /.direct-chat-infos -->
                                              <img class="direct-chat-img" src="<?=$foto_dosen;?>" alt="Message User Image">
                                              <!-- /.direct-chat-img -->
                                              <div class="direct-chat-text">
                                                <?=$data_chat['percakapan'];?>
                                              </div>
                                              <!-- /.direct-chat-text -->
                                            </div>
                                            <!-- /.Chat dari kiri -->
                                            <?php
                                          } else {
                                            ?>
                                            <!-- Chat dari kanan -->
                                            <div class="direct-chat-msg right">
                                              <div class="direct-chat-infos clearfix">
                                                <span class="direct-chat-name float-right"><?=$nama_mhs;?></span>
                                                <span class="direct-chat-timestamp float-left"><?=$fr_waktu;?></span>
                                              </div>
                                              <!-- /.direct-chat-infos -->
                                              <img class="direct-chat-img" src="<?=$foto_mhs;?>" alt="Message User Image">
                                              <!-- /.direct-chat-img -->
                                              <div class="direct-chat-text">
                                                <?=$data_chat['percakapan'];?>
                                              </div>
                                              <!-- /.direct-chat-text -->
                                            </div>
                                            <!-- /.Chat dari kanan -->
                                            <?php
                                          }
                                        }
                                      } else {
                                        ?>
                                        <center>Belum Ada Percakapan</center>
                                        <?php
                                      }
                                      ?>
                                    </div>
                                    <!--/.direct-chat-messages-->
                                  </div>
                                  <!-- /.card-body -->
                                  <div class="card-footer">
                                    <?php
                                    $idb = @$_GET['id_kelas'];
                                    $pro = @$_GET['progres'];
                                    ?>
                                    <form action="chat_mhs.php?id_kelas=<?=$idb;?>&progres=<?=$pro;?>&nim=<?=$nim;?>" method="post">
                                      <div class="input-group">
                                        <input type="text" name="pesan" placeholder="Tulis Pesan ..." class="form-control" required>
                                        <span class="input-group-append">
                                          <button type="submit" class="btn btn-primary" name="kirim"><i class="nav-icon fas fa-paper-plane"></i> Kirim</button>
                                        </span>
                                      </div>
                                    </form>
                                  </div>
                                  <!-- /.card-footer-->
                                </div>
                                <!--/.direct-chat -->
                                <!-- End of Judul -->
                              </div>
                              <div class="col-lg-4">
                                <div class="card card-primary">
                                  <div class="card-header">
                                    <h3 class="card-title"><i class="nav-icon fas fa-copy"></i> Status Bimbingan</h3>
                                  </div>
                                  <div class="card-body">
                                    <table class="table table-bordered table-sm">
                                      <tr>
                                        <td width="15%">
                                          <?php
                                          $progres = @$_GET['progres'];

                                          if($progres == 1) {
                                            ?>
                                            <center>
                                              <img src="../images/growth.png" width="20px" height="20px">
                                            </center>
                                            <?php
                                          }
                                          if($progres >= 2){
                                            ?>
                                            <center>
                                              <img src="../images/check.png" width="20px" height="20px">
                                            </center>
                                            <?php
                                          }
                                          if($progres == 0){
                                            ?>
                                            <center>
                                              <img src="../images/close.png" width="20px" height="20px">
                                            </center>
                                            <?php
                                          }
                                          ?>
                                        </td>
                                        <td>Judul</td>
                                        <td>-</td>
                                      </tr>

                                      <tr>
                                        <td width="15%">
                                          <center>
                                            <img src="../images/close.png" width="18px" height="18px">
                                          </center>
                                        </td>
                                        <td>Bab 1</td>
                                        <td>-</td>
                                      </tr>

                                      <tr>
                                        <td width="15%">
                                          <center>
                                            <img src="../images/close.png" width="18px" height="18px">
                                          </center>
                                        </td>
                                        <td>Bab 2</td>
                                        <td>-</td>
                                      </tr>

                                      <tr>
                                        <td width="15%">
                                          <center>
                                            <img src="../images/close.png" width="18px" height="18px">
                                          </center>
                                        </td>
                                        <td>Bab 3</td>
                                        <td>-</td>
                                      </tr>

                                      <tr>
                                        <td width="15%">
                                          <center>
                                            <img src="../images/close.png" width="18px" height="18px">
                                          </center>
                                        </td>
                                        <td>Bab 4</td>
                                        <td>-</td>
                                      </tr>

                                      <tr>
                                        <td width="15%">
                                          <center>
                                            <img src="../images/close.png" width="18px" height="18px">
                                          </center>
                                        </td>
                                        <td>Bab 5</td>
                                        <td>-</td>
                                      </tr>
                                    </table>
                                  </div>
                                </div>
                              </div><!--/div col 4-->
                            </div>
                          </div>
                        </div>
                      </div>
                    </div><!--/row-->
                  </div><!-- /.card-body -->
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
