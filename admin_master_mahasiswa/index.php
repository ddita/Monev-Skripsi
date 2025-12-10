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
                  <li class="breadcrumb-item"><a href="#">Admin Dashboard</a></li>
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
                    <a href="tambahmhs.php" class="btn btn-primary btn-sm"><i class="nav-icon fas fa-download"></i> Tambah Mahasiswa</a>
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#modal-import"><i class="nav-icon fas fa-file-excel"></i> Import Data
                    </button>
                    <a href="export.php" class="btn btn-info btn-sm" target="_blank"><i class="nav-icon fas fa-file"></i> Export Data</a>
                    <a href="prosesresetdata.php?reset=reset_data" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin akan mereset data ini?')"><i class="nav-icon fas fa-sync"></i> Reset Data</a>
                    <br>
                    <br>
                    <table id="example1" class="table table-bordered table-striped table-sm">
                      <thead>
                        <tr>
                          <th width="5%">No</th>
                          <th>NIM</th>
                          <th>Program Studi</th>
                          <th>Status</th>
                          <th>Angkatan</th>
                          <th>Kontak</th>
                          <th>Foto</th>
                          <th>Kelamin</th>
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
                          <td>
                            <b><?=$dt_mhs['nim'];?></b>
                            <br><?=$dt_mhs['nama'];?>
                          </td>
                          <td>
                            <?php
                            $kode_prodi = $dt_mhs['prodi'];
                            $ambilnamaprodi =  mysqli_query($conn, "SELECT nama_prodi FROM tbl_prodi WHERE kode_prodi='$kode_prodi'") or die(mysqli_error($conn));
                            $data_prodi = mysqli_fetch_assoc($ambilnamaprodi);
                            $nama_prodi = $data_prodi['nama_prodi'];
                            ?>
                            <?=$nama_prodi;?>
                          </td>
                          <td>
                            <center>
                              <?php
                              $kode_stt = $dt_mhs['status'];
                              $ambilstt = mysqli_query($conn, "SELECT status FROM tbl_status WHERE id='$kode_stt'") or die(mysqli_error($conn));
                              $data_stt = mysqli_fetch_assoc($ambilstt);
                              $stt_mhs = $data_stt['status'];
                              ?>
                              <?= $stt_mhs; ?>
                          </center>
                        </td>
                        <td>
                          <center>
                              <?php
                              $kode_akt = $dt_mhs['angkatan'];
                              $ambilakt =  mysqli_query($conn, "SELECT keterangan FROM tbl_angkatan WHERE kode_angkatan='$kode_akt'") or die(mysqli_error($conn));
                              $data_akt = mysqli_fetch_array($ambilakt);
                              $angkatan = $data_akt['keterangan'];
                              ?>
                              <?=$angkatan;?>
                            </center>
                          </td>
                          <td>
                            <center>
                              <a href="https://api.whatsapp.com/send?phone=<?=$dt_mhs['kontak'];?>&text=Assalamu'alaikum <?=$dt_mhs['nama'];?>" class="btn btn-sm btn-success" target="_blank">
                                <img src="../images/logo-wa.png" height="18px" width="18px"> <?=$dt_mhs['kontak'];?>
                              </a>
                            </center>
                          </td>
                          <td>
                                <?php
                                if ($dt_mhs['foto']=='') {
                                  ?>
                                  <center>
                                    <button type="button"class="btn btn-sm btn-info" style="background-color:transparent;" data-toggle="modal" data-target="#modal-foto" data-nim="<?=$dt_mhs['nim'];?>" data-foto="../images/graduate.png"> <img src="../images/graduate.png" height="50px" width="50px">
                                    </button>
                                  </center>
                                  <?php
                                } else {
                                ?>
                                <center>
                                  <button type="button"class="btn btn-sm btn-info" style="background-color:transparent;" data-toggle="modal" data-target="#modal-foto" data-nim="<?=$dt_mhs['nim'];?>" data-foto="<?=$dt_mhs['foto'];?>"> <img src="<?=$dt_mhs['foto'];?>" height="50px" width="50px">
                                  </button>
                                </center>
                                <?php
                                }
                                ?>
                              </td>
                          <td>
                            <?php
                            $k_mhs = $dt_mhs['kelamin'];
                            if ($k_mhs=='L') {
                              ?>
                                <p> Laki-laki </p>
                              <?php
                            } else{
                              ?>
                                <p> Perempuan </p>
                              <?php
                            }
                            ?>
                          </td>
                          <td>
                            <center>
                              <a href="prosesresetpw.php?kd_mhs=<?=$dt_mhs['nim'];?>&resetpw=resetpw" class="btn btn-sm btn-warning" onclick="return confirm('Anda akan mereset akun mahasiswa dengan NIM [<?=$dt_mhs['nim'];?>] - Nama Mahasiswa : [<?=$dt_mhs['nama'];?>]')">
                                <i class="nav-icon fas fa-sync"></i> Reset Pwd
                              </a>
                              <button type="button" class="btn btn-sm btn-info btn-edit" data-toggle="modal" data-target="#modal-default"
                             data-nim="<?= $dt_mhs['nim']; ?>"
                             data-nama="<?= $dt_mhs['nama'];?>" 
                             data-prodi="<?= $dt_mhs['prodi'];?>"
                             data-status="<?= $dt_mhs['status']; ?>" 
                             data-angkatan="<?= $dt_mhs['angkatan'];?>" 
                             data-kontak="<?= $dt_mhs['kontak']; ?>" 
                             data-kelamin="<?= $dt_mhs['kelamin']; ?>" >
                             <i class="nav-icon fas fa-edit"></i></button>
                              <a href="proseshapus.php?kd_mhs=<?=encriptData($dt_mhs['nim'])?>&hapus=hapus" class="btn btn-sm btn-danger" onclick="return confirm('Anda akan menghapus data mahasiswa dengan NIM [<?=$dt_mhs['nim'];?>] - Nama Mahasiswa : [<?=$dt_mhs['nama'];?>]')">
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
                        <td colspan="8"><center>Tidak ditemukan data mhs pada database</center></td>
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

