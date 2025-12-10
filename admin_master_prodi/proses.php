<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Proses Master Prodi</title>
</head>
<body>
	<?php
	session_start();
	require_once '../database/config.php';
	if (isset($_POST['tambahprodi'])) {
		$kode_prodi = trim(mysqli_real_escape_string($conn, $_POST['kodeprodi']));
		$nama_prodi = trim(mysqli_real_escape_string($conn, $_POST['nama']));
		$querycek = mysqli_query($conn, "SELECT * FROM tbl_prodi WHERE kode_prodi = '$kode_prodi' OR nama_prodi = '$nama_prodi'") or die(mysqli_error($conn));
		$returnvalue = mysqli_num_rows($querycek);
		if ($returnvalue==0) {
			mysqli_query($conn, "INSERT INTO tbl_prodi VALUES ('$kode_prodi', '$nama_prodi')") or die(mysqli_error($conn));
			echo '<script>alert("Tambah prodi telah berhasil")</script>';
			echo '<script>window.location = "../admin_master_prodi"</script>';
		} else{
			echo '<script>alert("Kode prodi sudah ada")</script>';
			echo '<script>window.location = "../admin_master_prodi/tambahprodi.php"</script>';
		}
	} else{
		$kd_prodi = @$_GET['kd_prodi'];
		if($kd_prodi!=null){
			echo '<script>alert("Data prodi dengan kode '.$kd_prodi.' berhasil dihapus")</script>';
			$qrdelprodi = mysqli_query($conn, "DELETE FROM tbl_prodi WHERE kode_prodi = '$kd_prodi' ") or die(mysqli_error($conn));
			echo '<script>window.location = "../admin_master_prodi"</script>';
		}
		$reset = @$_GET['reset'];
		if ($reset=="reset_data"){
			$queryresetprodi = mysqli_query($conn, "TRUNCATE TABLE tbl_prodi") or die(mysql_error($conn));
			echo '<script>alert("Semua data berhasil di reset")</script>';
			echo '<script>window.location = "../admin_master_prodi"</script>';
		}
	}
	?>
</body>
</html>