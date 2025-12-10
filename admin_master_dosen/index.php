<?php
session_start();
$konstruktor = 'admin_master_dosen';
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
                <h1 class="m-0">Master Data Dosen</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Admin Dashboard</a></li>
                  <li class="breadcrumb-item active">Master Data Dosen</li>
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
                    <h3 class="card-title"><i class="nav-icon fas fa-chalkboard-teacher"></i> Data Dosen</h3>
                  </div>
                  <div class="card-body">
                    <a href="tambahdosen.php" class="btn btn-primary btn-sm"><i class="nav-icon fas fa-download"></i> Tambah Dosen</a>
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal-import"><i class="nav-icon fas fa-file-excel"></i> Import Data
                    </button>
                    <a href="export.php" class="btn btn-info btn-sm" target="_blank"><i class="nav-icon fas fa-file"></i> Export Data</a>
                    <a href="proses.php?reset=reset_data" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin akan mereset data ini?')"><i class="nav-icon fas fa-sync"></i> Reset Data</a>
                    <br>
                    <br>
                    <table id="example1" class="table table-bordered table-striped table-sm">
                      <thead>
                        <tr>
                          <th width="10%">No</th>
                          <th>NIDN</th>
                          <th>Nama Dosen</th>
                          <th>Email</th>
                          <th>Kontak</th>
                          <th>Foto</th>
                          <th>Status</th>
                          <th><center>Aksi</center></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $no=1;
                        $querydosen = mysqli_query($conn, "SELECT * FROM tbl_dosen") or die(mysqli_error($conn));
                        if (mysqli_num_rows($querydosen)>0){
                          while ($dt_dosen = mysqli_fetch_array($querydosen)){
                            ?>
                            <tr>
                              <td><?=$no++;?></td>
                              <td><?=$dt_dosen['nidn'];?></td>
                              <td><?=$dt_dosen['nama'];?></td>
                              <td><?=$dt_dosen['email'];?></td>
                              <td>
                                <center>
                                  <a href="https://api.whatsapp.com/send?phone=<?=$dt_dosen['kontak'];?>&text=Assalamu'alaikum Bapak/Ibu <?=$dt_dosen['nama'];?>" class="btn btn-sm btn-success" target="_blank">
                                    <img src="../images/logo-wa.png" height="18px" width="18px"> <?=$dt_dosen['kontak'];?>
                                  </a>
                                </center>
                              </td>
                              <td>
                                <?php
                                if ($dt_dosen['foto']=='') {
                                  ?>
                                  <center>
                                    <button type="button"class="btn btn-sm btn-info" style="background-color:transparent;" data-toggle="modal" data-target="#modal-foto" data-nidn="<?=$dt_dosen['nidn'];?>" data-foto="../images/profile.png"> <img src="../images/profile.png" height="50px" width="50px">
                                    </button>
                                  </center>
                                  <?php
                                } else {
                                ?>
                                <center>
                                  <button type="button"class="btn btn-sm btn-info" style="background-color:transparent;" data-toggle="modal" data-target="#modal-foto" data-nidn="<?=$dt_dosen['nidn'];?>" data-foto="<?=$dt_dosen['foto'];?>"> <img src="<?=$dt_dosen['foto'];?>" height="50px" width="50px">
                                  </button>
                                </center>
                                <?php
                                }
                                ?>
                              </td>
                              <td>
                                <?php
                                $st_dosen = $dt_dosen['status'];
                                if ($st_dosen==1) {
                                  ?>
                                  <center>
                                    <button class="btn btn-sm btn-success"> Aktif</button>
                                  </center>
                                  <?php
                                } else{
                                  ?>
                                  <center>
                                    <button class="btn btn-sm btn-danger"> Nonaktif</button>
                                  </center>
                                  <?php
                                }
                                ?>
                              </td>
                              <td>
                                <center>
                                  <a href="proses.php?kd_dosen=<?=encriptData($dt_dosen['nidn'])?>&resetpw=resetpw" class="btn btn-sm btn-warning" onclick="return confirm('Anda akan mereset akun dosen dengan NIDN [<?=$dt_dosen['nidn'];?>] - Nama Dosen : [<?=$dt_dosen['nama'];?>]')">
                                    <i class="nav-icon fas fa-sync"></i> Reset Pwd
                                  </a>
                                  <button type="button" class="btn btn-sm btn-info btn-edit" data-toggle="modal" data-target="#modal-default"data-nidn="<?= $dt_dosen['nidn']; ?>"data-nama="<?= $dt_dosen['nama']; ?>"data-email="<?= $dt_dosen['email']; ?>"data-kontak="<?= $dt_dosen['kontak']; ?>"data-status="<?= $dt_dosen['status']; ?>"><i class="nav-icon fas fa-edit"></i> Edit
                                  </button>
                                  <a href="proses.php?kd_dosen=<?=encriptData($dt_dosen['nidn'])?>&hapus=hapus" class="btn btn-sm btn-danger" onclick="return confirm('Anda akan menghapus data angkatan dengan kode [<?=$dt_dosen['nidn'];?>] - dosen : [<?=$dt_dosen['nama'];?>]')">
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

   <div class="modal fade" id="modal-foto">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#001f3f;">
          <h4 class="modal-title"><font color="#ffffff"><i class="fas fa-file"></i> Edit Foto Dosen</font></h4>
        </div>
        <form class="form-horizontal" id="modal-foto" action="editfoto.php" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <div class="col-md-12">
              <!-- Horizontal Form -->
              <div class="card card-info">
                <!-- /.card-header -->
                <!-- form start -->
                <div class="card-body">
                  <div class="form-group row">
                    <label for="data-foto" class="col-sm-12 control-label">Upload Foto</label>
                    <div class="col-sm-12">
                      <center>
                        <img src="" id="fotodosen" height="100px" width="100px">
                        <input type="file" name="fotodosen" class="form-control" accept="image/*" required>
                        <br>
                        <input type="text" class="form-control" name="nidn" id="nidn" hidden>
                      </center>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                  <button type="submit" name="editfoto" class="btn btn-primary">Simpan Perubahan</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<script type="text/javascript">
 $('#modal-foto').on('show.bs.modal', function(e) {

   var nidn = $(e.relatedTarget).data('nidn');
   var foto = $(e.relatedTarget).data('foto');

   $(e.currentTarget).find('input[name="nidn"]').val(nidn);
   document.getElementById('fotodosen').src= foto;
   
 });
</script>

  <div class="modal fade" id="modal-import">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#001f3f;">
          <h4 class="modal-title"><font color="#ffffff"><i class="fas fa-file-excel"></i> Import Data Dosen</font></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="import.php" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <a href="download.php?filename=template_dosen.xls" class="btn btn-sm btn-secondary"><i class="nav-icon fas fa-file"></i> Download Template Import Excel</a>
            <br>
            <br>
            <div class="form-group">
              <label for="import">Ambil File Exel</label>
              <input type="file" class="form-control" id="import" name="file" placeholder="Ambil File Excel" accept="application/vnd.ms-excel">
            </div>
            <div class="modal-footer pull-right">
              <button type="submit" class="btn btn-success" name="importdosen"><i class="nav-icon fas fa-upload"></i>Import Data</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript">
   $('#modal-import').on('show.bs.modal', function(e) {

     var nidn = $(e.relatedTarget).data('nidn');
     var nama = $(e.relatedTarget).data('nama');
     var email = $(e.relatedTarget).data('email');
     var kontak = $(e.relatedTarget).data('kontak');
     var status = $(e.relatedTarget).data('status');

     $(e.currentTarget).find('input[name="nidn"]').val(nidn);
     $(e.currentTarget).find('input[name="nama"]').val(nama);
     $(e.currentTarget).find('input[name="email"]').val(email);
     $(e.currentTarget).find('input[name="kontak"]').val(kontak);
     $(e.currentTarget).find('select[name="status"]').val(status);   
   });
 </script>

 <div class="modal fade" id="modal-default">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#001f3f;">
        <h4 class="modal-title"><font color="#ffffff"><i class="fas fa-file"></i> Edit Data Dosen</font></h4>
      </div>
      <form id="modal-default" action="editdosen.php" method="POST">
        <div class="modal-body">
          <div class="col-md-12">
            <!-- Horizontal Form -->
            <div class="card card-info">
              <!-- /.card-header -->
              <!-- form start -->
              <div class="card-body">
                <div class="form-group row">
                  <label for="data-nidn" class="col-sm-12 control-label">NIDN</label>
                  <div class="col-sm-12">
                    <input type="text" class="form-control" name="nidn" id="nidn" disabled>
                    <input type="text" class="form-control" name="nidn" id="nidn" hidden>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="data-nama" class="col-sm-12 control-label">Nama Dosen</label>
                  <div class="col-sm-12">
                    <input type="text" class="form-control" name="nama" id="nama">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="data-email" class="col-sm-12 control-label">EMail</label>
                  <div class="col-sm-12">
                    <input type="text" class="form-control" name="email" id="email">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="data-kontak" class="col-sm-12 control-label">Kontak</label>
                  <div class="col-sm-12">
                    <input type="text" class="form-control" name="kontak" id="kontak">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="data-status" class="col-sm-12 control-label">Status</label>
                  <div class="col-sm-12">
                    <select class="form-control" id="editStatus" name="status" required>
                      <option value="1">Aktif</option>
                      <option value="2">Non Aktif</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" name="editdosen" class="btn btn-primary">Simpan Perubahan</button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script type="text/javascript">
   $('#modal-default').on('show.bs.modal', function(e) {

     var nidn = $(e.relatedTarget).data('nidn');
     var nama = $(e.relatedTarget).data('nama');
     var email = $(e.relatedTarget).data('email');
     var kontak = $(e.relatedTarget).data('kontak');
     var status = $(e.relatedTarget).data('status');




     $(e.currentTarget).find('input[name="nidn"]').val(nidn);
     $(e.currentTarget).find('input[name="nama"]').val(nama);
     $(e.currentTarget).find('input[name="email"]').val(email);
     $(e.currentTarget).find('input[name="kontak"]').val(kontak);
     $(e.currentTarget).find('select[name="status"]').val(status);  

   });
 </script>

</body>
</html>