<?php
function getConfig($conn, $id) {
  $q = mysqli_query($conn, "SELECT nilai_konfigurasi FROM tbl_konfigurasi WHERE id='$id'");
  $d = mysqli_fetch_assoc($q);
  return $d['nilai_konfigurasi'] ?? '';
}

$namaapp = getConfig($conn, 1); // nama_aplikasi
$univapp = getConfig($conn, 4); // nama_universitas
$tahun   = date('Y');
?>

<footer class="main-footer text-center">
  <strong>
    &copy; <?= $tahun; ?>
    <?= htmlspecialchars($univapp); ?> – 
    <?= htmlspecialchars($namaapp); ?>
  </strong>
  . All rights reserved.

  <div class="float-right d-none d-sm-inline-block">
    <b>Version</b> 1.0
  </div>
</footer>
