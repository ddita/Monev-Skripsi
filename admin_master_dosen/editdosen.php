<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dosen</title>
</head>

<body>
    <?php
    require_once '../database/config.php';

    if (isset($_POST['editdosen'])) {
        $nidn = $_POST['nidn'];
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $kontak = $_POST['kontak'];
        $status = $_POST['status'];

        $query = "UPDATE tbl_dosen SET nama='$nama', email='$email', kontak='$kontak', status='$status' WHERE nidn='$nidn'";

        if (mysqli_query($conn, $query)) {
            echo '<script>alert("Data berhasil diperbarui");
            window.location.href="../admin_master_dosen";
              </script>';
        } else {
            echo '<script>
                alert("Data gagal diperbarui");
                window.location.href="../admin_master_dosen";
              </script>';
        }
    }
    ?>

</body>
</html>