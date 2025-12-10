<?php
session_start();
$konstruktor = 'admin_master_mahasiswa';
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
                <h1 class="m-0">Tambah Mahasiswa</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Dashboard Mahasiswa</a></li>
                  <li class="breadcrumb-item active">Tambah Data Mahasiswa</li>
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
                    <h3 class="card-title"><i class="nav-icon fas fa-user-graduate"></i> Tambah Data Mahasiswa</h3>
                  </div>
                  <!-- /.card-header -->
                  <!-- form start -->
                  <form action="prosestambah.php" method="post">
                    <div class="card-body">
                      <a href="../admin_master_mahasiswa" class="btn btn-warning btn-sm"><i class="nav-icon fas fa-chevron-left"></i> Kembali</a>
                      <div class="form-group">
                        <label for="nim">NIM</label>
                        <input type="text" class="form-control" id="nim" name="nim" maxlength="50" onkeypress="return IsNumeric(event);" placeholder="Input NIM Mahasiswa" autofocus required>
                      </div>
                      <div class="form-group">
                        <label for="nama">Nama Mahasiswa</label>
                        <input type="text" class="form-control" id="nama" name="nama" maxlength="150" placeholder="Input Nama Mahasiswa" required>
                      </div>
                      <div class="form-group">
                        <label for="prodi">Program Studi</label>
                        <select class="form-control" name="prodi" required>
                          <option value="">-- Pilih Prodi --</option>
                          <?php
                          //panggil data pada data tabel prodi
                          $pglprodi = mysqli_query($conn, "SELECT * FROM tbl_prodi") or die(mysqli_error($conn));
                          //variabel untuk menampung return value dari query panggil prodi
                          $rvprodi = mysqli_num_rows($pglprodi);

                          //kondisi jika tabel prodi memiliki <= 1 data
                          if($rvprodi>0) {
                            //melakukan perulangan untuk menampilkan data
                            while($dt_prodi = mysqli_fetch_array($pglprodi)) {
                              //tampilkan data pada option di select elemen
                              ?>
                              <option value="<?=$dt_prodi['kode_prodi'];?>">
                                <?=$dt_prodi['kode_prodi'];?> - <?=$dt_prodi['nama_prodi'];?>
                              </option>
                              <?php
                            }
                          }
                          //kondisi jika tabel prodi kosong
                          else {

                          }
                          ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" name="status" required>
                          <option value="">-- Pilih Status --</option>
                          <?php
                          //panggil data pada data tabel status
                          $pglstatus = mysqli_query($conn, "SELECT * FROM tbl_status") or die(mysqli_error($conn));
                          //variabel untuk menampung return value dari query panggil prodi
                          $rvstatus = mysqli_num_rows($pglstatus);

                          //kondisi jika tabel prodi memiliki <= 1 data
                          if($rvstatus>0) {
                            //melakukan perulangan untuk menampilkan data
                            while($dt_status = mysqli_fetch_array($pglstatus)) {
                              //tampilkan data pada option di select elemen
                              ?>
                              <option value="<?=$dt_status['id'];?>">
                                <?=$dt_status['id'];?> - <?=$dt_status['status'];?>
                              </option>
                              <?php
                            }
                          }
                          //kondisi jika tabel prodi kosong
                          else {

                          }
                          ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="angkatan">Angkatan</label>
                        <select class="form-control" name="angkatan" required>
                          <option value="">-- Pilih Angkatan --</option>
                          <?php
                          //panggil data pada data tabel angkatan
                          $pglangkatan = mysqli_query($conn, "SELECT * FROM tbl_angkatan") or die(mysqli_error($conn));
                          //variabel untuk menampung return value dari query panggil angkatan
                          $rvangkatan = mysqli_num_rows($pglangkatan);

                          //kondisi jika tabel prodi memiliki <= 1 data
                          if($rvangkatan>0) {
                            //melakukan perulangan untuk menampilkan data
                            while($dt_angkatan = mysqli_fetch_array($pglangkatan)) {
                              //tampilkan data pada option di select elemen
                              ?>
                              <option value="<?=$dt_angkatan['kode_angkatan'];?>">
                                <?=$dt_angkatan['kode_angkatan'];?> - <?=$dt_angkatan['keterangan'];?>
                              </option>
                              <?php
                            }
                          }
                          //kondisi jika tabel prodi kosong
                          else {

                          }
                          ?>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="kontak">Kontak</label>
                        <input type="text" class="form-control" id="kontak" name="kontak" maxlength="15" placeholder="Input Kontak Mahasiswa" onkeypress="return IsNumeric(event);" required>
                      </div>
                      <div class="form-group">
                        <label for="kelamin">Jenis Kelamin</label>
                        <select class="form-control" name="kelamin" required>
                          <option value="">-- Pilih Jenis Kelamin --</option>
                          <option value="L">Laki-laki</option>
                          <option value="P">Perempuan</option>
                        </select>
                      </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                      <button type="submit" class="btn btn-primary btn-block" name="tambahmhs"><i class="nav-icon fas fa-plus"></i> Tambah Data</button>
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
