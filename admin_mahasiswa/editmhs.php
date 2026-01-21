<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    require_once '../database/config.php';

    if (isset($_POST['editmhs'])) {
        $nim = $_POST['nim'];
        $nama = $_POST['nama'];
        $prodi = $_POST['prodi'];
        $status = $_POST['status'];
        $angkatan = $_POST['angkatan'];
        $kontak = $_POST['kontak'];
        $kelamin = $_POST['kelamin'];

        $query = "UPDATE tbl_mahasiswa SET nama='$nama', prodi='$prodi', status='$status', angkatan='$angkatan', kontak='$kontak', kelamin='$kelamin' WHERE nim='$nim'";

        if (mysqli_query($conn, $query)) {
            echo '<script>alert("Data berhasil diperbarui");
            window.location.href="../admin_master_mahasiswa";
              </script>';
        } else {
            echo '<script>
                alert("Data gagal diperbarui");
                window.location.href="../admin_master_mahasiswa";
              </script>';
        }
    }
    ?>

</body>
</html>