<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>Hapus Periode Akademik</title>
</head>

<body>

	<?php
	session_start();
	require_once '../database/config.php';
	require_once '../helpers/crypto.php';

	/* =========================
   CEK ROLE ADMIN
   ========================= */
	if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
		header("Location: ../login/logout.php");
		exit;
	}

	/* =========================
   VALIDASI PARAMETER
   ========================= */
	if (!isset($_GET['kd_prd'])) {
		echo "Parameter tidak valid";
		exit;
	}

	$id_periode_enkrip = $_GET['kd_prd'];
	$id_periode = decriptData($id_periode_enkrip);

	if (!$id_periode) {
		echo "Data tidak valid";
		exit;
	}

	/* =========================
   PROSES HAPUS
   ========================= */
	$query = mysqli_query(
		$conn,
		"DELETE FROM tbl_periode WHERE id_periode='$id_periode'"
	);

	?>

	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<script>
		swal({
			title: "Berhasil",
			text: "Periode Akademik berhasil dihapus",
			icon: "success",
			button: false
		});

		setTimeout(function() {
			window.location.href = "../admin_master_periode";
		}, 1500);
	</script>

</body>

</html>