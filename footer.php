<?php
$pglnama = mysqli_query($conn, "SELECT * FROM tbl_konfigurasi WHERE id=3") or die(mysqli_error($conn));
        $arrnama = mysqli_fetch_assoc($pglnama);
        $namaapp = $arrnama['elemen'];

        $pglcopy = mysqli_query($conn, "SELECT * FROM tbl_konfigurasi WHERE id=4") or die(mysqli_error($conn));
        $arrcopy = mysqli_fetch_assoc($pglcopy);
        $copyapp = $arrcopy['elemen'];

        $pgluniv = mysqli_query($conn, "SELECT * FROM tbl_konfigurasi WHERE id=5") or die(mysqli_error($conn));
        $arruniv = mysqli_fetch_assoc($pgluniv);
        $univapp = $arruniv['elemen'];
?>

<footer class="main-footer">
    <strong>Copyright &copy; <?=$copyapp;?> <?php echo date ('Y'); ?> <?=$namaapp;?> <?=$univapp;?>.<a href="https://adminlte.io">Monitoring Evaluasi Skripsi</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0
    </div>
  </footer>