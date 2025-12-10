<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Update Nama App</title>
</head>
<body>
<?php
require '../database/config.php';
session_start();

if (isset($conn, $_POST['gantinama'])) {
	$nama = trim(mysqli_real_escape_string($conn, $_POST['nama-app']));
	$id = "3";
	$update_nama = mysqli_query($conn, "UPDATE tbl_konfigurasi SET elemen='$nama' WHERE id='$id'") or die(mysqli_error($conn));

	echo '<script>alert("Nama App berhasil di Update")</script>';
	echo '<script>window.location = "../admin_konfigurasi"</script>';
}
?>
</body>
</html>