<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Proses Periode Akademik</title>
</head>
<body>
<?php
require_once '../database/config.php';
session_start();

//trigger button tambahmhs dari halaman tambahmahasiswa.php
if(isset($conn, $_POST['tambahprd'])){
	$id_periode = trim(mysqli_real_escape_string($conn, $_POST['id_periode']));
	$periode = trim(mysqli_real_escape_string($conn, $_POST['nama_periode']));
	$tahun_akademik = trim(mysqli_real_escape_string($conn, $_POST['tahun_akademik']));
	$semester = trim(mysqli_real_escape_string($conn, $_POST['semester']));

	$querycek = mysqli_query($conn, "SELECT * FROM tbl_periode WHERE id_periode='$id_periode'") or die(mysqli_error($conn));
	$rvprd = mysqli_num_rows($querycek);

	if($rvprd>0) {
		?>
		<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            swal("Duplikat Data", "Periode Akademik  dengan ID : <?=$id_periode;?>, periode : <?=$periode;?> sudah ada dalam database", "error");
            setTimeout(function(){
                window.location.href = "../admin_master_periode";
            }, 1500);
        </script>
		<?php
	} else {
		$tambahperiode = mysqli_query($conn, "INSERT INTO tbl_periode VALUES ('$id_periode', '$periode', '$tahun_akademik', '$semester')") or die(mysqli_error($conn));

		?>
		<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            swal("Berhasil", "Periode Akademik dengan Id : <?=$id_periode;?>, Periode : <?=$periode;?> berhasil ditambahkan", "success");
            setTimeout(function(){
                window.location.href = "../admin_master_periode";
            }, 1500);
        </script>
        <?php
	}
}

?>
</body>
</html>