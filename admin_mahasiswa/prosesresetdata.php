<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Proses Reset Data Mahasiswa</title>
</head>
<body>
<?php
require_once '../database/config.php';
session_start();

$ambilnimmhs = mysqli_query($conn, "SELECT nim FROM tbl_mahasiswa") or die(mysqli_error($conn));
$rvambilnimmhs = mysqli_num_rows($ambilnimmhs);

if($rvambilnimmhs==0){
	?>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script>
		swal("Data Kosong", "Data Mahasiswa Kosong", "info");
		setTimeout(function(){
			window.location.href = "../admin_master_mahasiswa";
		}, 1500);
	</script>
	<?php
} else {
	while($data=mysqli_fetch_assoc($ambilnimmhs)){
		$nim = $data['nim'];
		$queryhapus_pg_mhs = mysqli_query($conn, "DELETE FROM tbl_pengguna WHERE username='$nim'") or die(mysqli_error($conn));
	}
	$queryhapus_mhs = mysqli_query($conn, "TRUNCATE TABLE tbl_mahasiswa") or die(mysqli_error($conn));
}

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script>
	swal("Berhasil", "Data Mahasiswa berhasil direset", "success");
	setTimeout(function(){
		window.location.href = "../admin_master_mahasiswa";
	}, 1500);
</script>
</body>
</html>