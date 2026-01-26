<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Reset Data Periode Akademik</title>
</head>
<body>
<?php
require_once '../database/config.php';
session_start();

$ambilidperiode = mysqli_query($conn, "SELECT id_periode FROM tbl_periode") or die(mysqli_error($conn));
$rvambilidperiode = mysqli_num_rows($ambilidperiode);

if($rvambilidperiode==0){
	?>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script>
		swal("Data Kosong", "Data Periode Akademik", "info");
		setTimeout(function(){
			window.location.href = "../admin_master_periode";
		}, 1500);
	</script>
	<?php
} else {
	while($data=mysqli_fetch_assoc($ambilidperiode)){
		$id_periode = $data['id_periode'];
	}
	$queryhapus_prd = mysqli_query($conn, "TRUNCATE TABLE tbl_periode") or die(mysqli_error($conn));
}

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
	swal("Berhasil", "Data Periode Akademik berhasil direset", "success");
	setTimeout(function(){
		window.location.href = "../admin_master_periode";
	}, 1500);
</script>
</body>
</html>