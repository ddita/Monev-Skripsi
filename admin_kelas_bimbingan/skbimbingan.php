<?php
require_once '../database/config.php';
require_once '../assets_adminlte/dist/fpdf186/fpdf.php';

class PDF extends FPDF
{
// Page header
	function Header()
	{
		$conn = mysqli_connect('localhost','root', '', 'monev_skripsi');
		$queryconf = mysqli_query($conn, "SELECT * FROM tbl_konfigurasi") or die(mysqli_error($conn));
		if (mysqli_num_rows($queryconf)>0) {
			while($data_conf = mysqli_fetch_array($queryconf)) {
				if ($data_conf['id']==1) {
					$logoapp = $data_conf['lokasi_file'];
				}
				if ($data_conf['id']==5) {
					$nama_univ = $data_conf['elemen'];
				}
			}
		}
    // Logo
		$this->Image($logoapp,15,6,25);
    // Arial bold 15
		$this->SetFont('Times','B',12);
    // Move to the right
		$this->Cell(80);
    // Title
		$this->Cell(30,6,$nama_univ,0,1,'C');
		$this->Cell(80);
		$this->Cell(30,6,'Fakultas Seni Pertunjukan',0,1,'C');
		$this->SetFont('Times','',12);
		$this->Cell(80);
		$this->Cell(30,6,'Jalan Gangnam Style',0,1,'C');
		$this->Cell(80);
		$this->Cell(30,6,'Telp. 08123456789 web: www.engene-u.ac.kr',0,1,'C');
    // Line break
		$this->SetLineWidth(1);
		$this->Line(10, 40, 200, 40);
		$this->Ln(10);
	}

// Page footer
	function Footer()
	{
    // Position at 1.5 cm from bottom
		$this->SetY(-15);
    // Arial italic 8
		$this->SetFont('Times','I',8);
    // Page number
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
	function judul_lampiran(){
		$this->SetFont('Times','',13);
		$this->Cell(80);
		$this->Cell(30,6,'Daftar Nama Mahasiswa Bimbingan Tugas Akhir',0,1,'C');
		$this->Cell(80);
		$this->Cell(30,6,'Semester Ganjil 2024/2025 Engene University',0,1,'C');
		$this->Cell(80);
		$this->Cell(30,6,'Periode 12 September s/d 31 Desember 2024',0,1,'C');
		$this->Ln(15);
	}
}


// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','B',13);
$pdf->Cell(80);
$pdf->Cell(30,6,'SURAT KEPUTUSAN DEKAN',0,1,'C');
$pdf->SetFont('Times','',12);
$pdf->Cell(80);
$pdf->Cell(30,10,'Nomor : 08302002',0,1,'C');
$pdf->Cell(80);
$pdf->Cell(30,6,'Tentang :',0,1,'C');
$pdf->SetFont('Times','B',12);
$pdf->Cell(80);
$pdf->Cell(30,6,'Pengangkatan Dosen Pembimbing Tugas Akhir (DPTA)',0,1,'C');
$pdf->Cell(80);
$pdf->Cell(30,6,'Mahasiswa Engene University',0,1,'C');
$pdf->Cell(80);
$pdf->Cell(30,6,'Semester Ganjil Tahun Periode 2024/2025',0,1,'C');
$pdf->Cell(80);
$pdf->Cell(30,10,'DEKAN FAKULTAS SENI PERTUNJUKAN ENGENE UNIVERSITY',0,1,'C');
$pdf->Ln(5);
$pdf->SetFont('Times','',12);
$pdf->Cell(40,6,'Menimbang',0,0,'L');
$pdf->Cell(5,6,':',0,0,'L');
$pdf->Cell(5,6,'1.',0,0,'L');
$pdf->Cell(5,6,'Bahwa dalam rangka penyelesaian tugas akhir mahasiswa untuk menempuh mata',0,1,'L');
$pdf->Cell(50);
$pdf->Cell(5,6,'kuliah tugas akhir perlu adanya pembimbing tugas akhir Engene University',0,1,'L');
$pdf->Cell(50);
$pdf->Cell(5,6,'semester Ganjil Tahun Periode 2024/2025.',0,1,'L');
$pdf->Cell(45);
$pdf->Cell(5,6,'2.',0,0,'L');
$pdf->Cell(5,6,'Bahwa untuk itu perlu diangkat dosen pembimbing tugas akhir.',0,1,'L');
$pdf->Cell(40,6,'Mengingat',0,0,'L');
$pdf->Cell(5,6,':',0,0,'L');
$pdf->Cell(5,6,'1.',0,0,'L');
$pdf->Cell(5,6,'Peraturan Anggaran Dasar (AD) Engene University.',0,1,'L');
$pdf->Cell(45);
$pdf->Cell(5,6,'2.',0,0,'L');
$pdf->Cell(5,6,'Keputusan rapat pimpinan Rektor Tanggal 30 Agustus 2025.',0,1,'L');
$pdf->SetFont('Times','B',13);
$pdf->Ln(5);
$pdf->Cell(80);
$pdf->Cell(30,6,'MEMUTUSKAN',0,1,'C');
$pdf->Ln(5);
$pdf->SetFont('Times','',12);
$pdf->Cell(40,6,'Menetapkan',0,0,'L');
$pdf->Cell(5,6,':',0,0,'L');
$pdf->Cell(5,6,'1.',0,0,'L');
$pdf->Cell(5,6,'Dosen yang namanya tercantum pada lampiran keputusan ini, diangkat sebagai',0,1,'L');
$pdf->Cell(50);
$pdf->Cell(5,6,'Dosen Pembimbing Tugas Akhir Mahasiswa Engene University, Semester Ganjil',0,1,'L');
$pdf->Cell(50);
$pdf->Cell(5,6,'Tahun Akademik 2024/2025 dengan masa bimbingan tanggal 12 September 2025',0,1,'L');
$pdf->Cell(50);
$pdf->Cell(5,6,'s/d 31 Desember 2024.',0,1,'L');
$pdf->Cell(45);
$pdf->Cell(5,6,'2.',0,0,'L');
$pdf->Cell(5,6,'Sebagaimana mestinya diharapkan yang bersangkutan dapat menjalankan tugas',0,1,'L');
$pdf->Cell(50);
$pdf->Cell(5,6,'dengan sebaik-baiknya dan berhak menerima honorarium sesuai dengan peraturan',0,1,'L');
$pdf->Cell(50);
$pdf->Cell(5,6,'yang berlaku.',0,1,'L');
$pdf->Cell(45);
$pdf->Cell(5,6,'3.',0,0,'L');
$pdf->Cell(5,6,'Surat keputusan ini berlaku sejak ditetapkan dan akan ditinjau kembali serta',0,1,'L');
$pdf->Cell(50);
$pdf->Cell(5,6,'dibenarkan apa bila terdapat kesalahan.',0,1,'L');
$pdf->Cell(100);
$pdf->Cell(30,6,'Ditetapkan di',0,0,'L');
$pdf->Cell(5,6,':',0,0,'L');
$pdf->Cell(5,6,'Gangnam-Gu',0,1,'L');
$pdf->Cell(100);
$pdf->Cell(30,6,'Tanggal',0,0,'L');
$pdf->Cell(5,6,':',0,0,'L');
$pdf->Cell(5,6,'Gangnam-Gu, '.date('d F Y'),0,1,'L');
$pdf->Cell(100);
$pdf->Cell(30,6,'Dekan Fakultas Seni Pertunjukan',0,0,'L');
$pdf->Ln(25);
$pdf->Cell(100);
$pdf->Cell(30,6,'Hwang Dita',0,0,'L');
$pdf->Ln(15);
$pdf->Cell(30,6,'Surat Keputusan ini disampaikan kepada:',0,1,'L');
$pdf->Cell(30,6,'1. Dosen yang bersangkutan.',0,1,'L');
$pdf->Cell(30,6,'2. Bagian Keuangan Engene University.',0,1,'L');

$periodeterpilih = @$_GET['periode'];
$de_periodeterpilih = decriptData($periodeterpilih);
$sql_kelas = mysqli_query($conn, "SELECT nidn FROM tbl_kelas_bimbingan WHERE kode_periode='$de_periodeterpilih' GROUP BY nidn") or die(mysqli_error($conn));
if (mysqli_num_rows($sql_kelas)>0) {
	while($dt_kelas = mysqli_fetch_array($sql_kelas)) {
		$nidn = $dt_kelas['nidn'];
		$sql_dsn = mysqli_query($conn, "SELECT nama FROM tbl_dosen WHERE nidn='$nidn'") or die(mysqli_error($conn));
		$dt_dsn = mysqli_fetch_assoc($sql_dsn);
		$nama_dosen = $dt_dsn['nama'];

		$pdf->AddPage();
		$pdf->judul_lampiran();
		
		$pdf->SetFont('Times','B',12);
		$pdf->Cell(30,6,'Nama Dosen :'.$nama_dosen,0,1,'L');
		$pdf->Cell(10,6,'No',1,0,'C');
		$pdf->Cell(50,6,'Nama Mahasiswa',1,0,'C');
		$pdf->Cell(30,6,'NIM',1,0,'C');
		$pdf->Cell(40,6,'Kontak',1,0,'C');
		$pdf->Cell(40,6,'Jenis Tugas Akhir',1,1,'C');
		
		$pdf->SetFont('Times','',12);
		$no = 1;
		$sql_kelas_dosen = mysqli_query($conn, "SELECT nim FROM tbl_kelas_bimbingan WHERE nidn='$nidn'") or die(mysqli_error($conn));
		while($dt_kelas_dosen = mysqli_fetch_array($sql_kelas_dosen)) {
			$nim = $dt_kelas_dosen['nim'];
			$sql_mhs = mysqli_query($conn, "SELECT nama,kontak FROM tbl_mahasiswa WHERE nim='$nim'") or die(mysqli_error($conn));
			$dt_mhs=mysqli_fetch_assoc($sql_mhs);
			$nama = $dt_mhs['nama'];
			$kontak = $dt_mhs['kontak'];
			
			$pdf->Cell(10,6,$no++,1,0,'C');
			$pdf->Cell(50,6,$nama,1,0,'C');
			$pdf->Cell(30,6,$nim,1,0,'C');
			$pdf->Cell(40,6,$kontak,1,0,'C');
			$pdf->Cell(40,6,'Skripsi',1,1,'C');
		}
	}
}


$pdf->Output();

?>