<div class="modal fade" id="modal-foto">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#001f3f;">
          <h4 class="modal-title"><font color="#ffffff"><i class="fas fa-file"></i> Edit Foto Mahasiswa</font></h4>
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
                        <img src="" id="fotomhs" height="100px" width="100px">
                        <input type="file" name="fotomhs" class="form-control" accept="image/*" required>
                        <br>
                        <input type="text" class="form-control" name="nim" id="nim" hidden>
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

   var nim = $(e.relatedTarget).data('nim');
   var foto = $(e.relatedTarget).data('foto');

   $(e.currentTarget).find('input[name="nim"]').val(nim);
   document.getElementById('fotomhs').src= foto;
   
 });
</script>

<div class="modal fade" id="modal-import">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header" style="background-color:#001f3f;">
          <h4 class="modal-title"><font color="#ffffff"><i class="fas fa-file-excel"></i> Import Data Mahasiswa</font></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="prosesimport.php" method="POST" enctype="multipart/form-data">
          <div class="modal-body">
            <a href="download.php?filename=template_mahasiswa.xls" class="btn btn-sm btn-secondary"><i class="nav-icon fas fa-file"></i> Download Template Import Excel</a>
            <br>
            <br>
            <div class="form-group">
              <label for="import">Ambil File Exel</label>
              <input type="file" class="form-control" id="import" name="file" placeholder="Ambil File Excel" accept="application/vnd.ms-excel">
            </div>
            <div class="modal-footer pull-right">
              <button type="submit" class="btn btn-success" name="importmhs"><i class="nav-icon fas fa-upload"></i>Import Data</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<script type="text/javascript">
   $('#modal-import').on('show.bs.modal', function(e) {

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
  $(e.currentTarget).find('select[name="angkatan"]').val(angkatan);
  $(e.currentTarget).find('input[name="kontak"]').val(kontak);
  $(e.currentTarget).find('select[name="kelamin"]').val(kelamin);
     
   });
</script>

<div class="modal fade" id="modal-default">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header" style="background-color:#001f3f;">
        <h4 class="modal-title"><font color="#ffffff"><i class="fas fa-file"></i> Edit Data Mahasiswa</font></h4>
      </div>
      <form id="modal-default" action="editmhs.php" method="POST">
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
                  <label for="data-prodi" class="col-sm-12 control-label">Program Studi</label>
                  <div class="col-sm-12">
                    <select class="form-control" id="editProdi" name="prodi" required>
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
                </div>
                <div class="form-group row">
                  <label for="data-status" class="col-sm-12 control-label">Status</label>
                  <div class="col-sm-12">
                    <select class="form-control" id="editStatus" name="status" required>
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
                </div>
                <div class="form-group row">
                  <label for="data-angkatan" class="col-sm-12 control-label">Angkatan</label>
                  <div class="col-sm-12">
                    <select class="form-control" id="editAngkatan" name="angkatan" required>
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
                </div>
                <div class="form-group row">
                  <label for="data-kontak" class="col-sm-12 control-label">Kontak</label>
                  <div class="col-sm-12">
                    <input type="text" class="form-control" name="kontak" id="kontak">
                  </div>
                </div>
                <div class="form-group row">
                  <label for="data-kelamin" class="col-sm-12 control-label">Kelamin</label>
                  <div class="col-sm-12">
                    <select class="form-control" id="editKelamin" name="kelamin" required>
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
            </div>
          </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript">
   $('#modal-default').on('show.bs.modal', function(e) {

   var nim = $(e.relatedTarget).data('nim');
   var nama = $(e.relatedTarget).data('nama');
   var prodi = $(e.relatedTarget).data('prodi');
   var status = $(e.relatedTarget).data('status');
   var angkatan = $(e.relatedTarget).data('angkatan');
   var kontak = $(e.relatedTarget).data('kontak');
   var kelamin = $(e.relatedTarget).data('kelamin');


  

  $(e.currentTarget).find('input[name="nim"]').val(nim);
  $(e.currentTarget).find('input[name="nama"]').val(nama);
  $(e.currentTarget).find('select[name="prodi"]').val(prodi);
  $(e.currentTarget).find('select[name="status"]').val(status);
  $(e.currentTarget).find('select[name="angkatan"]').val(angkatan);
  $(e.currentTarget).find('input[name="kontak"]').val(kontak);
  $(e.currentTarget).find('select[name="kelamin"]').val(kelamin);  
   
   });
</script>

</body>
</html>