<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Proses Master Angkatan</title>
</head>
<body>
	<?php
	session_start();
	require_once '../database/config.php';
	if (isset($_POST['tambahangkatan'])) {
		$kode_angkatan = trim(mysqli_real_escape_string($conn, $_POST['kodeangkatan']));
		$keterangan = trim(mysqli_real_escape_string($conn, $_POST['keterangan']));
		$querycek = mysqli_query($conn, "SELECT * FROM tbl_angkatan WHERE kode_angkatan = '$kode_angkatan' OR keterangan = '$keterangan'") or die(mysqli_error($conn));
		$returnvalue = mysqli_num_rows($querycek);
		if ($returnvalue==0) {
			mysqli_query($conn, "INSERT INTO tbl_angkatan VALUES ('$kode_angkatan', '$keterangan')") or die(mysqli_error($conn));
			echo '<script>alert("Tambah data angkatan telah berhasil")</script>';
			echo '<script>window.location = "../admin_master_angkatan"</script>';
		} else{
			echo '<script>alert("Kode angkatan sudah ada")</script>';
			echo '<script>window.location = "../admin_master_angkatan/tambahangkatan.php"</script>';
		}
	} else{
		$kd_angkatan = @$_GET['kd_angkatan'];
		$kode_akt = decriptData($kd_angkatan);
		if($kd_angkatan!=null){
			echo '<script>alert("Data Angkatan dengan kode '.$kode_akt.' berhasil dihapus")</script>';
			$qrdelangkatan = mysqli_query($conn, "DELETE FROM tbl_angkatan WHERE kode_angkatan = '$kode_akt' ") or die(mysqli_error($conn));
			echo '<script>window.location = "../admin_master_angkatan"</script>';
		}
		else {
			echo '<script>alert("Tidak ada data yang terpilih")</script>';
			echo '<script>window.location = "../admin_master_angkatan"</script>';
		}
	}
	?>
</body>
</html>
