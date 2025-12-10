<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Proses Reset Data Status Mahasiswa</title>
</head>
<body>
<?php
require_once '../database/config.php';
session_start();

$ambilidstatus = mysqli_query($conn, "SELECT id FROM tbl_status") or die(mysqli_error($conn));
$rvambilidstatus = mysqli_num_rows($ambilidstatus);

if($rvambilidstatus==0){
	?>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script>
		swal("Data Kosong", "Data Status Mahasiswa", "info");
		setTimeout(function(){
			window.location.href = "../admin_master_status";
		}, 1500);
	</script>
	<?php
} else {
	while($data=mysqli_fetch_assoc($ambilidstatus)){
		$id = $data['id'];
	}
	$queryhapus_sts = mysqli_query($conn, "TRUNCATE TABLE tbl_status") or die(mysqli_error($conn));
}

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
	swal("Berhasil", "Data Status Mahasiswa berhasil direset", "success");
	setTimeout(function(){
		window.location.href = "../admin_master_status";
	}, 1500);
</script>
</body>
</html>