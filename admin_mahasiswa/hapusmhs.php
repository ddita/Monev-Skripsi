<?php
require_once '../database/config.php';
session_start();

// Tambahkan fungsi dekripsi
function decriptData($data)
{
	$key = 'monev_skripsi_2024';
	return openssl_decrypt(base64_decode(urldecode($data)), 'AES-128-ECB', $key);
}

// Ambil NIM dari URL
$nim = @$_GET['nim'];
$de_nim = decriptData($nim);

// Hapus data mahasiswa dan user
$qmhs = mysqli_query($conn, "DELETE FROM tbl_mahasiswa WHERE nim='$de_nim'") or die(mysqli_error($conn));
$qusers = mysqli_query($conn, "DELETE FROM tbl_users WHERE username='$de_nim'") or die(mysqli_error($conn));
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Hapus Mahasiswa</title>
</head>

<body>
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script>
		swal("Berhasil", "Data Mahasiswa dengan NIM : <?= $de_nim; ?> berhasil dihapus", "success");
		setTimeout(function() {
			window.location.href = "../admin_mahasiswa";
		}, 1500);
	</script>
</body>

</html>