<?php
session_start();
require_once '../database/config.php';

/* =========================
   AMBIL KONFIGURASI SISTEM
========================= */
$config = [];
$qConfig = mysqli_query($conn, "SELECT * FROM tbl_konfigurasi");
while ($row = mysqli_fetch_assoc($qConfig)) {
  $config[$row['nama_konfigurasi']] = $row['nilai_konfigurasi'];
}

/* =========================
   FALLBACK CONFIG
========================= */
$nama_aplikasi    = $config['nama_aplikasi'] ?? 'Monitoring Skripsi';
$logo_aplikasi    = $config['logo_aplikasi'] ?? '../assets/image/UP.png';
$favicon          = $config['favicon'] ?? '../assets/image/UP.png';
$nama_universitas = $config['nama_universitas'] ?? 'Universitas';

/* =========================
   PROSES LOGIN
========================= */
if (isset($_POST['login'])) {

  $username = mysqli_real_escape_string($conn, trim($_POST['username']));
  $password = md5(trim($_POST['password'])); // sesuaikan DB

  $query = mysqli_query($conn, "
    SELECT * FROM tbl_users 
    WHERE username='$username'
      AND password='$password'
      AND status='aktif'
  ");

  if (mysqli_num_rows($query) === 1) {

    $user = mysqli_fetch_assoc($query);

    $_SESSION['id_user']    = $user['id_user'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['nama_user'] = $user['nama_lengkap'];
    $_SESSION['role']      = $user['role'];

    if ($user['role'] === 'admin') {
      header("Location: ../admin_dashboard");
    } elseif ($user['role'] === 'dosen') {
      header("Location: ../dosen_dashboard");
    } else {
      header("Location: ../mhs_dashboard");
    }
    exit;
  } else {
    $_SESSION['error'] = "Username atau password salah!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title><?= $nama_aplikasi; ?> | Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../assets_adminlte/plugins/fontawesome-free/css/all.min.css">
  <!-- AdminLTE -->
  <link rel="stylesheet" href="../assets_adminlte/dist/css/adminlte.min.css">

  <!-- CUSTOM STYLE -->
  <style>
  body {
    background: linear-gradient(
      rgba(0,0,0,0.6),
      rgba(0,0,0,0.6)
    ), url("../assets/image/bg-login.jpg");
    background-size: cover;
    background-position: center;
    font-family: 'Poppins', sans-serif;
  }

  /* LOGIN BOX LEBIH KECIL */
  .login-box {
    width: 350px;
  }

  .login-logo img {
    width: 75px;
    margin-bottom: 8px;
  }

  .login-logo h4 {
    font-size: 18px;
    font-weight: 500;
  }

  /* CARD TRANSPARAN */
  .card {
    border-radius: 15px;
    background: rgba(255, 255, 255, 0.88);
    backdrop-filter: blur(6px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
  }

  .login-card-body {
    padding: 22px;
  }

  .login-box-msg {
    font-weight: 500;
    font-size: 14px;
  }

  .btn-primary {
    border-radius: 25px;
  }

  .form-control {
    border-radius: 20px;
    height: 42px;
    font-size: 14px;
  }

  .input-group-text {
    border-radius: 0 20px 20px 0;
  }
</style>


  <link rel="shortcut icon" href="<?= $favicon; ?>">
</head>

<body class="hold-transition login-page">

  <div class="login-box">

    <!-- LOGO -->
    <div class="login-logo text-white">
      <img src="<?= $logo_aplikasi; ?>" alt="Logo">
      <h4><?= $nama_aplikasi; ?></h4>
    </div>

    <!-- CARD -->
    <div class="card">
      <div class="card-body login-card-body">

        <p class="login-box-msg">Silakan login untuk melanjutkan</p>

        <!-- ERROR -->
        <?php if (isset($_SESSION['error'])) { ?>
          <div class="alert alert-danger text-center">
            <?= $_SESSION['error'];
            unset($_SESSION['error']); ?>
          </div>
        <?php } ?>

        <!-- FORM -->
        <form method="post">

          <div class="input-group mb-3">
            <input type="text" name="username" class="form-control" placeholder="Username" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>

          <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-6">
              <a href="#" class="btn btn-outline-secondary btn-block">
                <i class="fas fa-question-circle"></i> Lupa
              </a>
            </div>
            <div class="col-6">
              <button type="submit" name="login" class="btn btn-primary btn-block">
                Login <i class="fas fa-sign-in-alt"></i>
              </button>
            </div>
          </div>
        </form>

        <hr>

        <!-- ILLUSTRATION -->
        <!-- <div class="text-center">
          <img src="../assets/image/illustration-login.jpg" width="120" class="mt-2">
        </div> -->

        <!-- FOOTER -->
        <p class="text-center mt-3 text-muted">
          &copy; <?= date('Y'); ?><br>
          <b><?= $nama_universitas; ?></b>
        </p>

      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="../assets_adminlte/plugins/jquery/jquery.min.js"></script>
  <script src="../assets_adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets_adminlte/dist/js/adminlte.min.js"></script>

</body>

</html>