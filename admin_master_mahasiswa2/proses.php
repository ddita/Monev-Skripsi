<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" conntent="width=device-width, initial-scale=1">
	<title>Proses Master Mahasiswa</title>
</head>
<body>
	<?php
	session_start();
	require_once '../database/config.php';
	if (isset($_POST['tambahmhs'])) {
		$nim = trim(mysqli_real_escape_string($conn, $_POST['nim']));
		$nama = trim(mysqli_real_escape_string($conn, $_POST['nama']));
		$prodi = trim(mysqli_real_escape_string($conn, $_POST['prodi']));
		$status = trim(mysqli_real_escape_string($conn, $_POST['status']));
		$angkatan = trim(mysqli_real_escape_string($conn, $_POST['angkatan']));
		$kontak = trim(mysqli_real_escape_string($conn, $_POST['kontak']));
		$kelamin = trim(mysqli_real_escape_string($conn, $_POST['kelamin']));
		$password = sha1('$nim');
		$stt_mhs = "2";

		$querycek = mysqli_query($conn, "SELECT * FROM tbl_mahasiswa WHERE nim = '$nim'") or die(mysqli_error($conn));
		$returnvalue = mysqli_num_rows($querycek);

		if ($returnvalue>1) {
			echo '<script>alert("Dosen dengan nim ['.$nim.'] tersebut sudah ada dalam database")</script>';
			echo '<script>window.location = "../admin_master_mahasiswa/tambahmhs.php"</script>';
		} else{
			$querytambah = mysqli_query($conn, "INSERT INTO  tbl_mahasiswa VALUES ('$nim', '$nama', '$prodi', '$status', '$angkatan', '$kontak', '$kelamin')") or die(mysqli_error($conn));
			$queytambahmhs= mysqli_query($conn, "INSERT INTO tbl_pengguna VALUES ('$nim', '$password', '$nama', '$stt_mhs')") or die (mysqli_error($conn));
			echo '<script>alert("Dosen dengan NIM ['.$nim.'] atas nama ['.$nama.'] berhasil ditambahkan")</script>';
			echo '<script>window.location = "../admin_master_mahasiswa"</script>';
		}
	} else{
		$kd_mhs = @$_GET['kd_mhs'];
		$hapus = @$_GET['hapus'];
		if ($hapus=='hapus'){
			echo '<script>alert("Data mahasiswa dengan NIM ['.$kd_mhs.'] berhasil di hapus!!")</script>';
			$qrdelmhs = mysqli_query($conn, "DELETE FROM tbl_mahasiswa WHERE nim='$kd_mhs'") or die (mysqli_error($conn));
			$qrdelpenggunamhs = mysqli_query($conn, "DELETE FROM tbl_pengguna WHERE username='$kd_mhs'") or die (mysqli_error($conn));
			echo '<script>window.location="../admin_master_mahasiswa"</script>';
		}

		//reset pw
		$resetpw = @$_GET['resetpw'];
		if ($resetpw=='resetpw'){
			$passreset = sha1($kd_mhs);
			$qrresetpw = mysqli_query($conn, "UPDATE tbl_pengguna SET password='$passreset' WHERE username='$kd_mhs' ") or die (mysqli_error($conn));
			echo '<script>alert("Password Dosen dengan NIDN ['.$kd_mhs.'] berhasil reset")</script>';
			echo '<script>window.location="../admin_master_dosen"</script>';
		}

		//reset data
		$reset = @$_GET['reset'];
		if ($reset=="reset_data"){

			//ambil nidn dosen dari tabel dosen
			$qrnimmhs = mysqli_query($conn, "SELECT nim FROM tbl_mahasiswa") or die(mysqli_error($conn));
			//retun value
			$rvmhs = mysqli_num_rows($qrnimmhs);

			if($rvmhs>0){
				while($data=mysqli_fetch_assoc($qrnimmhs)){
					//menampung nidn pada setiap perulangan di dalam variabel $nidn_dosen
					$nim_mhs = $data['nim'];
					//menghapus data berdasarkan nidn pada setiap perulangan
					$qrdelpengguna = mysqli_query($conn, "DELETE FROM tbl_pengguna WHERE username='$nim_mhs'") or die(mysql_error($conn));
				}
			} else{

			}

			$queryresetmhs = mysqli_query($conn, "TRUNCATE TABLE tbl_mahasiswa") or die(mysql_error($conn));
			echo '<script>alert("Semua data berhasil di reset")</script>';
			echo '<script>window.location = "../admin_master_mahasiswa"</script>';
		}

	}
	?>
</body>
</html>