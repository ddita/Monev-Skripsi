<?php
session_start();
$konstruktor = 'admin_dosen';
require_once '../database/config.php';

/* ================= CEK LOGIN ================= */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/logout.php");
    exit;
}

/* ================= DEKRIP ================= */
function decriptData($data)
{
    $key = 'monev_skripsi_2024';
    return openssl_decrypt(
        base64_decode(urldecode($data)),
        'AES-128-ECB',
        $key
    );
}

if (!isset($_GET['nip'])) {
    die('NIP tidak ditemukan');
}

$nip_enkripsi = $_GET['nip'];
$nip = decriptData($nip_enkripsi);

if (!$nip) {
    die('NIP tidak valid');
}

$nip = mysqli_real_escape_string($conn, $nip);


/* ================= AMBIL DATA DOSEN ================= */
$qDosen = mysqli_query($conn, "SELECT d.nip, d.nama_dosen, d.aktif, u.status AS status_user FROM tbl_dosen d
    LEFT JOIN tbl_users u ON u.username = d.nip WHERE d.nip = '$nip'
");
$dosen = mysqli_fetch_assoc($qDosen);

if (!$dosen) {
    die('Data dosen tidak ditemukan');
}

/* ================= HITUNG BEBAN ================= */
$qBeban = mysqli_query($conn, "SELECT COUNT(*) total FROM tbl_mahasiswa WHERE dosen_pembimbing='$nip'
            AND aktif=1
            AND id_status != 6
");
$beban = mysqli_fetch_assoc($qBeban)['total'];

if ($beban > 8) {
    $label = 'Overload';
    $badge = 'danger';
} elseif ($beban >= 6) {
    $label = 'Padat';
    $badge = 'warning';
} else {
    $label = 'Normal';
    $badge = 'success';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Edit Dosen</title>
    <?php include '../mhs_listlink.php'; ?>
    <script>
        (function() {
            const theme = localStorage.getItem("theme") || "dark";
            document.documentElement.classList.add(theme + "-mode");
        })();
    </script>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include '../mhs_navbar.php'; ?>
        <?php include '../admin_sidebar.php'; ?>
        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="../images/UP.png" alt="Monev-Skripsi" height="60" width="60">
        </div>

        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Edit Dosen</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="../admin_dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="../admin_dosen">Dosen</a></li>
                                <li class="breadcrumb-item active">Edit</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <a href="../admin_dosen" class="btn btn-warning btn-sm mb-3">
                        <i class="nav-icon fas fa-chevron-left"></i> Kembali
                    </a>
                    <div class="row">

                        <!-- FORM EDIT -->
                        <div class="col-lg-6">
                            <div class="card card-warning">
                                <div class="card-header">
                                    <h3 class="card-title"><i class="fas fa-edit"></i> Form Edit Dosen</h3>
                                </div>

                                <form method="post" action="proses.php">
                                    <div class="card-body">
                                        <input type="hidden" name="action" value="update_dosen">
                                        <input type="hidden" name="nip" value="<?= $dosen['nip']; ?>">

                                        <div class="form-group">
                                            <label>Nama Dosen</label>
                                            <input type="text" name="nama_dosen" class="form-control"
                                                value="<?= htmlspecialchars($dosen['nama_dosen']); ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label>Status Dosen</label>
                                            <select name="aktif" class="form-control" required>
                                                <option value="1" <?= $dosen['aktif'] == 1 ? 'selected' : ''; ?>>Aktif</option>
                                                <option value="0" <?= $dosen['aktif'] == 0 ? 'selected' : ''; ?>>Nonaktif</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>Status User</label>
                                            <select name="status_user" class="form-control" required>
                                                <option value="aktif" <?= $dosen['status_user'] == 'aktif' ? 'selected' : ''; ?>>
                                                    Aktif
                                                </option>
                                                <option value="nonaktif" <?= $dosen['status_user'] == 'nonaktif' ? 'selected' : ''; ?>>
                                                    Nonaktif
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="card-footer">
                                        <button type="submit" name="editmhs" class="btn btn-warning btn-block">
                                            <i class="fas fa-save"></i> Simpan Perubahan
                                        </button>
                                        <a href="../admin_dosen" class="btn btn-secondary btn-block">Batal</a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- INFO BEBAN -->
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h5>Beban Bimbingan</h5>
                                    <h1><?= $beban ?></h1>
                                    <span class="badge badge-<?= $badge ?>"><?= $label ?></span>
                                    <hr>
                                </div>
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