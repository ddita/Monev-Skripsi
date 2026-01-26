<?php
session_start();
require_once '../database/config.php';

/* ================== CEK LOGIN ADMIN ================== */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login/logout.php");
    exit;
}

/* ================== KONFIG ================== */
date_default_timezone_set('Asia/Jakarta');

/* ================== FUNGSI UTIL ================== */
function decriptData($data)
{
    $key = 'monev_skripsi_2024';
    return openssl_decrypt(base64_decode(urldecode($data)), 'AES-128-ECB', $key);
}

function encriptData($data)
{
    $key = 'monev_skripsi_2024';
    return urlencode(
        base64_encode(
            openssl_encrypt($data, 'AES-128-ECB', $key)
        )
    );
}

function logAktivitas($conn, $ket)
{
    $usr   = $_SESSION['username'] ?? '-';
    $waktu = date('Y-m-d H:i:s');

    $stmt = mysqli_prepare($conn, "INSERT INTO tbl_cross_auth (username, waktu, keterangan) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $usr, $waktu, $ket);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/* ================== ACTION ================== */
$action = $_GET['action'] ?? $_POST['action'] ?? '';

mysqli_begin_transaction($conn);

try {

    /* =====================================================
   ➕ TAMBAH DOSEN
===================================================== */
    if ($action === 'tambah_dosen') {

        $nip        = trim($_POST['nip']);
        $nama_dosen = trim($_POST['nama_dosen']);

        if ($nip === '' || $nama_dosen === '') {
            throw new Exception("NIP dan Nama Dosen wajib diisi");
        }

        /* === CEK DUPLIKAT DOSEN === */
        $cek = mysqli_prepare($conn, "SELECT nip FROM tbl_dosen WHERE nip=?");
        mysqli_stmt_bind_param($cek, "s", $nip);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);

        if (mysqli_stmt_num_rows($cek) > 0) {
            throw new Exception("Dosen dengan NIP $nip sudah terdaftar");
        }
        mysqli_stmt_close($cek);

        /* === INSERT tbl_dosen === */
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO tbl_dosen (nip, nama_dosen, aktif)
     VALUES (?, ?, 1)"
        );
        mysqli_stmt_bind_param($stmt, "ss", $nip, $nama_dosen);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        /* === INSERT tbl_users === */
        $password = sha1($nip);

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO tbl_users (username, password, nama_lengkap, role, status, created_at)
        VALUES (?, ?, ?, 'dosen', 'aktif', NOW())"
        );
        mysqli_stmt_bind_param($stmt, "sss", $nip, $password, $nama_dosen);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        logAktivitas($conn, "Menambahkan dosen $nip");
    }

    /* =====================================================
   ✏️ UPDATE DOSEN (FINAL VERSION)
===================================================== */
    if ($action === 'update_dosen') {

        $nip         = mysqli_real_escape_string($conn, $_POST['nip']);
        $nama_dosen  = mysqli_real_escape_string($conn, trim($_POST['nama_dosen']));
        $status_user = mysqli_real_escape_string($conn, $_POST['status_user']);
        $aktif       = (int) $_POST['aktif']; // ⬅️ PASTI ADA (dari select)

        mysqli_begin_transaction($conn);

        try {

            // 🔹 Update tbl_dosen
            $q1 = mysqli_query($conn, "
            UPDATE tbl_dosen
            SET nama_dosen = '$nama_dosen',
                aktif = '$aktif'
            WHERE nip = '$nip'
        ");
            if (!$q1) {
                throw new Exception(mysqli_error($conn));
            }

            // 🔹 Update tbl_users (sinkron)
            $q2 = mysqli_query($conn, "
            UPDATE tbl_users
            SET nama_lengkap = '$nama_dosen',
                status = '$status_user'
            WHERE username = '$nip'
        ");
            if (!$q2) {
                throw new Exception(mysqli_error($conn));
            }

            mysqli_commit($conn);

            logAktivitas($conn, "Update data dosen NIP $nip");
            header("Location: index.php?status=success");
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            die("❌ Gagal update dosen: " . $e->getMessage());
        }
    }

    /* =====================================================
     🚫 NONAKTIF DOSEN
  ===================================================== */ elseif ($action === 'nonaktif') {

        if (!isset($_GET['nip'])) {
            throw new Exception("NIP tidak ditemukan");
        }

        $nip = decriptData($_GET['nip']);

        if (!$nip) {
            throw new Exception("NIP tidak valid");
        }

        $nip = mysqli_real_escape_string($conn, $nip);

        $q1 = mysqli_query(
            $conn,
            "UPDATE tbl_dosen SET aktif = 0 WHERE nip = '$nip'"
        );

        $q2 = mysqli_query(
            $conn,
            "UPDATE tbl_users SET status = 'nonaktif' WHERE username = '$nip'"
        );

        if (!$q1 || !$q2) {
            throw new Exception(mysqli_error($conn));
        }

        logAktivitas($conn, "Menonaktifkan dosen $nip");
    }

    /* =====================================================
     🗑️ HAPUS DOSEN
  ===================================================== */ elseif ($action === 'hapus') {

        $nip = decriptData($_GET['nip']);

        mysqli_query($conn, "DELETE FROM tbl_users WHERE username='$nip'");
        mysqli_query($conn, "DELETE FROM tbl_dosen WHERE nip='$nip'");

        logAktivitas($conn, "Menghapus dosen $nip");
    } else {
        throw new Exception("Aksi tidak valid");
    }

    mysqli_commit($conn);
    header("Location: ../admin_dosen?status=success");
    exit;
} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: ../admin_dosen?status=error&msg=" . urlencode($e->getMessage()));
    exit;
}
