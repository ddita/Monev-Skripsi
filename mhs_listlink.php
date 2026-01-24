<?php
$pgllogotitle = mysqli_query($conn, "SELECT * FROM tbl_konfigurasi WHERE id=2") or die(mysqli_error($conn));
$arrtitle = mysqli_fetch_array($pgllogotitle);
$logotitle = $arrtitle['nilai_konfigurasi'];
?>
<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
<!-- Font Awesome -->
<link rel="stylesheet" href="../assets_adminlte/plugins/fontawesome-free/css/all.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet" href="../assets_adminlte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<!-- iCheck -->
<link rel="stylesheet" href="../assets_adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
<!-- JQVMap -->
<link rel="stylesheet" href="../assets_adminlte/plugins/jqvmap/jqvmap.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="../assets_adminlte/dist/css/adminlte.min.css">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="../assets_adminlte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
<!-- Daterange picker -->
<link rel="stylesheet" href="../assets_adminlte/plugins/daterangepicker/daterangepicker.css">
<!-- summernote -->
<link rel="stylesheet" href="../assets_adminlte/plugins/summernote/summernote-bs4.min.css">
<link rel="shortcut icon" href="<?= $logotitle; ?>">
<link rel="stylesheet" href="../assets_adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../assets_adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="../assets_adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<!-- <link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="../assets/css/form-theme.css"> -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<!-- <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css"> -->