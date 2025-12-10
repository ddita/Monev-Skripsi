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

if (isset($conn, $_POST['updateuniv'])) {
	$nama_univ = trim(mysqli_real_escape_string($conn, $_POST['universitas']));
	$id = "5";
	$update_univ = mysqli_query($conn, "UPDATE tbl_konfigurasi SET elemen='$nama_univ' WHERE id='$id'") or die(mysqli_error($conn));

	echo '<script>alert("Nama Universitas App berhasil di Update")</script>';
	echo '<script>window.location = "../admin_konfigurasi"</script>';
}
?>
</body>
</html>