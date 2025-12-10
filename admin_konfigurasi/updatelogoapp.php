<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Update Logo App</title>
</head>
<body>
<?php
require '../database/config.php';
session_start();

if (isset($conn, $_POST['uplogoapp'])) {
	$file = $_FILES['logoapp']['name'];
	$ekstensi = explode(".", $file);
	$file_name = "img-logoapp".round(microtime(true)).".".end($ekstensi);
	$temp_file = $_FILES['logoapp']['tmp_name'];
	$target_dir = "../images/";
	$file_upload = $target_dir.$file_name;
	$aksi_upload = move_uploaded_file($temp_file, $file_upload);

	$idlogo = "1";
	$update_logoapp = mysqli_query($conn, "UPDATE tbl_konfigurasi SET lokasi_file='$file_upload' WHERE id='$idlogo'") or die(mysqli_error($conn));

	echo '<script>alert("Logo App berhasil di Update")</script>';
	echo '<script>window.location = "../admin_konfigurasi"</script>';
}
?>
</body>
</html>