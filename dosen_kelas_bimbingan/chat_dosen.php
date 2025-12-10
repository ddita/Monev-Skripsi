<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Input Chat Dosen</title>
</head>
<body>
<?php
require_once '../database/config.php';
session_start();

$nidn = @$_GET['nidn'];
$de_nidn = decriptData($nidn);
$idb = @$_GET['id_kelas'];
$de_idb = decriptData($idb);
$pro = @$_GET['progres'];
$de_pro = decriptData($pro);
$chat_dosen = trim(mysqli_real_escape_string($conn, $_POST['pesan']));
$tgl = date('Y-m-d H:i:s');

$tambah_chat = mysqli_query($conn, "INSERT INTO tbl_detail_bimbingan (id,nidn,tgl,percakapan,id_kelas_bimbingan,progres) VALUES ('','$de_nidn','$tgl','$chat_dosen','$de_idb','$de_pro')") or die (mysqli_error($conn));
    
    echo '<script>alert("Percakapan disimpan")</script>';
    echo '<script>window.location="detailbimbingan.php?id_kelas='.$de_idb.'&progres='.$de_pro.'"</script>';   

?>
</body>
</html>