<?php
date_default_timezone_set('Asia/Jakarta');
$conn = mysqli_connect('localhost','root', '', 'monev_skripsi');

if(!$conn) {
	"Database tidak terkoneksi";
}

function encriptData($kk) {
	$hh = base64_encode($kk);
	$aa = str_replace('=', '', $hh);

	$panjang_karakter = strlen($aa);

	$pelengkap_ganjil = '123';

	if($panjang_karakter % 2 === 0) {
                      // genap
		$pp           = $panjang_karakter;
		$bagi_2       = $pp / 2;
		$bagian_pecah = $aa;
	} else{
                      // ganjil
		$mm           = $aa . $pelengkap_ganjil;
		$pp           = strlen($mm);
		$bagi_2       = $pp / 2;
		$bagian_pecah = $mm;
	}

	$bagian_awal    = substr($bagian_pecah, 0, -$bagi_2);
	$bagian_tengah  = rand(1000, 9999);
	$bagian_akhir   = substr($bagian_pecah, $bagi_2);

	$panjang_al = strlen($bagian_awal);
	$panjang_ak = sprintf('%05d', $panjang_al);

	$hasil_encode = $bagian_awal . $bagian_tengah . $bagian_akhir . $panjang_ak;

                      // tambahan encode
	$encode_akhir = base64_encode($hasil_encode);
	$encode_akhir = str_replace('=', '', $encode_akhir);

	return $encode_akhir;
}

function decriptData($vale) {
	$val = base64_decode($vale);
                      // VmlyZ45832k12300005

	$jl             = substr($val, -5);
                      // 00005

	$int_data       = (int)$jl;
                      // 5

	$kata           = substr($val, 0, -5);
                      // VmlyZ45832k123

	$bagian_awal    = substr($kata, 0, $int_data);
                      // VmlyZ

	$bagian_akhir   = substr($kata, -$int_data);
                      // 2k123

	$gabungan       = $bagian_awal . $bagian_akhir;

	$mn             = str_replace('123', '', $gabungan);
	$hasil_decript  = base64_decode($mn);

	return $hasil_decript;
}

$value = 'Delynn';

$encript_data = encriptData($value);
$decript_data = decriptData($encript_data);

// echo $value;
// echo '<hr>';
// echo $encript_data;
// echo '<hr>';
// echo $decript_data;
                    // function encriptData($kk) {
                    //   // $hh = $kk;
                    //   $hh = base64_encode($kk);
                    //   $aa = str_replace('=', '', $kk);
                    //   // return $aa;
                    //   $panjang_karakter = strlen($aa);
                    //   $rand = '123';

                    //   if($panjang_karakter % 2 === 0) {
                    //     //genap
                    //     $pp = $panjang_karakter;
                    //     $bagi_2 = $pp/2;
                    //   } else {
                    //     //ganjil
                    //     $mm = $aa . $rand;
                    //     $pp = strlen($mm);
                    //     $bagi_2 = $pp/2;
                    //   }

                    //   $bagian_awal = substr($aa, 0, -$bagi_2);
                    //   $bagian_tengah = '308';
                    //   $bagian_akhir = substr($aa, -$bagi_2);

                    //   $hasil_encode = $bagian_awal . $bagian_tengah . $bagian_akhir;
                    //   return $hasil_encode;
                    // }

                    // // function decriptData($uu) {
                    // //   $hasil = base64_decode($uu);
                    // //   return $hasil;
                    // // }

                    // $value = 'Dita';
                    // $data_encript = encriptData($value);

                    // echo $data_encript;

                    // // echo 'Variable: '.$value;
                    // // echo '<hr>';
                    // // echo 'Encode: '.$data_encript;
                    // // echo '<hr>';
                    // // echo 'Decode: '.decriptData($data_encript);
?>