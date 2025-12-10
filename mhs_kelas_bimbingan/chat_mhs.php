<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Input Chat Mahasiswa</title>
</head>
<body>
<?php
require_once '../database/config.php';
session_start();

$nim = @$_GET['nim'];
$idb = @$_GET['id_kelas'];
$pro = @$_GET['progres'];
$chat_mhs = trim(mysqli_real_escape_string($conn, $_POST['pesan']));
$tgl = date('Y-m-d H:i:s');

$tambah_chat = mysqli_query($conn, "INSERT INTO tbl_detail_bimbingan (id,nim,tgl,percakapan,id_kelas_bimbingan,progres) VALUES ('','$nim','$tgl','$chat_mhs','$idb','$pro')") or die (mysqli_error($conn));
    
    echo '<script>alert("Percakapan disimpan")</script>';
    echo '<script>window.location="detailbimbingan.php?id_kelas='.$idb.'&progres='.$pro.'"</script>';   

?>
</body>
</html>