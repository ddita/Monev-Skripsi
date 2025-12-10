<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Proses Hapus Periode Akademik</title>
</head>
<body>
<?php
require_once '../database/config.php';
session_start();

$kode_periode = @$_GET['kd_prd'];
$de_kdperiode = decriptData($kode_periode);

$queryhapusprd = mysqli_query($conn, "DELETE FROM tbl_periode WHERE kode_periode='$de_kdperiode'") or die(mysqli_error($conn));

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
	swal("Berhasil", "Periode Akademik dengan Kode : <?=$kode_periode;?> berhasil dihapus", "success");
	setTimeout(function(){
		window.location.href = "../admin_master_periode";
	}, 1500);
</script>
</body>
</html>