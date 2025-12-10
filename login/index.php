<?php
session_start();
require_once '../database/config.php';
$pgllogotitle = mysqli_query($conn, "SELECT * FROM tbl_konfigurasi WHERE id=2") or die(mysqli_error($conn));
$arrtitle = mysqli_fetch_array($pgllogotitle);
$logotitle = $arrtitle['lokasi_file'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Monitoring Skripsi | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../assets_adminlte/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="../assets_adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets_adminlte/dist/css/adminlte.min.css">
  <link rel="shortcut icon" href="<?=$logotitle;?>">
</head>
<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <center>
        <?php
        $pgllogoapp = mysqli_query($conn, "SELECT lokasi_file FROM tbl_konfigurasi WHERE id=1") or die(mysqli_error($conn));
        $arrlogo = mysqli_fetch_array($pgllogoapp);
        $logoapp = $arrlogo['lokasi_file'];

        $pglnama = mysqli_query($conn, "SELECT * FROM tbl_konfigurasi WHERE id=3") or die(mysqli_error($conn));
        $arrnama = mysqli_fetch_assoc($pglnama);
        $namaapp = $arrnama['elemen'];

        $pglcopy = mysqli_query($conn, "SELECT * FROM tbl_konfigurasi WHERE id=4") or die(mysqli_error($conn));
        $arrcopy = mysqli_fetch_assoc($pglcopy);
        $copyapp = $arrcopy['elemen'];

        $pgluniv = mysqli_query($conn, "SELECT * FROM tbl_konfigurasi WHERE id=5") or die(mysqli_error($conn));
        $arruniv = mysqli_fetch_assoc($pgluniv);
        $univapp = $arruniv['elemen'];
        ?>
        <img src="<?=$logoapp;?>" height="100px" width="100px">
      </center>
      <a href="../login"><b><?=$namaapp;?></b></a>
      </div>
      <!-- /.login-logo -->
      <div class="card">
        <div class="card-body login-card-body">

          <form action="" method="post">
            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="NIM/NIDN" name="username">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user-circle"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" placeholder="Password" name="pass">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <a href="#" class="btn btn-block btn-danger">
                  <i class="nav-icon fas fa-question-circle"></i> Lupa Password
                </a>
              </div>
              <!-- /.col -->
              <div class="col-6">
                <button type="submit" class="btn btn-primary btn-block" name="login">Log In <i class="nav-icon fas fa-sign-out-alt"></i></button>
              </div>
              <!-- /.col -->
            </div>
          </form>
          <?php
          //tangkap trigger button login
          if(isset($conn, $_POST['login'])) {
            //menyimpan value pada elemen username dan pass kedalam variabel $user dan $pwd
            $user = trim(mysqli_real_escape_string($conn, $_POST['username']));
            $pwd = sha1(trim(mysqli_real_escape_string($conn, $_POST['pass'])));
            //cek user pada database
            $querycek = mysqli_query($conn, "SELECT * FROM tbl_pengguna WHERE username='$user' AND password='$pwd'") or die(mysqli_error($conn));

            //cek apakah ada value yang dikembalikan oleh query, jika value = 1 maka terdapat user seperti value elemen yang diinput user, jika 0 sebaliknya
            $rv = mysqli_num_rows($querycek);

            //buat percabangan
            if ($rv == 1) {
              //jika hasil $rv = 1 (ada user yang dimaksud)

              //buat variabel untuk menampung array value dari querycek
              $array = mysqli_fetch_assoc($querycek);
              //buat variabel untuk menampung nilai tertentu dari array yang diambil lewat query
              $st_user = $array['status'];

              //buat nested if (percabangan didalam percabangan)
              if ($st_user==0) {
                $_SESSION['username'] = $user;
                $_SESSION['nama_user'] = $array['nama_user'];
                $_SESSION['status'] = $st_user;

                echo '<script>window.location="../admin_dashboard"</script>';
              }
              elseif ($st_user==1) {
                $_SESSION['username'] = $user;
                $_SESSION['nama_user'] = $array['nama_user'];
                $_SESSION['status'] = $st_user;

                echo '<script>window.location="../dosen_dashboard"</script>';
              }
              elseif ($st_user==2) {
                $_SESSION['username'] = $user;
                $_SESSION['nama_user'] = $array['nama_user'];
                $_SESSION['status'] = $st_user;

                echo '<script>window.location="../mhs_dashboard"</script>';
              }
            }
            else {
              //jika hasil $rv = 0 (tidak ada user yang dimaksud)
              session_destroy();
              echo '<script>window.location="../gagal_login"</script>';
            }
          }
          ?>
          <div>
            <br>
            <center>
              copyright &copy; <?=$copyapp;?> <br> <b> <?=$univapp;?> </b> <br> <?php echo date ('Y'); ?>
            </center>
          </div>
        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="../assets_adminlte/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../assets_adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../assets_adminlte/dist/js/adminlte.min.js"></script>
  </body>
  </html>
