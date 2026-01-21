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
		$this->Cell(30,6,'Faculty of Performing Arts',0,1,'C');
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
}

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

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','B',12);
$pdf->Cell(150);
$pdf->Cell(30,6,'Tanjung, '.date('d F Y'),0,1,'C');
$pdf->Ln(5);
$pdf->Cell(80);
$pdf->Cell(30,6,'DATA DOSEN',0,1,'C');
$pdf->Cell(80);
$pdf->Cell(30,6,$nama_univ,0,1,'C');
$pdf->Cell(80);
$pdf->Cell(30,6,'TAHUN PERIODE 2025/2026',0,1,'C');
$pdf->Ln(5);
$pdf->Cell(8,6,'No',1,0,'C');
$pdf->Cell(25,6,'NIM',1,0,'C');
$pdf->Cell(40,6,'Nama',1,0,'C');
$pdf->Cell(50,6,'Prodi',1,0,'C');
$pdf->Cell(20,6,'Status',1,0,'C');
$pdf->Cell(20,6,'Angkatan',1,0,'C');
$pdf->Cell(28,6,'Kontak',1,0,'C');
$pdf->Cell(15,6,'Kelamin',1,1,'C');

$no=1;
$query_mhs = mysqli_query($conn, "SELECT * FROM tbl_mahasiswa") or die(mysqli_error($conn));
if(mysqli_num_rows($query_mhs)>1) {
	while($dt_mhs = mysqli_fetch_array($query_mhs)) {
		$stts = $dt_mhs['status'];
		$query_stts = mysqli_query($conn, "SELECT * FROM tbl_status WHERE id='$stts'") or die(mysqli_error($conn));
		$stt_mhs = mysqli_fetch_assoc($query_stts);
		$status = $stt_mhs['status'];

		$kode_prodi = $dt_mhs['prodi'];
		$query_prodi = mysqli_query($conn, "SELECT * FROM tbl_prodi WHERE kode_prodi='$kode_prodi'") or die(mysqli_error($conn));
		if (mysqli_num_rows($query_prodi)>0) {
			$prodi = mysqli_fetch_assoc($query_prodi);
			$nama_prodi = $prodi['nama_prodi'];
		}

		$kode_angkatan = $dt_mhs['angkatan'];
		$query_akt = mysqli_query($conn, "SELECT * FROM tbl_angkatan WHERE kode_angkatan='$kode_angkatan'") or die(mysqli_error($conn));
		if (mysqli_num_rows($query_akt)>0) {
			$angkatan = mysqli_fetch_assoc($query_akt);
			$ket_angkatan = $angkatan['keterangan'];
		}


		$pdf->SetFont('Times','',12);
		$pdf->Cell(8,6,$no++,1,0,'C');
		$pdf->Cell(25,6,$dt_mhs['nim'],1,0,'C');
		$pdf->Cell(40,6,$dt_mhs['nama'],1,0,'C');
		$pdf->Cell(50,6,$nama_prodi,1,0,'C');
		$pdf->Cell(20,6,$status,1,0,'C');
		$pdf->Cell(20,6,$ket_angkatan,1,0,'C');
		$pdf->Cell(28,6,$dt_mhs['kontak'],1,0,'C');
		$pdf->Cell(15,6,$dt_mhs['kelamin'],1,1,'C');
	}
}

$pdf->Output();

?>