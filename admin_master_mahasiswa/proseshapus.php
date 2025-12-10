<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Proses Hapus Mahasiswa</title>
</head>
<body>
<?php
require_once '../database/config.php';
session_start();

$nim = @$_GET['kd_mhs'];
$de_nim = decriptData($nim);

$queryhapusmhs = mysqli_query($conn, "DELETE FROM tbl_mahasiswa WHERE nim='$de_nim'") or die(mysqli_error($conn));
$queryhapus_pg_mhs = mysqli_query($conn, "DELETE FROM tbl_pengguna WHERE username='$de_nim'") or die(mysqli_error($conn));

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
	swal("Berhasil", "Data Mahasiswa dengan NIM : <?=$de_nim;?> berhasil dihapus", "success");
	setTimeout(function(){
		window.location.href = "../admin_master_mahasiswa";
	}, 1500);
</script>
</body>
</html>