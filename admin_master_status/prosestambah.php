<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Proses Master Status Akademik</title>
</head>
<body>
<?php
require_once '../database/config.php';
session_start();

//trigger button tambahmhs dari halaman tambahmahasiswa.php
if(isset($conn, $_POST['tambahsts'])){
	$id = trim(mysqli_real_escape_string($conn, $_POST['id']));
	$status = trim(mysqli_real_escape_string($conn, $_POST['status']));

	$querycek = mysqli_query($conn, "SELECT * FROM tbl_status WHERE id='$id'") or die(mysqli_error($conn));
	$rvsts = mysqli_num_rows($querycek);

	if($rvsts>0) {
		?>
		<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            swal("Duplikat Data", "Status Mahasiswa dengan ID : <?=$id;?>, Status : <?=$status;?> sudah ada dalam database", "error");
            setTimeout(function(){
                window.location.href = "../admin_master_status";
            }, 1500);
        </script>
		<?php
	} else {
		$tambahstatus = mysqli_query($conn, "INSERT INTO tbl_status VALUES ('$id', '$status')") or die(mysqli_error($conn));

		?>
		<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            swal("Berhasil", "Status Mahasiswa dengan ID : <?=$id;?>, Status : <?=$status;?> berhasil ditambahkan", "success");
            setTimeout(function(){
                window.location.href = "../admin_master_status";
            }, 1500);
        </script>
        <?php
	}
}

?>
</body>
</html>