<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Update Foto Dosen</title>
</head>
<body>
<?php
require '../database/config.php';
session_start();

if (isset($conn, $_POST['editfoto'])) {
	$file = $_FILES['fotodosen']['name'];
	$ekstensi = explode(".", $file);
	$file_name = "img-fotodosen".round(microtime(true)).".".end($ekstensi);
	$temp_file = $_FILES['fotodosen']['tmp_name'];
	$target_dir = "../images/";
	$file_upload = $target_dir.$file_name;
	$aksi_upload = move_uploaded_file($temp_file, $file_upload);

	$nidn = trim(mysqli_real_escape_string($conn, $_POST['nidn']));
	$update_foto_dosen = mysqli_query($conn, "UPDATE tbl_dosen SET foto='$file_upload' WHERE nidn='$nidn'") or die(mysqli_error($conn));

	echo '<script>alert("Foto dosen berhasil di Update")</script>';
	echo '<script>window.location = "../admin_master_dosen"</script>';
}
?>
</body>
</html>