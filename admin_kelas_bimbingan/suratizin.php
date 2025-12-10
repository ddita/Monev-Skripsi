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
}
$idkelasbimbingan = @$_GET['id_kelas'];
$de_idkelasbimbingan = decriptData($idkelasbimbingan);
$pgl_kelas = mysqli_query($conn, "SELECT nidn,nim FROM tbl_kelas_bimbingan WHERE id_kelas=$de_idkelasbimbingan") or die(mysqli_error($conn));
$dt_kelas = mysqli_fetch_assoc($pgl_kelas);
$nidn = $dt_kelas['nidn'];
$nim = $dt_kelas['nim'];

$sql_mhs = mysqli_query($conn, "SELECT nama,prodi FROM tbl_mahasiswa WHERE nim = $nim") or die(mysqli_error($conn));
$dt_mhs = mysqli_fetch_assoc($sql_mhs);
$nama_mhs = $dt_mhs['nama'];
$kd_prodi = $dt_mhs['prodi'];

$sql_prodi = mysqli_query($conn, "SELECT nama_prodi FROM tbl_prodi WHERE kode_prodi=$kd_prodi") or die(mysqli_error($conn));
$dt_prodi = mysqli_fetch_assoc($sql_prodi);
$nama_prodi = $dt_prodi['nama_prodi'];

$sql_dsn = mysqli_query($conn, "SELECT nama FROM tbl_dosen WHERE nidn = $nidn") or die(mysqli_error($conn));
$dt_dsn = mysqli_fetch_assoc($sql_dsn);
$nama_dsn = $dt_dsn['nama'];

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->Cell(25,6,'Nomor', 0,0,'L');
$pdf->Cell(5,6,':', 0,0,'L');
$pdf->Cell(50,6,'30/08/AGT/2024', 0,1,'L');
$pdf->Cell(25,6,'Hal', 0,0,'L');
$pdf->Cell(5,6,':', 0,0,'L');
$pdf->SetFont('Times','B',12);
$pdf->Cell(50,6,'Permohonan Izin Penelitian', 0,1,'L');
$pdf->Ln(8);
$pdf->Cell(25,6,'Kepada Yth', 0,0,'L');
$pdf->Cell(5,6,':', 0,0,'L');
$pdf->Ln(20);
$pdf->SetFont('Times','I',12);
$pdf->Cell(50,6,"Assalamu'alaikum Wr.Wb", 0,1,'L');
$pdf->Ln(5);
$pdf->SetFont('Times','',12);
$pdf->Cell(50,6,'Diberitahukan dengan hormat, bahwa mahasiswa sebelum mengakhiri pendidikan di Fakultas Seni Pertunjukan', 0,1,'L');
$pdf->Cell(50,6,'Engene University diwajibkan membuat karya ilmiah berupa riset/penelitian. Sehubungan dengan hal itu',0,1,'L');
$pdf->Cell(50,6,'mahasiswa kami :',0,1,'L');
$pdf->Ln(5);
$pdf->Cell(30,6,'NIM', 0,0,'L');
$pdf->Cell(5,6,':', 0,0,'L');
$pdf->Cell(50,6,$nim, 0,1,'L');
$pdf->Cell(30,6,'Nama', 0,0,'L');
$pdf->Cell(5,6,':', 0,0,'L');
$pdf->Cell(50,6,$nama_mhs, 0,1,'L');
$pdf->Cell(30,6,'Program Studi', 0,0,'L');
$pdf->Cell(5,6,':', 0,0,'L');
$pdf->Cell(50,6,$nama_prodi, 0,1,'L');
$pdf->Ln(5);
$pdf->Cell(50,6,'Bermaksud mohon keterangan/data pada instansi/perusahaan yang Bapak/Ibu pimpin untuk keperluan menyusun', 0,1,'L');
$pdf->Cell(50,6,'skripsi dengan judul :', 0,1,'L');
$pdf->Ln(5);
$pdf->SetFont('Times','B','12');
$pdf->Cell(70);
$pdf->Cell(50,6,'SISTEM MONITORING EVALUASI SKRIPSI BERBASIS WEB', 0,1,'C');
$pdf->Ln(5);
$pdf->SetFont('Times','',12);
$pdf->Cell(40,6,'Dosen Pembimbing', 0,0,'L');
$pdf->Cell(5,6,':', 0,0,'L');
$pdf->Cell(50,6,$nama_dsn, 0,1,'L');
$pdf->Ln(5);
$pdf->Cell(50,6,'Hasil karya ilmiah tersebut semata-mata bersifat dan bertujuan keilmuan dan tidak disajikan pada pihak luar', 0,1,'L');
$pdf->Cell(50,6,'karena itu kami mohon perkenan Bapak/Ibu untuk dapat memberikan data/keterangan yang diperlukan oleh', 0,1,'L');
$pdf->Cell(50,6,'mahasiswa tersebut', 0,1,'L');
$pdf->Ln(5);
$pdf->Cell(50,6,'Atas perhatian dan bantuan Bapak/Ibu, kami ucapkan terimakasih.', 0,1,'L');
$pdf->Ln(5);
$pdf->SetFont('Times','I',12);
$pdf->Cell(50,6,"Wassalamu'alaikum Wr.Wb", 0,1,'L');
$pdf->Ln(10);
$pdf->SetFont('Times','',12);
$pdf->Cell(120);
$pdf->Cell(30,6,'Tanjung, '.date('d F Y'),0,1,'L');
$pdf->Cell(120);
$pdf->Cell(50,6,'Dekan,', 0,1,'L');
$pdf->Ln(20);
$pdf->Cell(120);
$pdf->Cell(50,6,'Pudjono', 0,1,'L');
$pdf->SetFont('Times','B',12);
$pdf->Cell(120);
$pdf->Cell(50,6,'NUPN : 142536348', 0,1,'L');

$pdf->Output();

?>