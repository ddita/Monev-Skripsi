<?php
session_start();
$konstruktor = 'admin_mahasiswa';
require_once '../database/config.php';

/* ================= CEK LOGIN ================= */
if (!isset($_SESSION['role'])) {
    header("Location: ../login/logout.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login/logout.php");
    exit;
}

/* ================= HELPER ================= */
function decriptData($data)
{
    $key = 'monev_skripsi_2024';
    return openssl_decrypt(
        base64_decode(urldecode($data)),
        'AES-128-ECB',
        $key
    );
}

/* ================= VALIDASI PARAM ================= */
if (!isset($_GET['nim'])) {
    header("Location: ../admin_mahasiswa");
    exit;
}

$nim = decriptData($_GET['nim']);

/* ================= DATA MAHASISWA ================= */
$qMhs = mysqli_query($conn, "SELECT * FROM tbl_mahasiswa WHERE nim='$nim'");
$mhs  = mysqli_fetch_assoc($qMhs);

if (!$mhs) {
    die("Data mahasiswa tidak ditemukan");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Monev Skripsi | Edit Mahasiswa</title>

    <?php include '../mhs_listlink.php'; ?>

    <script>
        (function() {
            const theme = localStorage.getItem("theme") || "dark";
            document.documentElement.classList.add(theme + "-mode");
        })();
    </script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">

    <div class="wrapper">
        <?php include '../mhs_navbar.php'; ?>
        <?php include '../admin_sidebar.php'; ?>

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="../images/UP.png" alt="Monev-Skripsi" height="60" width="60">
        </div>

        <div class="content-wrapper">

            <!-- HEADER -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Edit Mahasiswa</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="../admin_mahasiswa">Mahasiswa</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CONTENT -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-6">

                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-user-edit"></i> Edit Data Mahasiswa
                                    </h3>
                                </div>

                                <form action="prosesedit.php" method="POST">
                                    <div class="card-body">

                                        <input type="hidden" name="nim" value="<?= $mhs['nim']; ?>">

                                        <div class="form-group">
                                            <label>NIM</label>
                                            <input type="text" class="form-control" value="<?= $mhs['nim']; ?>" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>Nama Mahasiswa</label>
                                            <input type="text" name="nama" class="form-control"
                                                value="<?= htmlspecialchars($mhs['nama']); ?>" required>
                                        </div>

                                        <!-- PRODI -->
                                        <div class="form-group">
                                            <label>Program Studi</label>
                                            <select name="prodi" class="form-control" required>
                                                <?php
                                                $qProdi = mysqli_query($conn, "SELECT * FROM tbl_prodi");
                                                while ($p = mysqli_fetch_assoc($qProdi)) {
                                                    $sel = ($p['kode_prodi'] == $mhs['prodi']) ? 'selected' : '';
                                                    echo "<option value='{$p['kode_prodi']}' $sel>
                                                        {$p['kode_prodi']} - {$p['nama_prodi']}
                                                      </option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- STATUS SKRIPSI -->
                                        <div class="form-group">
                                            <label>Status Skripsi</label>
                                            <select name="status_skripsi" class="form-control" required>
                                                <?php
                                                $qStatus = mysqli_query($conn, "SELECT * FROM tbl_status");
                                                while ($s = mysqli_fetch_assoc($qStatus)) {
                                                    $sel = ($s['id'] == $mhs['status_skripsi']) ? 'selected' : '';
                                                    echo "<option value='{$s['id']}' $sel>
                                                        {$s['status']}
                                                      </option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- DOSEN -->
                                        <div class="form-group">
                                            <label>Dosen Pembimbing</label>
                                            <select name="nip_dosen" class="form-control" required>
                                                <?php
                                                $qDosen = mysqli_query($conn, "SELECT nip, nama_dosen FROM tbl_dosen");
                                                while ($d = mysqli_fetch_assoc($qDosen)) {
                                                    $sel = ($d['nip'] == $mhs['dosen_pembimbing']) ? 'selected' : '';
                                                    echo "<option value='{$d['nip']}' $sel>{$d['nama_dosen']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- ANGKATAN -->
                                        <div class="form-group">
                                            <label>Angkatan</label>
                                            <select name="angkatan" class="form-control" required>
                                                <?php
                                                $qAng = mysqli_query($conn, "SELECT kode_angkatan FROM tbl_angkatan ORDER BY kode_angkatan DESC");
                                                while ($a = mysqli_fetch_assoc($qAng)) {
                                                    $sel = ($a['kode_angkatan'] == $mhs['angkatan']) ? 'selected' : '';
                                                    echo "<option value='{$a['kode_angkatan']}' $sel>{$a['kode_angkatan']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- AKTIF -->
                                        <div class="form-group">
                                            <label>Status Mahasiswa</label>
                                            <select name="aktif" class="form-control">
                                                <option value="1" <?= $mhs['aktif'] == 1 ? 'selected' : ''; ?>>Aktif</option>
                                                <option value="0" <?= $mhs['aktif'] == 0 ? 'selected' : ''; ?>>Nonaktif</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" name="editmhs" class="btn btn-warning btn-block">
                                            <i class="fas fa-save"></i> Simpan Perubahan
                                        </button>
                                        <a href="../admin_mahasiswa" class="btn btn-secondary btn-block">Batal</a>
                                    </div>

                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include '../footer.php'; ?>
    </div>

    <?php include '../mhs_script.php'; ?>

</body>

</html>