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
                <h1 class="m-0">Master Data Mahasiswa</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active">Master Data Mahasiswa</li>
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
                    <h3 class="card-title"><i class="nav-icon fas fa-user-graduate"></i> Data Mahasiswa</h3>
                  </div>
                  <div class="card-body">
                    <a href="tambahmhs.php" class="btn btn-primary btn-sm"><i class="nav-icon fas fa-download"></i> Tambah Data</a>
                    <br>
                    <br>
                    <table id="example1" class="table table-bordered table-striped">
                      <thead>
                        <tr>
                          <th width="5%">No</th>
                          <th>NIM</th>
                          <th>Nama</th>
                          <th>Prodi</th>
                          <th>Status</th>
                          <th>Angkatan</th>
                          <th>Kontak</th>
                          <th>Jenis Kelamin</th>
                          <th><center>Aksi</center></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no=1;
                        $querymhs = mysqli_query($conn, "SELECT * FROM tbl_mahasiswa") or die(mysqli_error($conn));
                        if (mysqli_num_rows($querymhs)>0){
                          while ($dt_mhs = mysqli_fetch_array($querymhs)){
                        ?>
                        <tr>
                          <td><?=$no++;?></td>
                          <td><?=$dt_mhs['nim'];?></td>
                          <td><?=$dt_mhs['nama'];?></td>
                          <td><?=$dt_mhs['prodi'];?></td>
                          <td>
                              <?php
                                // punya modal apa?
                              $st_mhs = $dt_mhs['status'];
                              $qrst = mysqli_query($conn, "SELECT status FROM tbl_status WHERE id='$stt_mhs'") or die(mysqli_error($conn));

                                //tampung array dari query
                              $tampungstmhs = mysqli_fetch_array($qrst);

                                //tampilkan berdasarkan nama kolo pada database
                              $status = $tampungstmhs['status'];
                              ?>
                              <?= $status; ?>
                            </td>
                          <td><?=$dt_mhs['angkatan'];?></td>
                          <td><?=$dt_mhs['kontak'];?></td>
                          <td><?=$dt_mhs['kelamin'];?></td>
                          <td>
                            <center>
                              <a href="proses.php?kd_mhs=<?=$dt_mhs['nim'];?>&resetpw=resetpw" class="btn btn-sm btn-warning" onclick="return confirm('Anda akan mereset akun dosen dengan NIM [<?=$dt_mhs['nim'];?>] - Nama Mahasiswa : [<?=$dt_mhs['nama'];?>]')">
                                <i class="nav-icon fas fa-sync"></i> Reset Pwd
                              </a>
                              <button type="button" class="btn btn-sm btn-info btn-edit" data-toggle="modal" data-target="#modal-edit" data-nim="<?= $dt_mhs['nim']; ?>" data-nama="<?= $dt_mhs['nama']; ?>" data-prodi="<?= $dt_mhs['prodi']; ?>" data-status="<?= $dt_mhs['status']; ?>" data-angkatan="<?= $dt_mhs['angkatan']; ?>" data-kontak="<?= $dt_mhs['kontak']; ?>" data-kelamin="<?= $dt_mhs['kelamin']; ?>"> <i class="nav-icon fas fa-edit"></i>
                              </button>
                              <a href="proses.php?kd_mhs=<?=$dt_mhs['nim'];?>&hapus=hapus" class="btn btn-sm btn-danger" onclick="return confirm('Anda akan menghapus data mahasiswa dengan NIM [<?=$dt_mhs['nim'];?>] - Nama : [<?=$dt_mhs['nama'];?>]')">
                                <i class="nav-icon fas fa-trash"></i>
                              </a>
                            </center>
                          </td>
                        </tr>
                        <?php
                        }
                        }
                        else {
                          ?>
                          <tr>
                            <td colspan="7"><center>Tidak ditemukan data dosen pada database</center></td>
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

  <div class="modal fade" id="modal-edit">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#001f3f;">
          <h4 class="modal-title"><font color="#ffffff"><i class="fas fa-file"></i> Edit Data Mahasiswa</font></h4>
        </div>
        <form id="modal-edit" action="editmhs.php" method="POST">
          <div class="modal-body">
            <div class="col-md-12">
              <!-- Horizontal Form -->
              <div class="card card-info">
                <!-- /.card-header -->
                <!-- form start -->
                <div class="card-body">
                  <div class="form-group row">
                    <label for="data-nim" class="col-sm-12 control-label">NIM</label>
                    <div class="col-sm-12">
                      <input type="text" class="form-control" name="nim" id="nim" disabled>
                      <input type="text" class="form-control" name="nim" id="nim" hidden>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="data-nama" class="col-sm-12 control-label">Nama Mahasiswa</label>
                    <div class="col-sm-12">
                      <input type="text" class="form-control" name="nama" id="nama">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="data-prodi" class="col-sm-12 control-label">Prodi</label>
                    <div class="col-sm-12">
                      <input type="text" class="form-control" name="prodi" id="prodi">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="data-status" class="col-sm-12 control-label">Status</label>
                    <div class="col-sm-12">
                      <select class="custom-select from control-border" id="editStatus" name="status" required>
                        <option value=""> -- Pilih Status Mahasiswa --</option>
                        <option value="1">Aktif</option>
                        <option value="2">Non Aktif</option>
                        <option value="3">Cuti</option>
                        <option value="4">Drop Out</option>
                        <option value="5">Passed Out</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="data-angkatan" class="col-sm-12 control-label">Angkatan</label>
                    <div class="col-sm-12">
                      <input type="text" class="form-control" name="angkatan" id="angkatan">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="data-kontak" class="col-sm-12 control-label">Kontak</label>
                    <div class="col-sm-12">
                      <input type="text" class="form-control" name="kontak" id="kontak">
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="data-kelamin" class="col-sm-12 control-label">Jenis Kelamin</label>
                    <div class="col-sm-12">
                      <select class="form-control" id="kelamin" name="kelamin" required>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <button type="submit" name="editmhs" class="btn btn-primary">Simpan Perubahan</button>
                </div>
              </form>
            </div>
          </div>
        </div>

<script type="text/javascript">
  $('#modal-edit').on('show.bs.modal', function(e) {
    var nim = $(e.relatedTarget).data('nim');
    var nama = $(e.relatedTarget).data('nama');
    var prodi = $(e.relatedTarget).data('prodi');
    var status = $(e.relatedTarget).data('status');
    var angkatan = $(e.relatedTarget).data('angkatan');
    var kontak = $(e.relatedTarget).data('kontak');
    var kelamin = $(e.relatedTarget).data('kelamin');
    

    $(e.currentTarget).find('input[name="nim"]').val(nim);
    $(e.currentTarget).find('input[name="nama"]').val(nama);
    $(e.currentTarget).find('input[name="prodi"]').val(prodi);
    $(e.currentTarget).find('select[name="status"]').val(status);
    $(e.currentTarget).find('input[name="angkatan"]').val(angkatan);
    $(e.currentTarget).find('input[name="kontak"]').val(kontak);
    $(e.currentTarget).find('select[name="kelamin"]').val(kelamin);
  });
</script>

</body>
</html>
