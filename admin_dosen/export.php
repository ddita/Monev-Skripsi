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
$pdf->Cell(10,6,'No',1,0,'C');
$pdf->Cell(30,6,'NIDN',1,0,'C');
$pdf->Cell(50,6,'Nama',1,0,'C');
$pdf->Cell(50,6,'Email',1,0,'C');
$pdf->Cell(30,6,'Kontak',1,0,'C');
$pdf->Cell(25,6,'Status',1,1,'C');

$no=1;
$query_dosen = mysqli_query($conn, "SELECT * FROM tbl_dosen") or die(mysqli_error($conn));
if(mysqli_num_rows($query_dosen)>1) {
	while($dt_dosen = mysqli_fetch_array($query_dosen)) {
		$stts_dosen = $dt_dosen['status'];
		if ($stts_dosen==1) {
			$stts = 'Aktif';
		} else {
			$stts = 'Non-Aktif';
		}

		$pdf->SetFont('Times','',12);
		$pdf->Cell(10,6,$no++,1,0,'C');
		$pdf->Cell(30,6,$dt_dosen['nidn'],1,0,'C');
		$pdf->Cell(50,6,$dt_dosen['nama'],1,0,'C');
		$pdf->Cell(50,6,$dt_dosen['email'],1,0,'C');
		$pdf->Cell(30,6,$dt_dosen['kontak'],1,0,'C');
		$pdf->Cell(25,6,$stts,1,1,'C');
	}
}

$pdf->Output();

?>