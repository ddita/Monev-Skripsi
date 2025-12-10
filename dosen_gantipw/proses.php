<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
<?php
session_start();
require_once '../database/config.php';
//trigger button 'gantipw'
if(isset($conn, $_POST['gantipw'])) {
	//variabel untuk menampung elemen yang di post sesuai dengan atribut 'name' nya
	$user = trim(mysqli_real_escape_string($conn, $_POST['user']));
	$pwlama = sha1(trim(mysqli_real_escape_string($conn, $_POST['pwlama'])));
	$pwbaru = sha1(trim(mysqli_real_escape_string($conn, $_POST['pwbaru'])));
	//cek dari tabel pengguna berdasarkan value elem $user
	$querycekpw = mysqli_query($conn, "SELECT * FROM tbl_pengguna WHERE username = '$user'") or die(mysqli_error($conn));
	//simpan array hasil cek query pada variabel $arr
	$arr = mysqli_fetch_assoc($querycekpw);
	//jika value dari array pada kolom password tidak sama dengan $pwlama
	if($arr['password']!=$pwlama){
		//jika password lama tidak sesuai dengan inputan
		echo '<script>alert("Password lama tidak sesuai")</script>';
		echo '<script>window.location="../dosen_gantipw"</script>';
	}
	else{
		//jika password lama sesuai dengan inputan
		$queryupdatepw = mysqli_query($conn, "UPDATE tbl_pengguna SET password = '$pwbaru' WHERE username = '$user'") or die(mysqli_error($conn));
		//tampilkan alert bahwa password sudah berhasil diubah
		echo '<script>alert("Password telah diubah, silahkan login kembali menggunakan password baru anda")</script>';
		//jika password sudah berhasil diubah
		echo '<script>window.location="../login/logout.php"</script>';
	}
}
?>
</body>
</html>