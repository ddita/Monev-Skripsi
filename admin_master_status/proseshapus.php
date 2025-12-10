<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Proses Hapus Status Mahasiswa</title>
</head>
<body>
<?php
require_once '../database/config.php';
session_start();

$id = @$_GET['kd_sts'];
$de_id = decriptData($id);

$queryhapussts = mysqli_query($conn, "DELETE FROM tbl_status WHERE id='$de_id'") or die(mysqli_error($conn));

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
	swal("Berhasil", "Status Mahasiswa dengan ID : <?=$de_id;?> berhasil dihapus", "success");
	setTimeout(function(){
		window.location.href = "../admin_master_status";
	}, 1500);
</script>
</body>
</html>