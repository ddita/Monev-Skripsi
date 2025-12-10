<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
    <?php
    session_start();
    require_once '../database/config.php';
    require '../lib/phpexcel-xls-library/vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
    // Delet Data
    $kd_dosen = @$_GET['kd_dosen'];
    $de_kddosen = decriptData($kd_dosen);
    $hapus = @$_GET['hapus'];
    if(@$hapus=='hapus'){
        echo '<script>alert("Data Dosen dengan NIDN [ '.$de_kddosen.' ] berhasil di hapus broo")</script>';
        $qrdeldosen = mysqli_query($conn, "DELETE FROM tbl_dosen WHERE nidn = '$de_kddosen'") or die (mysqli_error($conn));
        $qrdelpengguna = mysqli_query($conn, "DELETE FROM tbl_pengguna WHERE username = '$de_kddosen'") or die (mysqli_error($conn));

        echo '<script>window.location="../admin_master_dosen"</script>';
    }
    
    //reset password
    $resetpw = @$_GET['resetpw'];
    if($resetpw=='resetpw'){
        $passreset = sha1($kd_dosen);
        $qrresetpw = mysqli_query($conn, "UPDATE tbl_pengguna SET password='$passreset' WHERE username='$de_kddosen'") or die (mysqli_error($conn));

        echo '<script>alert("password dengan NIDN [ '.$de_kddosen.' ] berhasil di reset")</script>';
        echo '<script>window.location="../admin_master_dosen"</script>';
    }

    // Tambah data
    if(isset($_POST['tambahdosen'])){
        $nidn = trim(mysqli_real_escape_string($conn, $_POST['nidn']));
        $nama = trim(mysqli_real_escape_string($conn, $_POST['nama']));
        $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
        $kontak = trim(mysqli_real_escape_string($conn, $_POST['kontak']));
        $status = trim(mysqli_real_escape_string($conn, $_POST['status']));
        $password = sha1($nidn);
        $stt_dosen = "1";

        $querycek = mysqli_query($conn, "SELECT * FROM tbl_dosen WHERE nidn='$nidn'") or die (mysqli_error($conn));
        $returnvalue = mysqli_num_rows($querycek);

        if($returnvalue==0){
            mysqli_query($conn, "INSERT INTO tbl_dosen VALUES ('$nidn','$nama','$email','$kontak','$status')") or die (mysqli_error($conn));
            $querytambahdosen = mysqli_query($conn, "INSERT INTO tbl_pengguna VALUES ('$nidn','$password','$nama','$stt_dosen')") or die (mysqli_error($conn));

            echo '<script>alert("Dosen dengan NIDN [ '.$nidn.' ] atas nama [ '.$nama.' ] berhasil ditambahkan")</script>';
            echo '<script>window.location="../admin_master_dosen"</script>';
        } else{
            echo '<script>alert("Dosen dengan NIDN [ '.$nidn.' ] sudah ada dalam database")</script>';
            echo '<script>window.location="../admin_master_dosen/tambahdosen.php"</script>';
        }
    }

    // reset data
    $reset = @$_GET['reset'];
    if($reset=="reset_data"){

    // ambil nidn dari tabel dosen
        $querynidndosen = mysqli_query($conn, "SELECT * FROM tbl_dosen") or die (mysqli_error($conn));
    //return value
        $returnvalue = mysqli_num_rows($querynidndosen);

        if($returnvalue>0){
        // proses perulangan sebanyak record yang ditentukan pada database
            while($data = mysqli_fetch_assoc($querynidndosen)){
            // menampung nidn pada setiap perulangan di dalam variabel $nidn_dosen
                $nidn_dosen = $data['nidn'];
                // mengapus data berdasarkan nidn pada setiap perulangan
                $qrdelpengguna = mysqli_query($conn, "DELETE FROM tbl_pengguna WHERE username = '$nidn_dosen'") or die (mysqli_error($conn));
            }
        } else{

        }

        $queryresetdosen = mysqli_query($conn,"TRUNCATE TABLE tbl_dosen") or die (mysqli_error($conn));

        echo '<script>alert("Semua data sudah berhasil di reset boyyy... keren")</script>';
        echo '<script>window.location = "../admin_master_dosen"</script>';
    }
    ?>

    <?php
    //trigger post dari button name=importdosen di halaman index.php
    if (isset($_POST['importdosen'])) {
        //$file merupakan variabel untuk menampung nama file yang di upload
        $file = $_FILES['file']['name'];
        //memisahkan ekstensi file yang di upload
        $ekstensi = explode (".", $file);
        //variabel file_name untuk merename file yang yang diupload dengan nama file roundmicrotime(tgl+jam+menit+detik+milidetik) + ekstensi
        $file_name = "file".round(microtime(true)).".".end($ekstensi);
        //$sumber mengambl nama file yang sudah diubah dengan round microtime secara temporer/temporary [temp_name]
        $sumber = $_FILES['file']['tmp_name'];
        //direktori untuk upload file
        $target_dir ="template/";
        //menentukan direktori file setelah diupload beserta nama file baru yang dimdifikasi dengan round microtime
        $target_file = $target_dir.$file_name;
        //upload file ke direktori atau folder "file-import"
        $upload = move_uploaded_file($sumber, $target_file);
        //load file excel yang telah diupload
        $file_excel = PHPExcel_IOFactory::load($target_file);
        $data_excel = $file_excel->getActiveSheet()->toArray(null, true,true,true);

        for ($j=2; $j <= count($data_excel); $j++) {
            $nidn          = $data_excel[$j]['B'];
            $nama          = addslashes($data_excel[$j]['C']);
            $nohp          = $data_excel[$j]['D'];
            $kelamin       = $data_excel[$j]['E'];
            $stat          = $data_excel[$j]['F'];
            $pass          = sha1($nidn);
            $st_pengguna   = '1';

            $cekdosen = mysqli_query($conn, "SELECT nidn FROM tbl_dosen WHERE nidn='$nidn'") or die(mysqli_error($conn));
            $rvd= mysqli_num_rows($cekdosen);
            if($rvd>0){

            } else{
                $kosong = "";
                $tambahdosen = mysqli_query($conn, "INSERT INTO tbl_dosen VALUES ('$nidn','$nama','$email','$kontak','$status')") or die(mysqli_error($conn));
                $hapusdosenkosong = mysqli_query($conn, "DELETE FROM tbl_dosen WHERE nidn='$kosong'") or die(mysqli_error($conn));
                $tambahpenggunadosen = mysqli_query($conn, "INSERT INTO tbl_pengguna VALUES ('$nidn','$pass','$nama','$st_pengguna')") or die(mysqli_error($conn));
                $hapus_pg_dosenkosong = mysqli_query($conn, "DELETE FROM tbl_pengguna WHERE username='$kosong'") or die(mysqli_error($conn));
            }
        }
        unlink($target_file);
        ?>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script>
            swal("Berhasil", "Semua data dosen berhasil di import", "success")
            setTimeout(function(){
                window.location.href = "../admin_master_dosen";
            }, 1500);
        </script>
        <?php
    }
    ?>
</body>
</html>