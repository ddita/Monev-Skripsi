<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title> Admin Import Data </title>
</head>
<body>
<?php
require_once '../database/config.php';
require '../lib/phpexcel-xls-library/vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
session_start();
error_reporting(0);

//ambil trigger tombol importmahasiswa
if (isset($conn, $_POST['importmhs'])) {
	//buat variabel untuk menampung value nama file dari elemen file pada index(modal import)
	$file = $_FILES['file']['name'];

	//buat variabel untuk memisahkan ekstensi file dengan nama filenya
	$ekstensi = explode(".", $file);

	//buat variabel untuk merename file dengan nama baru
	$file_name = "file".round(microtime(true)).".".end($ekstensi);

	//buat variabel untuk menampung file temporary dari file yang diupload
	$sumber = $_FILES['file']['tmp_name'];

	//deklarasikan variabel direktori untuk mengupload file
	$target_dir = "file-import/";

	//buat variabel untuk mengarahkan file ke target direktori
	$target_file = $target_dir.$file_name;

	//buat variabel yang berisikan perintah untuk mengupload file ke target direktori
	$upload = move_uploaded_file($sumber, $target_file);

	//identifikasi file ecel yang akan digunakan sebagai referensi import
	$file_excel = PHPExcel_IOFactory::load($target_file);

	//identifikasi sheet pada excel yang sedang aktif
	$data_excel = $file_excel->getActiveSheet()->toArray(null, true,true,true);

	for ($i=2; $i<= count($data_excel); $i++){
		//deklarasi perulangan
		$nim      = $data_excel[$i]['B'];
		$nama     = addslashes($data_excel[$i]['C']);
		$prodi    = $data_excel[$i]['D'];
		$status   = $data_excel[$i]['E'];
		$angkatan = $data_excel[$i]['F'];
		$kontak   = $data_excel[$i]['G'];
		$kelamin  = $data_excel[$i]['H'];
		$pass     = md5($nim);
		$st_mhs = "2";

		$cekmhs = mysqli_query($conn, "SELECT nim FROM tbl_mahasiswa WHERE nim='$nim'") or die(mysqli_error($conn));

		$rvmhs = mysqli_num_rows($cekmhs);

		if($rvmhs>0){

		} else {
			$kosong = "";
			$tambahmhs = mysqli_query($conn, "INSERT INTO tbl_mahasiswa VALUES ('$nim', '$nama', '$prodi', '$status', '$angkatan', '$kontak', '$kelamin' )") or die(mysqli_error($conn));
			$delkosong = mysqli_query($conn, "DELETE FROM tbl_mahasiswa WHERE nim ='$kosong'") or die(mysqli_error($conn));
			$tambahpenggunamhs = mysqli_query($conn, "INSERT INTO tbl_pengguna VALUES ('$nim', '$pass', '$nama', '$st_mhs')") or die(mysqli_error($conn));
			$delpenggunakosong = mysqli_query($conn, "DELETE FROM tbl_pengguna WHERE username ='$kosong'") or die(mysqli_error($conn));
		}
	}
	unlink($target_file);

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
    	swal("Berhasil", "Semua Data Mahasiswa Berhasil diimport", "success");
    	setTimeout(function() {
    		window.location.href = "../admin_master_mahasiswa";
    	}, 1500);
    </script>
    <?php
}
?>
</body>
</html>