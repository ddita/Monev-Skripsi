<?php
//trigger untuk get value dengan elemen filename
if(isset($_GET['filename'])) {
	//buat variabel $filename untuk menyimpan value dari elemen
	$filename = $_GET['filename'];
	//untuk mengarahkan deirektori dimana file template_dosen disimpan
	$back_dir = "file-import/";
	//untuk mengarahkan file yang dituju untuk di download
	$file = $back_dir.$_GET['filename'];

	//code untuk melakukan download
	if (file_exists($file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: private');
		header('Pragma: private');
		header('Content-Length: '.filesize($file));
		ob_clean();
		flush();
		readfile($file);

		exit;
	} else {

	}
}
?>