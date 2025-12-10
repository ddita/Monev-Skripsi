<tr>
  <td>
    <?=$no++;?>
  </td>
  <td><?=$datakelasbimbingan['nidn'];?></td>
  <td>
    <?php
    $idkelasbimbingan = $datakelasbimbingan['id_kelas'];
    $qrjumlah = mysqli_query($conn, "SELECT id_kelas FROM tbl_mhs_bimbingan WHERE id_kelas = '$idkelasbimbingan'") or die(mysqli_error($conn));
    $jumlah = mysqli_num_rows($qrjumlah);
    echo $jumlah;
    ?>
  </td>
  <td>
    <?php
    $id_kelas = $datakelasbimbingan['id_kelas'];
    $qrprogres = mysqli_query($conn, "SELECT * FROM tbl_mhs_bimbingan WHERE id_kelas = '$id_kelas'") or die(mysqli_error($conn));
    $cekqrprogres = mysqli_num_rows($qrprogres);
    $rata_rata_presentase = 0;

    if($cekqrprogres>0) {
      $total_presentase = 0;
      while($dataprogres = mysqli_fetch_array($qrprogres)) {
        $id_progres = $dataprogres['id_progres'];
        $qrpresentase = mysqli_query($conn,"SELECT presentase FROM tbl_progres WHERE id_progres = '$id_progres'") or die(mysqli_error($conn));
        $datapresentase = mysqli_fetch_assoc($qrpresentase);
        $presentase = $datapresentase['presentase'];
        $total_presentase += $presentase;
      }
      $rata_rata_presentase = $total_presentase/$cekqrprogres;
    }
    $rata_rata_presentase = number_format($rata_rata_presentase,2);
    echo $rata_rata_presentase.'%';
    ?>
  </td>
  <td>Status</td>
  <td>
    <center>
      <a href="detail.php?id_kelas=<?=$datakelasbimbingan['id_kelas'];?>" class="btn btn-sm btn-info">
        <i class="nav-icon fas fa-edit"></i> Detail</a>
    </center>
  </td>
</tr>