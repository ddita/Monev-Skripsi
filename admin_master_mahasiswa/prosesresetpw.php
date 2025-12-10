<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Proses Reset Password Mahasiswa</title>
</head>
<body>
<?php
require_once '../database/config.php';
session_start();

$nim = @$_GET['kd_mhs'];
$pass = sha1($nim);

$queryresetmhs = mysqli_query($conn, "UPDATE tbl_mahasiswa SET password='$pass' WHERE username='$nim'") or die(mysqli_error($conn));

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
	swal("Berhasil", "Password akun mahasiswa dengan NIM : <?=$nim;?> berhasil dihapus", "success");
	setTimeout(function(){
		window.location.href = "../admin_master_mahasiswa";
	}, 1500);
</script>
?>
</body>
</html>