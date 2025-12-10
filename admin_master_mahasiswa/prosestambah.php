<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Proses Master Data Mahasiswa</title>
</head>
<body>
<?php
require_once '../database/config.php';
session_start();

//trigger button tambahmhs dari halaman tambahmahasiswa.php
if(isset($conn, $_POST['tambahmhs'])){
	$nim = trim(mysqli_real_escape_string($conn, $_POST['nim']));
	$nama = trim(mysqli_real_escape_string($conn, $_POST['nama']));
	$prodi = trim(mysqli_real_escape_string($conn, $_POST['prodi']));
	$status = trim(mysqli_real_escape_string($conn, $_POST['status']));
	$angkatan = trim(mysqli_real_escape_string($conn, $_POST['angkatan']));
	$kontak = trim(mysqli_real_escape_string($conn, $_POST['kontak']));
	$kelamin = trim(mysqli_real_escape_string($conn, $_POST['kelamin']));
	$pass = sha1($nim);
	$st_mhs = "2";

	$querycek = mysqli_query($conn, "SELECT nim FROM tbl_mahasiswa WHERE nim=$nim") or die(mysqli_error($conn));
	$rvmhs = mysqli_num_rows($querycek);

	if($rvmhs>0) {
		?>
		<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            swal("Duplikat Data", "Data Mahasiswa dengan NIM : <?=$nim;?>, nama mahasiswa : <?=$nama;?> sudah ada dalam database", "error");
            setTimeout(function(){
                window.location.href = "../admin_master_mahasiswa";
            }, 1500);
        </script>
		<?php
	} else {
		$tambahmhs = mysqli_query($conn, "INSERT INTO tbl_mahasiswa VALUES ('$nim', '$nama', '$prodi', '$status', '$angkatan', '$kontak', '$kelamin')") or die(mysqli_error($conn));
		$tambah_pg_mhs = mysqli_query($conn, "INSERT INTO tbl_pengguna VALUES ('$nim', '$pass', '$nama', '$st_mhs')") or die(mysqli_error($conn));

		?>
		<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            swal("Berhasil", "Data Mahasiswa dengan NIM : <?=$nim;?>, nama mahasiswa : <?=$nama;?> berhasil ditambahkan", "success");
            setTimeout(function(){
                window.location.href = "../admin_master_mahasiswa";
            }, 1500);
        </script>
        <?php
	}
}

?>
</body>
</html>