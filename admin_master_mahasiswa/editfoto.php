<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Update Foto Mahasiswa</title>
</head>
<body>
<?php
require '../database/config.php';
session_start();

if (isset($conn, $_POST['editfoto'])) {
	$file = $_FILES['fotomhs']['name'];
	$ekstensi = explode(".", $file);
	$file_name = "img-fotomahasiswa".$file;
	$temp_file = $_FILES['fotomhs']['tmp_name'];
	$target_dir = "../images/";
	$file_upload = $target_dir.$file_name;
	$aksi_upload = move_uploaded_file($temp_file, $file_upload);

	$nim = trim(mysqli_real_escape_string($conn, $_POST['nim']));
	$update_foto_mhs = mysqli_query($conn, "UPDATE tbl_mahasiswa SET foto='$file_upload' WHERE nim='$nim'") or die(mysqli_error($conn));

	echo '<script>alert("Foto Mahasiswa berhasil di Update")</script>';
	echo '<script>window.location = "../admin_master_mahasiswa"</script>';
}
?>
</body>
</html>