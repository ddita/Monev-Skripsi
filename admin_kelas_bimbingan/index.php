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
      <!-- <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake" src="../images/UP.png" alt="Monev-Skripsi" height="60" width="60">
      </div> -->

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
                  <li class="breadcrumb-item active">Dashboard Kelas Bimbingan</li>
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
                  <div class="card card-header">
                    <h5 class="card-title" style="font-weight: bold ;"><i class="fas fa-chalkboard-teacher nav-icon"></i> Kelas Bimbingan</h5>
                  </div>
                  <div class="card-body">
                    <?php
                    echo $encript_data;
                    ?>
                    <div class="form-group row">
                      <div class="col-lg-2">
                        <label for="">Periode Akademik :</label>
                      </div>
                      <div class="col-lg-3">
                        <form action="" method="post">
                          <select class="form-control" id="periode" name="periode" required>
                            <option value="">-- Pilih Periode Akademik --</option>
                            <?php
                            $pglperiode = mysqli_query($conn, "SELECT * FROM tbl_periode") or die(mysqli_error($conn));
                            $rvperiode = mysqli_num_rows($pglperiode);
                            if ($rvperiode>0){
                              while ($dt_periode = mysqli_fetch_array($pglperiode)) {
                                ?>
                                <option value="<?=$dt_periode['kode_periode'];?>"> <?=$dt_periode['kode_periode'];?> - <?=$dt_periode['periode'];?> </option>
                                <?php
                              }
                            } else {

                            }
                            ?>
                          </select>
                        </div>
                        <div class="col-lg-7">
                          <button type="submit" class="btn btn-info btn-md" name="cari"><i class="nav-icon fas fa-search"></i> Cari Data</button>
                          </form>
                          <button type="submit" class="btn btn-primary btn-md" name="tambah"><i class="nav-icon fas fa-plus"></i> Tambah Data</button>
                          <button type="submit" class="btn btn-success btn-md" name="impor"><i class="nav-icon fas fa-file-excel"></i> Import Data</button>
                          <?php
                          if (isset($conn, $_POST['cari'])) {
                            $periodeterpilih = trim(mysqli_real_escape_string($conn, $_POST['periode']));
                          ?>
                          <a href="skbimbingan.php?periode=<?=encriptData($periodeterpilih)?>" class="btn btn-md btn-warning" target="_blank"><i class="nav-icon fas fa-file">&nbsp</i>  Surat Keputusan</a>
                        </div>
                        <div class="col-lg-12">
                          <?php
                          $pgl_kelas_bimbingan = mysqli_query($conn, "SELECT * FROM tbl_kelas_bimbingan WHERE kode_periode = '$periodeterpilih'") or die(mysqli_error($conn));
                          ?>
                          <table id="example1" class="table table-bordered table-striped table-sm">
                      <thead>
                        <tr>
                          <th width="10%">No</th>
                          <th>Periode Akademik</th>
                          <th>Dosen Pembimbing</th>
                          <th>Mahasiswa</th>
                          <th>Progres</th>
                          <th>Status</th>
                          <th><center>Aksi</center></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no=1;
                        $querybimbingan = mysqli_query($conn, "SELECT * FROM tbl_kelas_bimbingan WHERE kode_periode = '$periodeterpilih'") or die(mysqli_error($conn));
                        $rvkelasbimbingan = mysqli_num_rows($querybimbingan);

                        if($rvkelasbimbingan>0) {
                          while ($dt_bimbingan = mysqli_fetch_array($querybimbingan)) {
                            ?>
                            <tr>
                              <td><?=$no++?></td>
                              <td>
                                <?php
                                $pd = $periodeterpilih;
                                $ambilperiode = mysqli_query($conn, "SELECT periode FROM tbl_periode WHERE kode_periode = '$pd'");
                                $arrp = mysqli_fetch_assoc($ambilperiode);
                                $periode = $arrp['periode'];
                                ?>
                                <b><?=$periodeterpilih;?></b>
                                <br><?=$periode;?>
                              </td>
                              <td>
                                <?php
                                $nidn = $dt_bimbingan['nidn'];
                                $ambilnamadosen = mysqli_query($conn, "SELECT nama FROM tbl_dosen WHERE nidn='$nidn'");
                                $arr = mysqli_fetch_assoc($ambilnamadosen);
                                $dosen = $arr['nama'];
                                ?>
                                <b><?=$dt_bimbingan['nidn']?></b>
                                <br><?=$dosen;?>
                              </td>
                              <td>
                                <?php
                                $nim = $dt_bimbingan['nim'];
                                $ambilnamamhs = mysqli_query($conn, "SELECT nama,kontak FROM tbl_mahasiswa WHERE nim = '$nim'");
                                $arrmhs = mysqli_fetch_assoc($ambilnamamhs);
                                $mhs = $arrmhs['nama'];
                                ?>
                                <b><?=$nim?></b>
                                <br><?=$mhs;?>
                                <br><a href="https://api.whatsapp.com/send?phone=<?=$arrmhs['kontak'];?>&text=Assalamu'alaikum Mahasiswa <?=$arrmhs['nama']?>" class="btn btn-sm btn-success" target="_blank">
                                  <img src="../images/logo-wa.png" height="18px" width="18px"> <?=$arrmhs['kontak'];?>
                                </a>
                              </td>
                              <td> JUDUL - PROGRES</td>
                              <td>
                                <center>
                                  <?php
                                  if ($dt_bimbingan['status']==1) {
                                    ?>
                                    <button type="button" class="btn btn-sm btn-primary"> Aktif</button>
                                    <?php
                                  } else {
                                    ?>
                                    <button type="button" class="btn btn-sm btn-danger"> Tidak Selesai</button>
                                    <?php
                                  }
                                  ?>
                                </center>
                              </td>
                              <td>
                                <center>
                                  <a href="detailbimbingan.php?id_kelas=<?=encriptData($dt_bimbingan['id_kelas'])?>" class="btn btn-sm btn-info"> <i class="nav-icon fas fa-edit"></i> Detail</a>
                                  <a href="" class="btn btn-sm btn-danger"><i class="nav-icon fas fa-trash"></i></a>
                                  <a href="suratizin.php?id_kelas=<?=encriptData($dt_bimbingan['id_kelas'])?>" class="btn btn-sm btn-warning" target="_blank"><i class="nav-icon fas fa-file">&nbsp</i>  Surat Izin</a>
                                </center>
                              </td>
                            </tr>
                            <?php
                          }
                        }
                        ?>
                      </tbody>
                    </table>
                          <?php

                        }
                        ?>
                    </div>

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